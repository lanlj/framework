<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:42
 */

namespace lanlj\fw\util;

use Exception;
use lanlj\fw\bean\BeanInstance;
use lanlj\fw\bean\BeanMapping;
use lanlj\fw\core\Arrays;
use ReflectionClass;
use ReflectionObject;
use ReflectionProperty;

final class BeanUtil
{
    /**
     * @param array $valuesS
     * @param $class
     * @param bool $db
     * @return array
     */
    public static function populates(array $valuesS, $class, $db = false)
    {
        $objs = [];
        foreach ($valuesS as $values) {
            $obj = self::populate($values, $class, $db);
            if (!is_null($obj)) $objs[] = $obj;
        }
        return $objs;
    }

    /**
     * NULL is returned if the specified class is not exist.
     * @param object|array $values
     * @param string $class
     * @param bool $db
     * @return object|null
     */
    public static function populate($values, $class, $db = false)
    {
        if (is_subclass_of($class, BeanMapping::class))
            return call_user_func(array($class, 'mapping'), $values);
        $obj = self::newInstance($class);
        if (is_null($obj)) return null;
        $ref = new ReflectionObject($obj);
        $values = new Arrays($values);
        foreach ($ref->getProperties() as $property) {
            $name = $property->getName();
            $value = $values->get(self::getColumnName($property, $db));
            $other_ways = true;
            if ($ref->hasMethod($mn = 'set' . ucwords(str_replace('_', '', $name)))) {
                try {
                    $method = $ref->getMethod($mn);
                    $method->setAccessible(true);
                    $method->invoke($obj, $value);
                    $other_ways = false;
                } catch (Exception $e) {
                }
            }
            if ($other_ways) {
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
     * @param string $class
     * @return object|null
     */
    public static function newInstance($class)
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
    private static function getColumnName(ReflectionProperty $property, $db = false)
    {
        return $db && preg_match('/@column\([\'"]([A-Za-z0-9_]+)[\'"]\)/', $property->getDocComment(), $matches) == 1
            ? $matches[2]
            : $property->getName();
    }
}