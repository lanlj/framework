<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/16
 * Time: 13:59
 */

namespace lanlj\fw\route;

use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;

class Mapper implements BeanMapping
{
    /**
     * @var string
     */
    private static $reqPath;

    /**
     * @var string
     */
    private static $defaultNS;

    /**
     * @var string|array
     */
    private $path;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $filePath;

    /**
     * @var string|array
     */
    private $params;

    /**
     * @var string|array
     */
    private $requires;

    /**
     * @var string
     */
    private $namespace;

    /**
     * @var string|array
     */
    private $initParams;

    /**
     * @var string
     */
    private $scope;

    /**
     * Mapper constructor.
     * @param array|string $path
     * @param string $name
     * @param string $filePath
     * @param string $params
     * @param array|string $requires
     * @param string $namespace
     * @param array|string $initParams
     * @param string $scope
     */
    public function __construct($path = null, $name = null, $filePath = null, $params = null, $requires = null, $namespace = null, $initParams = null, $scope = null)
    {
        $this->path = $path;
        $this->name = $name;
        $this->filePath = $filePath;
        $this->params = $params;
        $this->requires = $requires;
        $this->namespace = $namespace;
        $this->initParams = $initParams;
        $this->scope = $scope;
    }

    /**
     * @param object|array $values
     * @return $this
     */
    public static function mapping($values)
    {
        if ($values instanceof self)
            return $values;
        $values = new Arrays($values);
        return new self(
            $values->get('path'),
            $values->get('name'),
            $values->get('filePath'),
            $values->get('params'),
            $values->get('requires'),
            $values->get('namespace'),
            $values->get('initParams'),
            $values->get('scope')
        );
    }

    /**
     * @param string $reqPath
     */
    public static function setReqPath($reqPath)
    {
        self::$reqPath = $reqPath;
    }

    /**
     * @param string $defaultNS
     */
    public static function setDefaultNS($defaultNS)
    {
        self::$defaultNS = $defaultNS;
    }

    /**
     * @return array
     */
    public function getPath()
    {
        return is_array($this->path) ? $this->path : [$this->path];
    }

    /**
     * @param array|string $path
     * @return $this
     */
    public function setPath($path)
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath()
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath($filePath)
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        if (is_array($this->params)) return $this->params;
        $ps = @preg_replace($this->path, $this->params, self::$reqPath);
        parse_str($ps, $params);
        return $params;
    }

    /**
     * @param array|string $params
     * @return $this
     */
    public function setParams($params)
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequires()
    {
        return is_array($this->requires) ? $this->requires : [$this->requires];
    }

    /**
     * @param array|string $requires
     * @return $this
     */
    public function setRequires($requires)
    {
        $this->requires = $requires;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace()
    {
        if (is_string($this->namespace)) return $this->namespace;
        $name = is_string($this->name) ? $this->name : self::$reqPath;
        return sprintf(self::$defaultNS, $name);
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace($namespace)
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return array
     */
    public function getInitParams()
    {
        if (is_array($this->initParams)) return $this->initParams;
        parse_str($this->initParams, $params);
        return $params;
    }

    /**
     * @param array|string $initParams
     * @return $this
     */
    public function setInitParams($initParams)
    {
        $this->initParams = $initParams;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope()
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope($scope)
    {
        $this->scope = $scope;
        return $this;
    }
}