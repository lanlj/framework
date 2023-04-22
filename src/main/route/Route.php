<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 13:23
 */

namespace lanlj\fw\route;

use lanlj\fw\app\Application;
use lanlj\fw\core\Arrays;
use lanlj\fw\ctr\Controller;
use lanlj\fw\http\Request;
use lanlj\fw\route\exception\HttpError;
use lanlj\fw\util\ArrayUtil;

class Route
{
    /**
     * 对象实例
     * @var Route
     */
    private static $_instance = null;

    /**
     * 路由表
     * @var Arrays
     */
    protected $route;

    /**
     * 请求Path
     * @var string
     */
    protected $reqPath;

    /**
     * Route constructor.
     */
    protected function __construct()
    {
        $this->init();
    }

    /**
     * 初始化配置
     */
    protected function init()
    {
        ob_start(); // 开启缓冲区

        $app = Application::getInstance();
        $app->startup();

        $httpErrs = $this->getDefaultHttpErrs();

        // 检测访问路径是不是PHP_SELF
        $this->reqPath = $app->getRequestPath();
        if ($_SERVER['PHP_SELF'] === $this->reqPath) {
            $_403 = HttpError::mapping($httpErrs->get(403));
            header($_403->getErrHeader());
            die($_403->getErrMessage());
        }

        $this->route = new Arrays(); // 初始化路由
    }

    /**
     * 默认HTTP错误列表
     * @return Arrays
     */
    protected function getDefaultHttpErrs()
    {
        return new Arrays([
            403 => new HttpError(
                'HTTP/1.1 403 Forbidden',
                '<html lang="en"><title>403 Forbidden</title><body>403 Forbidden</body></html>'
            ),
            404 => new HttpError(
                'HTTP/1.1 404 Not Found',
                '<html lang="en"><title>404 Not Found</title><body>404 Not Found</body></html>'
            ),
            500 => new HttpError(
                'HTTP/1.1 500 Internal Server Error',
                '<html lang="en"><title>500 Internal Server Error</title><body>500 Internal Server Error</body></html>'
            )
        ]);
    }

    /**
     * 获取路由实例
     * @return Route
     */
    public static final function getInstance()
    {
        if (is_null(self::$_instance) || !isset(self::$_instance)) {
            self::$_instance = new static();
        }
        return self::$_instance;
    }

    /**
     * @param array|object $route
     * @return $this
     */
    public function setRoute($route)
    {
        $this->route->addAll(ArrayUtil::toArray($route, false, true));
        $httpErrs = $this->getDefaultHttpErrs();
        $httpErrs->addAll($this->route->get('httpErrs', []));
        $this->route->add($httpErrs->getArray(), 'httpErrs');
        return $this;
    }

    /**
     * @param string $baseDir
     * @return $this
     */
    public function setBaseDir($baseDir)
    {
        $this->route->add($baseDir, 'baseDir');
        return $this;
    }

    /**
     * @param string|array $requires
     * @return $this
     */
    public function setRequires($requires)
    {
        $this->route->add($requires, 'requires');
        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->route->add($namespace, 'namespace');
        return $this;
    }

    /**
     * @param Mapper $defaultMapper
     * @return $this
     */
    public function setDefaultMapper(Mapper $defaultMapper)
    {
        $this->route->add($defaultMapper, 'defaultMapper');
        return $this;
    }

    /**
     * @param Mapper $homeMapper
     * @return $this
     */
    public function setHomeMapper(Mapper $homeMapper)
    {
        $path = $homeMapper->getPath();
        $path[] = '';
        $this->addMapper($homeMapper->setPath($path));
        return $this;
    }

    /**
     * @param Mapper $mapper
     * @return $this
     */
    public function addMapper(Mapper $mapper)
    {
        $mappers = new Arrays($this->route->get('mappers', []));
        $this->route->add($mappers->add($mapper)->getArray(), 'mappers');
        return $this;
    }

