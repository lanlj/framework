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
use lanlj\fw\http\{Request, Response};
use lanlj\fw\util\{BeanUtil, JsonUtil, UrlUtil, Utils, XMLUtil};

class Application implements BeanInstance
{
    /**
     * @var string 配置文件路径
     */
    public static string $configPath = "./src/resources/application.xml";

    /**
     * @var Arrays
     */
    protected static Arrays $sysConfig;

    /**
     * @var Arrays
     */
    protected static Arrays $properties;

    /**
     * @var bool
     */
    private static bool $_started = false;

    /**
     * @var Application
     */
    private static ?self $_instance = null;

    /**
     * @var Request
     */
    private static ?Request $_request = null;

    /**
     * @var Response
     */
    private static ?Response $_response = null;

    /**
     * @var string
     */
    private ?string $appClass;

    /**
     * Application constructor.
     */
    protected function __construct()
    {
        if (!self::$_started) {
            $config = [];
            if (is_file(self::$configPath))
                $config = XMLUtil::toArray(file_get_contents(self::$configPath));
            $appConfig = new Arrays($config);
            self::$sysConfig = new Arrays($appConfig->get("sys", []));
            self::$properties = new Arrays($appConfig->get("props", []));
            $this->appClass = $appConfig->get("@attributes")["class"];

            $filters = self::$sysConfig->get("filters", []);
            $filters = $this->arrPackage(Utils::getDefault($filters, "filter"));
            self::$sysConfig->add($filters, "filters");

            $propFiles = self::$sysConfig->get("prop-files", []);
            $propFiles = $this->arrPackage(Utils::getDefault($propFiles, "prop-file"));
            self::$sysConfig->add($propFiles, "prop-files");

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
                            $props = JsonUtil::toJson($props, true);
                            break;
                    }
                    self::$properties->addAll($props);
                }
            }

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
     * @param array $arr
     * @return array
     */
    protected function arrPackage(array $arr): array
    {
        $len = count($arr);
        if ($len === 0) return [];
        if ((new Arrays($arr))->getKeys()
            ->contains("@attributes", true)
        ) return [$arr];
        return $arr;
    }

    /**
     * Return an array of Filter. (Filter[])
     * @return array
     */
    protected function getFilters(): array
    {
        $_filters = [];
        $filters = self::$sysConfig->get("filters");

        $contextPath = self::$sysConfig->get("context-path", "/");
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
    public function getRequestPath(): string
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
    public function getProperty(string $name, $default = null)
    {
        return self::$properties->get($name, $default);
    }

    /**
     * @return Request
     */
    public static function getRequest(): Request
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
    public function setProperty(string $name, $value)
    {
        self::$properties->add($value, $name);
    }

    /**
     * @param string|array $paths
     * @param string $reqPath
     * @return bool
     */
    protected function pathMatches($paths, string $reqPath): bool
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
    public static function getResponse(): Response
    {
        if (is_null(self::$_response) || !isset(self::$_response)) {
            self::$_response = new Response();
        }
        return self::$_response;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
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
    public static function newInstance(...$_): self
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * @return DB
     */
    public function getDB(): ?DB
    {
        $sql = self::$sysConfig->get("sql");
        $attrs = Utils::getDefault($sql, "@attributes");
        $db = BeanUtil::populate($sql, $attrs["class"]);
        return $db instanceof DB ? $db : NULL;
    }

    /**
     * @return Arrays
     */
    public function getProperties(): Arrays
    {
        return self::$properties;
    }
}