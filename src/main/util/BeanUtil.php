<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:42
 */

namespace lanlj\fw\util;

use Exception;
use lanlj\fw\bean\{BeanInstance, BeanMapping};
use lanlj\fw\core\Arrays;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;

class BeanUtil
{
    /**
     * @param array $values
     * @param mixed $class
     * @param bool $db
     * @return array
     */
    public static function populates(array $values, $class, bool $db = false): array
    {
        $objs = [];
        foreach ($values as $value) {
            $obj = self::populate($value, $class, $db);
            if (!is_null($obj)) $objs[] = $obj;
        }
        return $objs;
    }

    /**
     * NULL is returned if the specified class is not exist.
     * @param object|array $value
     * @param mixed $class
     * @param bool $db
     * @return object|null
     */
    public static function populate($value, $class, bool $db = false): ?object
    {
        if (!is_object($value) && !is_array($value)) return NULL;
        $values = new Arrays($value);
        if ($values->isEmpty()) return NULL;
        if (is_subclass_of($class, BeanMapping::class))
            return call_user_func(array($class, 'mapping'), $value);
        $obj = self::newInstance($class);
        if (is_null($obj)) return NULL;
        $ref = new ReflectionObject($obj);
        foreach ($ref->getProperties() as $property) {
            $name = $property->getName();
            $value = $values->get(self::getColumnName($property, $db));
            $otherWays = true;
            $_name = str_replace('_', '', $name);
            if ($ref->hasMethod($mn = 'set' . ucwords($_name)) || $ref->hasMethod($mn = 'set' . strtoupper($_name))) {
                try {
                    $method = $ref->getMethod($mn);
                    $method->setAccessible(true);
                    $method->invoke($obj, $value);
                    $otherWays = false;
                } catch (Exception $e) {
                }
            }
            if ($otherWays) {
                if ($ref->hasMethod('__set'))
                    $obj->$name = $value;
                else {
                    $property->setAccessible(true);
                    $property->setValue($obj, $value);
                }
            }
        }
        return $obj;
    }

    /**
     * @param mixed $class
     * @return object|null
     */
    public static function newInstance($class): ?object
    {
        if (is_object($class)) return $class;
        if (!class_exists($class)) return null;
        if (is_subclass_of($class, BeanInstance::class))
            return call_user_func(array($class, 'newInstance'));
        try {
            $ref = new ReflectionClass($class);
            $const = $ref->getConstructor();
            if (!is_null($const) && !$const->isPublic()) {
                $method = null;
                if ($ref->hasMethod($mn = 'getInstance')) {
                    $method = $ref->getMethod($mn);
                } elseif ($ref->hasMethod($mn = 'newInstance')) {
                    $method = $ref->getMethod($mn);
                }
                if (!is_null($method) && $method->getNumberOfRequiredParameters() == 0) {
                    return $method->invoke(null);
                }
            }
            try {
                return $ref->newInstance();
            } catch (Exception $e) {
                return $ref->newInstanceWithoutConstructor();
            }
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * @param ReflectionProperty $property
     * @param bool $db
     * @return string
     */
    public static function getColumnName(ReflectionProperty $property, bool $db = false): ?string
    {
        return $db && preg_match('/@column\([\'"]([A-Za-z0-9_]+)[\'"]\)/', $property->getDocComment(), $matches) == 1
            ? $matches[1]
            : $property->getName();
    }
}