    /**
     * 执行路由
     */
    public function run()
    {
        $mapper = $this->getMapper();
        $requires = $this->route->get('requires', []);
        foreach ($requires as $require) {
            $require = $this->ifEval($require);
            if (is_file($require)) require "$require";
        }
        foreach ($mapper->getRequires() as $require) {
            $require = $this->ifEval($require);
            if (is_file($require)) require "$require";
        }
        $path = $this->ifEval($mapper->getFilePath());
        if (is_file($path)) require "$path";

        $namespace = $mapper->getNamespace();
        if (!class_exists($namespace)) {
            $_500 = $this->getHttpErr(500);
            header($_500->getErrHeader());
            die($_500->getErrMessage());
        }

        $initParams = $mapper->getInitParams();
        $params = $mapper->getParams();
        switch (strtoupper($mapper->getScope())) {
            case 'GET':
                $_GET = (new Arrays($_GET))->addAll($initParams)->addAll($params)->getArray();
                break;
            case 'POST':
                $_POST = (new Arrays($_POST))->addAll($initParams)->addAll($params)->getArray();
                break;
            default:
                Request::addParams($initParams);
                Request::addParams($params);
                break;
        }

        $ctr = call_user_func(array($namespace, 'getInstance'));
        $status = 0;
        if ($ctr instanceof Controller) {
            $status = $ctr->service();
        }
        if ($status != 1) {
            ob_end_clean(); // 删除缓冲区内容并关闭
            header('Content-Type: text/html; charset=utf-8');
        }
        $httpErr = $this->getHttpErr($status);
        if (!is_null($httpErr)) {
            header($httpErr->getErrHeader());
            die($httpErr->getErrMessage());
        }
    }

    /**
     * 获取Mapper对象
     * @return Mapper
     */
    protected function getMapper()
    {
        $baseDir = $this->route->get('baseDir', '/');
        $reqPath = preg_replace("~$baseDir~", '', $this->reqPath, 1);

        Mapper::setReqPath($reqPath);
        Mapper::setDefaultNS($this->route->get('namespace', '%s'));

        $mapper = null;
        $path = null;
        foreach ($this->route->get('mappers', []) as $val) {
            if ($val instanceof Mapper) {
                $paths = $val->getPath();
            } else {
                if (!isset($val['path'])) continue;
                $paths = $val['path'];
            }
            $path = $this->getPath($paths, $reqPath);
            if (!is_null($path)) {
                $mapper = $val;
                break;
            }
        }

        if (!is_null($mapper)) {
            return Mapper::mapping($mapper)->setPath($path);
        } else {
            if (!is_null($defaultMapper = $this->route->get('defaultMapper'))) {
                return Mapper::mapping($defaultMapper)->setPath($path);
            } else {
                $_404 = $this->getHttpErr(404);
                header($_404->getErrHeader());
                die($_404->getErrMessage());
            }
        }
    }

    /**
     * 获取当前访问路径
     * @param array|string $paths
     * @param string $reqPath
     * @return string
     */
    protected function getPath($paths, $reqPath)
    {
        if (!is_array($paths)) $paths = [$paths];
        foreach ($paths as $path) {
            //$matches = null;
            if ($path == $reqPath || @preg_match($path, $reqPath, $matches)) {
                if (empty($matches) || $matches[0] == $reqPath) {
                    return $path;
                }
            }
        }
        return NULL;
    }

    /**
     * @param int $err_code
     * @return HttpError
     */
    public function getHttpErr($err_code)
    {
        $httpErrs = new Arrays($this->route->get('httpErrs', []));
        $httpErr = $httpErrs->get($err_code);
        if (!is_null($httpErr))
            return HttpError::mapping($httpErr);
        return null;
    }

    /**
     * 是否需要eval转换
     * @param string $require
     * @return string
     */
    protected function ifEval($require)
    {
        return substr($require, 0, 5) == 'eval_'
            ? $require = eval('return ' . substr($require, 5))
            : $require;
    }

    /**
     * @param int $err_code
     * @param HttpError $http_err
     * @return $this
     */
    public function addHttpErr($err_code, HttpError $http_err)
    {
        $httpErrs = new Arrays($this->route->get('httpErrs', []));
        $this->route->add($httpErrs->add($http_err, $err_code)->getArray(), 'httpErrs');
        return $this;
    }

    private function __clone()
    {
    }
}