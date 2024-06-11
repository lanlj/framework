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
    private static string $reqPath;

    /**
     * @var string
     */
    private static string $defaultNS;

    /**
     * @var string|array
     */
    private $path;

    /**
     * @var string
     */
    private ?string $name;

    /**
     * @var string
     */
    private ?string $filePath;

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
    private ?string $namespace;

    /**
     * @var string|array
     */
    private $initParams;

    /**
     * @var string
     */
    private ?string $scope;

    /**
     * Mapper constructor.
     * @param array|string $path
     * @param string $name
     * @param string $filePath
     * @param array|string $params
     * @param array|string $requires
     * @param string $namespace
     * @param array|string $initParams
     * @param string $scope
     */
    public function __construct(
        $path = null, string $name = null, string $filePath = null, $params = null,
        $requires = null, string $namespace = null, $initParams = null, string $scope = null
    )
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
    public static function mapping($values): self
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
    public static function setReqPath(string $reqPath)
    {
        self::$reqPath = $reqPath;
    }

    /**
     * @param string $defaultNS
     */
    public static function setDefaultNS(string $defaultNS)
    {
        self::$defaultNS = $defaultNS;
    }

    /**
     * @return array
     */
    public function getPath(): array
    {
        return is_array($this->path) ? $this->path : [$this->path];
    }

    /**
     * @param array|string $path
     * @return $this
     */
    public function setPath($path): self
    {
        $this->path = $path;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    /**
     * @param string $filePath
     * @return $this
     */
    public function setFilePath(?string $filePath): self
    {
        $this->filePath = $filePath;
        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
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
    public function setParams($params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @return array
     */
    public function getRequires(): array
    {
        return is_array($this->requires) ? $this->requires : [$this->requires];
    }

    /**
     * @param array|string $requires
     * @return $this
     */
    public function setRequires($requires): self
    {
        $this->requires = $requires;
        return $this;
    }

    /**
     * @return string
     */
    public function getNamespace(): string
    {
        if (is_string($this->namespace)) return $this->namespace;
        $name = is_string($this->name) ? $this->name : self::$reqPath;
        return sprintf(self::$defaultNS, $name);
    }

    /**
     * @param string $namespace
     * @return $this
     */
    public function setNamespace(string $namespace): self
    {
        $this->namespace = $namespace;
        return $this;
    }

    /**
     * @return array
     */
    public function getInitParams(): array
    {
        if (is_array($this->initParams)) return $this->initParams;
        parse_str($this->initParams, $params);
        return $params;
    }

    /**
     * @param array|string $initParams
     * @return $this
     */
    public function setInitParams($initParams): self
    {
        $this->initParams = $initParams;
        return $this;
    }

    /**
     * @return string
     */
    public function getScope(): ?string
    {
        return $this->scope;
    }

    /**
     * @param string $scope
     * @return $this
     */
    public function setScope(?string $scope): self
    {
        $this->scope = $scope;
        return $this;
    }
}