<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 13:23
 */

namespace lanlj\fw\route;

use lanlj\fw\app\Application;
use lanlj\fw\base\Arrays;
use lanlj\fw\ctr\Controller;
use lanlj\fw\http\Request;
use lanlj\fw\route\exception\HttpError;
use lanlj\fw\util\ArrayUtil;

class Route
{
    /**
     * 对象实例
     * @var self
     */
    private static self $_instance;

    /**
     * @var Application
     */
    protected Application $app;

    /**
     * 路由表
     * @var Arrays
     */
    protected Arrays $route;

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
    protected function init(): void
    {
        ob_start(); // 开启缓冲区

        $this->app = Application::getInstance();

        $httpErrs = $this->getDefaultHttpErrs();

        // 检测访问路径是不是PHP_SELF
        if ($_SERVER['PHP_SELF'] === $this->app->getRequestPath(false)) {
            $_403 = HttpError::mapping($httpErrs[403]);
            header($_403->getErrHeader());
            die($_403->getErrMessage());
        }

        $this->route = new Arrays(
            ['httpErrs' => $httpErrs]
        ); // 初始化路由
        $this->setRouteFile('./src/resources/route.json');
    }

    /**
     * 默认HTTP错误列表
     * @return array
     */
    protected function getDefaultHttpErrs(): array
    {
        return [
            400 => new HttpError(
                "HTTP/1.1 400 Bad Request",
                '<html lang="en"><title>400 Bad Request</title><body>400 Bad Request</body></html>'
            ),
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
        ];
    }

    /**
     * @param string $file
     * @return $this
     */
    public function setRouteFile(string $file): self
    {
        if (is_file($file))
            $this->route->addAll(json_decode(file_get_contents($file), true));
        return $this;
    }

    /**
     * 获取路由实例
     * @return self
     */
    public static final function getInstance(): self
    {
        return self::$_instance ?? self::$_instance = new static();
    }

    /**
     * @param array|object $route
     * @return $this
     */
    public function setRoute($route): self
    {
        $this->route->addAll(ArrayUtil::toArray($route, false, true));
        return $this;
    }

    /**
     * @param string $baseDir
     * @return $this
     */
    public function setBaseDir(string $baseDir): self
    {
        $this->route->add($baseDir, 'baseDir');
        return $this;
    }

    /**
     * @param string|array $requires
     * @return $this
     */
    public function setRequires($requires): self
    {
        $this->route->add($requires, 'requires');
        return $this;
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace(string $namespace): self
    {
        $this->route->add($namespace, 'namespace');
        return $this;
    }

    /**
     * @param Mapper $defaultMapper
     * @return $this
     */
    public function setDefaultMapper(Mapper $defaultMapper): self
    {
        $this->route->add($defaultMapper, 'defaultMapper');
        return $this;
    }

    /**
     * @param Mapper $homeMapper
     * @return $this
     */
    public function setHomeMapper(Mapper $homeMapper): self
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
    public function addMapper(Mapper $mapper): self
    {
        $mappers = new Arrays($this->route->get('mappers', []));
        $this->route->add($mappers->add($mapper)->getArray(), 'mappers');
        return $this;
    }

    /**
     * 执行路由
     */
    public function run(): void
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
        $status = 500;
        if ($ctr instanceof Controller) {
            $status = $ctr->service();
        }
        if ($status != 200) {
            ob_end_clean(); // 删除缓冲区内容并关闭
            header('Content-Type: text/html; charset=utf-8');

            $httpErr = $this->getHttpErr($status);
            if (!is_null($httpErr)) {
                header($httpErr->getErrHeader());
                die($httpErr->getErrMessage());
            }
        }
    }

    /**
     * 获取Mapper对象
     * @return Mapper
     */
    protected function getMapper(): Mapper
    {
        $baseDir = $this->route->get('baseDir', '/');
        $reqPath = preg_replace("~$baseDir~", '', $this->app->getRequestPath(), 1);

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
    protected function getPath($paths, string $reqPath): ?string
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
     * @param int $errCode
     * @return HttpError
     */
    public function getHttpErr(int $errCode): ?HttpError
    {
        $httpErrs = new Arrays($this->route->get('httpErrs', []));
        $httpErr = $httpErrs->get($errCode);
        return !is_null($httpErr) ? HttpError::mapping($httpErr) : NULL;
    }

    /**
     * 是否需要eval转换
     * @param string|null $require
     * @return string|null
     */
    protected function ifEval(?string $require): ?string
    {
        return substr($require, 0, 5) == 'eval_' ? eval('return ' . substr($require, 5) . ';') : $require;
    }

    /**
     * @param int $errCode
     * @param HttpError $httpErr
     * @return $this
     */
    public function addHttpErr(int $errCode, HttpError $httpErr): self
    {
        $httpErrs = new Arrays($this->route->get('httpErrs', []));
        $this->route->add($httpErrs->add($httpErr, $errCode)->getArray(), 'httpErrs');
        return $this;
    }

    private function __clone()
    {
    }
}