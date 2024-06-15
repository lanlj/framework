<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/11
 * Time: 13:54
 */

namespace lanlj\fw\ctr;

use lanlj\fw\app\Application;
use lanlj\fw\auth\Authorization;
use lanlj\fw\auth\po\{Account, Token};
use lanlj\fw\core\{Arrays, Strings};
use lanlj\fw\http\storage\{Cookie, Session};
use lanlj\fw\repo\{Repository, TokenRepo};
use lanlj\fw\util\{BooleanUtil, JsonUtil, Utils};
use stdClass;
use function ezsql\functions\{eq, orderBy, where};

abstract class AuthController extends CommController
{
    /**
     * @var Session
     */
    protected Session $session;

    /**
     * @var string
     */
    protected string $authName = "auth";

    /**
     * @var string
     */
    protected string $isCookieName = "isCookie";

    /**
     * @var string
     */
    protected string $cookiePath = "";

    /**
     * @var TokenRepo
     */
    protected TokenRepo $tokenRepo;

    /**
     * 保存授权
     * @param Authorization $authorization
     * @param bool $cookie
     * @return bool
     */
    protected final function saveAuthorization(Authorization $authorization, bool $cookie = false): bool
    {
        $token = $authorization->getToken();
        $accountId = $token->getAccount()->getId();
        $rst = $this->tokenRepo->select(
            'id, token, expires', null,
            where(eq('account_id', $accountId)), orderBy('expires', 'DESC')
        );
        $bool = false;
        if (!is_null($rst) && $rst->expires - time() > 0) {
            $bool = true;
            $token->setId($rst->id);
            $token->setToken($rst->token);
            $token->setExpires($rst->expires);
        }
        $this->session->setAttribute($this->authName, $authorization);
        if ($cookie) {
            $class = new stdClass();
            $class->account = $accountId;
            $class->token = $token->getToken();
            $ci = 'bGFubG' . str_replace('=', '', base64_encode(JsonUtil::toJsonString($class))) . 'o5OA==';
            $cookie = new Cookie($this->authName, $ci);
            $cookie->setExpire($token->getExpires());
            $cookie->setPath($this->cookiePath);
            $this->resp->addCookie($cookie);
            if (!$bool) {
                return $this->tokenRepo->insert(new Token(null, $class->token, new Account($class->account), $token->getExpires()));
            }
        }
        return true;
    }

    /**
     * 移除授权
     */
    protected final function removeAuthorization(): void
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
    protected final function isAuthorization(): void
    {
        if (!$this->getAuthorization()->isAuth()) die('Unauthorized.');
    }

    /**
     * 获得授权对象
     * @param bool $origin
     * @return Authorization|stdClass
     */
    protected final function getAuthorization(bool $origin = false)
    {
        $authorization = $this->session->getAttribute($this->authName);
        if (is_null($authorization)) {
            $authorization = new Authorization();
            $cookie = $this->req->getCookie($this->authName);
            if (!Utils::isEmpty($cookie)) {
                $str = (new Strings($cookie))
                    ->replaceFirst('/bGFubG/', '')
                    ->replaceLast('/o5OA==/', '')->getString();
                $tk = new Arrays(JsonUtil::toJson(base64_decode($str), true));
                $rst = $this->tokenRepo->select('*', null, where(eq('token', $tk->get('token'))));
                if (!is_null($rst)) {
                    if ($rst->account_id == $tk->get('account') && $rst->expires - time() > 0) {
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
    protected function init(): void
    {
        parent::init();
        $this->session = $this->req->getSession();
        date_default_timezone_set("Asia/Shanghai");
        Repository::initialize(Application::getInstance()->getDB()->getDBO());
        $this->tokenRepo = new TokenRepo();
    }
}