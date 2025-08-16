<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/11/19
 * Time: 9:42
 */

namespace lanlj\fw\util;

use Error;
use Exception;
use lanlj\fw\base\Arrays;
use lanlj\fw\bean\{BeanInstance, BeanMapping};
use ReflectionClass;
use ReflectionObject;
use stdClass;

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
        $objects = [];
        foreach ($values as $value) {
            $obj = self::populate($value, $class, $db);
            if (!is_null($obj)) $objects[] = $obj;
        }
        return $objects;
    }

    /**
     * NULL is returned if the specified class is not exist.
     * @param object|array $value
     * @param string|object $class
     * @param bool $db
     * @return object|null
     */
    public static function populate($value, $class, bool $db = false): ?object
    {
        if (!is_string($class) && !is_object($class)) return NULL;
        if (!is_object($value) && !is_array($value)) return NULL;
        if ($value instanceof $class) return $value;
        $values = new Arrays($value);
        if ($values->isEmpty()) return NULL;
        if (is_subclass_of($class, BeanMapping::class))
            return call_user_func(array($class, 'mapping'), $value);
        $obj = self::newInstance($class);
        if (is_null($obj)) return NULL;
        if ($obj instanceof stdClass) return (object)$values->getArray();
        $ref = new ReflectionObject($obj);
        $properties = $ref->getProperties();
        $parent = $ref->getParentClass();
        while (true) {
            if ($parent === false) break;
            $properties = array_merge($parent->getProperties(), $properties);
            $parent = $parent->getParentClass();
        }
        foreach ($properties as $property) {
            try {
                $name = $property->getName();
                $value = $values->get(DBUtil::getColumnName($property, $db));

                if ($property->hasType()) {
                    $type = $property->getType();
                    if (!$type->isBuiltin()) {
                        $value = self::populate($value, $type->getName(), $db);
                    }
                    if (is_null($value) && !$type->allowsNull()) {
                        $property->setAccessible(true);
                        if ($property->isInitialized($obj)) {
                            $value = $property->getValue($obj);
                        } else continue;
                    }
                }

                $otherWays = true;
                $_name = str_replace('_', '', $name);
                if ($ref->hasMethod($mn = 'set' . ucwords($_name)) || $ref->hasMethod($mn = 'set' . strtoupper($_name))) {
                    $method = $ref->getMethod($mn);
                    $method->setAccessible(true);
                    $method->invoke($obj, $value);
                    $otherWays = false;
                }
                if ($otherWays) {
                    if ($ref->hasMethod('__set'))
                        $obj->$name = $value;
                    else {
                        $property->setAccessible(true);
                        $property->setValue($obj, $value);
                    }
                }
            } catch (Error | Exception $e) {
                continue;
            }
        }
        return $obj;
    }

    /**
     * @param string|object $class
     * @return object|null
     */
    public static function newInstance($class): ?object
    {
        if (is_object($class)) return $class;
        if (is_string($class) && !class_exists($class)) return null;
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
}