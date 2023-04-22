<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 17:30
 */

namespace lanlj\fw\app;

use lanlj\fw\bean\BeanInstance;
use lanlj\fw\core\Arrays;
use lanlj\fw\db\DB;
use lanlj\fw\filter\Filter;
use lanlj\fw\http\Request;
use lanlj\fw\http\Response;
use lanlj\fw\json\Json;
use lanlj\fw\util\BeanUtil;
use lanlj\fw\util\UrlUtil;
use lanlj\fw\util\Utils;
use lanlj\fw\util\XMLUtil;

class Application implements BeanInstance
{
    /**
     * @var bool
     */
    protected static $_started = false;

    /**
     * @var Application
     */
    private static $_instance = null;

    /**
     * @var Request
     */
    private static $_request = null;

    /**
     * @var Response
     */
    private static $_response = null;

    /**
     * @var Arrays
     */
    protected $sysConfig;

    /**
     * @var Arrays
     */
    protected $properties;

    /**
     * @var string
     */
    private $appClass;

    /**
     * Application constructor.
     */
    protected function __construct()
    {
        $config = [];
        if (is_file($filename = "./src/resources/application.xml"))
            $config = XMLUtil::toArray(file_get_contents($filename));
        $appConfig = new Arrays($config);
        $this->sysConfig = new Arrays($appConfig->get("sys", []));
        $this->properties = new Arrays($appConfig->get("props", []));
        $this->appClass = $appConfig->get("@attributes")["class"];

        $filters = $this->sysConfig->get("filters", []);
        $filters = $this->arrPackage(Utils::getDefault($filters, "filter"));
        $this->sysConfig->add($filters, "filters");

        $propFiles = $this->sysConfig->get("prop-files", []);
        $propFiles = $this->arrPackage(Utils::getDefault($propFiles, "prop-file"));
        $this->sysConfig->add($propFiles, "prop-files");

        foreach ($propFiles as $propFile) {
            $attrs = Utils::getDefault($propFile, "@attributes");
            if (is_file($filename = $attrs["path"])) {
                $props = file_get_contents($filename);
                $type = $attrs["type"];
                switch ($type) {
                    case "xml":
                        $props = XMLUtil::toArray($props);
                        break;
                    default:
                        $props = Json::toJson($props, true);
                        break;
                }
                $this->properties->addAll($props);
            }
        }
    }

    /**
     * @param array $arr
     * @return array
     */
    protected function arrPackage($arr)
    {
        $len = count($arr);
        if ($len === 0) return [];
        if ((new Arrays($arr))->getKeys()
            ->contains("@attributes", true)
        ) return [$arr];
        return $arr;
    }

    /**
     * @return Application
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            $instance = new static();
            if (is_subclass_of($instance->appClass, self::class)) {
                $instance = call_user_func(array($instance->appClass, "newInstance"));
            }
            self::$_instance = $instance;
        }
        return self::$_instance;
    }

    /**
     * @param array ...$_
     * @return self
     */
    public static function newInstance(...$_)
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    /**
     * @return DB
     */
    public function getDB()
    {
        $sql = $this->sysConfig->get("sql");
        $attrs = Utils::getDefault($sql, "@attributes");
        $db = BeanUtil::populate($sql, $attrs["class"]);
        return $db instanceof DB ? $db : NULL;
    }

    /**
     * 启动程序
     */
    public function startup()
    {
        if (!self::$_started) {
            $filters = $this->getFilters();
            $request = self::getRequest();
            $response = self::getResponse();
            foreach ($filters as $filter) {
                $filter->doFilter($request, $response);
            }
            self::$_started = true;
        }
    }

    /**
     * Return an array of Filter. (Filter[])
     * @return array
     */
    protected function getFilters()
    {
        $_filters = [];
        $filters = $this->sysConfig->get("filters");

        $contextPath = $this->sysConfig->get("context-path", "/");
        $reqPath = preg_replace("~$contextPath~", "", $this->getRequestPath(), 1);

        foreach ($filters as $filter) {
            $urlPattern = Utils::getDefault($filter, "url-pattern");
            if (is_null($urlPattern)) continue;
            if ($this->pathMatches($urlPattern, $reqPath)) {
                $attrs = Utils::getDefault($filter, "@attributes");
                $filter = BeanUtil::populate($filter, $attrs["class"]);
                if ($filter instanceof Filter) $_filters[] = $filter;
            }
        }
        return $_filters;
    }

    /**
     * @return string
     */
    public function getRequestPath()
    {
        if (is_null($reqPath = $this->getProperty("requestPath"))) {
            $reqPath = UrlUtil::fixPath(parse_url(self::getRequest()->getRequestURL(), PHP_URL_PATH));
            $this->setProperty("requestPath", $reqPath);
        }
        return $reqPath;
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getProperty($name, $default = null)
    {
        return $this->properties->get($name, $default);
    }

    /**
     * @return Request
     */
    public static function getRequest()
    {
        if (is_null(self::$_request) || !isset(self::$_request)) {
            self::$_request = new Request();
        }
        return self::$_request;
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function setProperty($name, $value)
    {
        $this->properties->add($value, $name);
    }

    /**
     * @param string|array $paths
     * @param string $reqPath
     * @return bool
     */
    protected function pathMatches($paths, $reqPath)
    {
        if (!is_array($paths)) $paths = [$paths];
        foreach ($paths as $path) {
            if ($path == $reqPath || @preg_match($path, $reqPath, $matches)) {
                if (empty($matches) || $matches[0] == $reqPath) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * @return Response
     */
    public static function getResponse()
    {
        if (is_null(self::$_response) || !isset(self::$_response)) {
            self::$_response = new Response();
        }
        return self::$_response;
    }

    /**
     * @return Arrays
     */
    public function getProperties()
    {
        return $this->properties;
    }
}