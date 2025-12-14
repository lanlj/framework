<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/12/9
 * Time: 17:30
 */

namespace lanlj\fw\app;

use lanlj\fw\base\{Arrays, Strings};
use lanlj\fw\bean\BeanInstance;
use lanlj\fw\db\DB;
use lanlj\fw\filter\Filter;
use lanlj\fw\http\{Request, Response, url\Url};
use lanlj\fw\proxy\SqlLogProxy;
use lanlj\fw\util\{BeanUtil, JsonUtil, UrlUtil, Utils, XMLUtil};

class Application implements BeanInstance
{
    /**
     * @var string 配置文件路径
     */
    protected static string $configPath = "./src/resources/application.xml";

    /**
     * @var Arrays
     */
    protected static Arrays $sysConfig;

    /**
     * @var Arrays
     */
    protected static Arrays $properties;

    /**
     * @var DB|null
     */
    private static ?DB $_db;

    /**
     * @var bool
     */
    private static bool $_started = false;

    /**
     * @var Application
     */
    private static self $_instance;

    /**
     * @var Request
     */
    private static Request $_request;

    /**
     * @var Response
     */
    private static Response $_response;

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
            $properties = $appConfig->get("props", []);
            !isset(self::$properties) ? self::$properties = new Arrays($properties) : self::$properties->addAll($properties);
            $this->setProperty("appClass", $appConfig->get("@attributes")["class"]);

            $filters = self::$sysConfig->get("filters", []);
            $filters = $this->arrPackage(Utils::getDefault($filters, "filter", []));
            self::$sysConfig->add($filters, "filters");

            $propFiles = self::$sysConfig->get("prop-files", []);
            $propFiles = $this->arrPackage(Utils::getDefault($propFiles, "prop-file", []));
            self::$sysConfig->add($propFiles, "prop-files");

            foreach ($propFiles as $propFile) {
                $attrs = Utils::getDefault($propFile, "@attributes", []);
                if (is_file($filename = $attrs["path"])) {
                    $props = file_get_contents($filename);
                    $type = $attrs["type"];
                    switch ($type) {
                        case "xml":
                            $props = XMLUtil::toArray($props);
                            break;
                        case "json":
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

            $this->initSqlLogProxy($this->getDB());

            self::$_started = true;
        }
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function setProperty(string $name, $value): self
    {
        self::$properties->add($value, $name);
        return $this;
    }

    /**
     * @param array $arr
     * @return array
     */
    protected function arrPackage(array $arr): array
    {
        if (count($arr) == 0) return [];
        if ((new Arrays($arr))->getKeys()
            ->contains("@attributes")
        ) return [$arr];
        return $arr;
    }

    /**
     * Return an array of Filter.
     * @return Filter[]
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
     * @param bool $save
     * @return string
     */
    public function getRequestPath(bool $save = true): string
    {
        $reqPath = '';
        if ($save && !is_null($reqPath = $this->getProperty("requestPath"))) return $reqPath;

        $reqUrl = new Url(self::getRequest()->getRequestURL());
        $path = $reqUrl->get(Url::PATH);
        $query = '';

        $includeQuery = $this->getProperty("requestPathIncludeQuery", false);
        $reqQuery = $reqUrl->get(Url::QUERY);
        if ($includeQuery) $query = in_array($reqQuery, [null, '']) ? '' : "?$reqQuery";

        $fixReqPath = $this->getProperty("fixRequestPath", true);
        if (!$save || $fixReqPath) $reqPath = UrlUtil::fixPath($path) . $query;
        if ($save && is_null($this->getProperty("requestPath"))) $this->setProperty("requestPath", $reqPath = $path . $query);

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
        return self::$_request ?? self::$_request = new Request();
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
        return self::$_response ?? self::$_response = new Response();
    }

    /**
     * 初始化SQL日志代理
     * @param DB|null $db
     * @return bool
     */
    protected function initSqlLogProxy(?DB $db): bool
    {
        if (!is_null($db) && !is_null($db->getDBO()) && !(new Strings($db->getLogFile()))->trim()->isEmpty()) {
            $sql = self::$sysConfig->get("sql");
            $attrs = Utils::getDefault($sql, "@attributes");
            $proxy = BeanUtil::newInstance($attrs["log-class"]);
            $db->initProxyDBO($proxy instanceof SqlLogProxy ? $proxy : NULL);
            return true;
        }
        return false;
    }

    /**
     * @return DB
     */
    public function getDB(): ?DB
    {
        if (isset(self::$_db)) return self::$_db;
        $sql = self::$sysConfig->get("sql", []);
        $attrs = Utils::getDefault($sql, "@attributes");
        $db = BeanUtil::populate($sql, $attrs["class"]);
        return self::$_db = $db instanceof DB ? $db->setLogFile($attrs["log-file"]) : NULL;
    }

    /**
     * 设置是否修复请求路径(剔除请求路径中多余的斜线)
     * @param bool $fix
     * @return void
     */
    public static function setFixRequestPath(bool $fix = true): void
    {
        if (!isset(self::$properties)) self::$properties = new Arrays();
        self::$properties->add($fix, "fixRequestPath");
    }

    /**
     * 设置请求路径是否包含查询
     * @param bool $includeQuery
     * @return void
     */
    public static function setRequestPathIncludeQuery(bool $includeQuery = false): void
    {
        if (!isset(self::$properties)) self::$properties = new Arrays();
        self::$properties->add($includeQuery, "requestPathIncludeQuery");
    }

    /**
     * @param string $configPath
     * @return void
     */
    public static function setConfigPath(string $configPath): void
    {
        self::$configPath = $configPath;
    }

    /**
     * @return self
     */
    public static function getInstance(): self
    {
        if (!isset(self::$_instance)) {
            $instance = new self();
            if (is_subclass_of($appClass = $instance->getProperty("appClass"), self::class)) {
                $instance = call_user_func(array($appClass, "newInstance"));
            }
            self::$_instance = $instance;
        }
        return self::$_instance;
    }

    /**
     * @param mixed ...$args
     * @return self
     */
    public static function newInstance(...$args): self
    {
        return self::$_instance ?? self::$_instance = new static();
    }

    /**
     * @return Arrays
     */
    public function getProperties(): Arrays
    {
        return self::$properties;
    }
}