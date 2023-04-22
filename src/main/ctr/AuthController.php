<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 13:54
 */

namespace lanlj\fw\ctr;

use ezSQLcore;
use lanlj\fw\auth\Authorization;
use lanlj\fw\auth\Token;
use lanlj\fw\core\Arrays;
use lanlj\fw\core\Strings;
use lanlj\fw\http\storage\Cookie;
use lanlj\fw\http\storage\Session;
use lanlj\fw\json\Json;
use lanlj\fw\util\BooleanUtil;
use lanlj\fw\util\Utils;
use stdClass;

abstract class AuthController extends CommController
{
    /**
     * @var Session
     */
    protected $session;

    /**
     * @var ezSQLcore
     */
    protected $dbo;

    /**
     * @var string
     */
    protected $authName = "auth";

    /**
     * @var string
     */
    protected $isCookieName = "isCookie";

    /**
     * @var string
     */
    private $cookiePath = "";

    /**
     * @param string $cookiePath
     */
    protected function setCookiePath($cookiePath)
    {
        $this->cookiePath = $cookiePath;
    }

    /**
     * 保存授权
     * @param Authorization $authorization
     * @param bool $cookie
     * @return bool
     */
    protected final function saveAuthorization(Authorization $authorization, $cookie = false)
    {
        $token = $authorization->getToken();
        $account = $token->getAccount()->getId();
        $sql = "SELECT id, expires FROM lj_token WHERE account = '%s' ORDER BY expires DESC LIMIT 0, 1;";
        $rst = $this->dbo->get_row(sprintf($sql, $account));
        $bool = false;
        if (!is_null($rst) && $rst->expires - time() > 0) {
            $bool = true;
            $token->setId($rst->id);
            $token->setExpires($rst->expires);
        }
        $this->session->setAttribute($this->authName, $authorization);
        if ($cookie) {
            $class = new stdClass();
            $class->account = $account;
            $class->token = $token->getId();
            $ci = 'bGFubG' . str_replace('=', '', base64_encode(Json::toJsonString($class))) . 'o5OA==';
            $cookie = new Cookie($this->authName, $ci);
            $cookie->setExpire($token->getExpires());
            $cookie->setPath($this->cookiePath);
            $this->resp->addCookie($cookie);
            if (!$bool) {
                $sql = "INSERT INTO lj_token VALUES('%s', '%s', '%s');";
                return $this->dbo->query(sprintf($sql, $class->token, $class->account, $token->getExpires()));
            }
        }
        return true;
    }

    /**
     * 移除授权
     */
    protected final function removeAuthorization()
    {
        $this->session->removeAttribute($this->authName);
        $this->session->removeAttribute($this->isCookieName);
        $cookie = new Cookie($this->authName, null);
        $cookie->setPath($this->cookiePath);
        $this->req->removeCookie(null, $cookie);
    }

    /**
     * 是否授权
     */
    protected final function isAuthorization()
    {
        if (!$this->getAuthorization()->isAuth())
            die('Unauthorized.');
    }

    /**
     * 获得授权对象
     * @param bool $origin
     * @return Authorization|stdClass
     */
    protected final function getAuthorization($origin = false)
    {
        $authorization = $this->session->getAttribute($this->authName);
        if (is_null($authorization)) {
            $authorization = new Authorization();
            $cookie = $this->req->getCookie($this->authName);
            if (!Utils::isEmpty($cookie)) {
                $str = (new Strings($cookie))
                    ->replaceFirst('/bGFubG/', '')
                    ->replaceLast('/o5OA==/', '')->getString();
                $tk = new Arrays(Json::toJson(base64_decode($str), true));
                $rst = $this->dbo->get_row(sprintf("SELECT * FROM lj_token WHERE id = '%s';", $tk->get('token')));
                if (!is_null($rst)) {
                    if ($rst->account == $tk->get('account') && $rst->expires - time() > 0) {
                        $authorization->setToken(Token::mapping($rst));
                        $this->session->setAttribute($this->isCookieName, true);
                        $this->session->setAttribute($this->authName, $authorization);
                    }
                }
            }
        }
        if ($origin) {
            $class = new stdClass();
            $isCookieName = $this->isCookieName;
            $class->$isCookieName = BooleanUtil::toBool($this->session->getAttribute($isCookieName));
            $class->authorization = $authorization;
            return $class;
        }
        return $authorization;
    }

    /**
     * 初始化配置
     * @return void
     */
    protected function init()
    {
        parent::init();
        $this->session = Session::getInstance();
        date_default_timezone_set("Asia/Shanghai");
    }

    /**
     * @param ezSQLcore $dbo
     */
    protected function setDBO(ezSQLcore $dbo)
    {
        $this->dbo = $dbo;
        $this->dbo->setPrepare();
    }
}