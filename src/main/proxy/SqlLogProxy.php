<?php
/**
 * Created by PhpStorm.
 * User: LanLiJun
 * Mail: jun@lanlj.com
 * Date: 2024/6/14
 * Time: 20:00
 */

namespace lanlj\fw\proxy;

use DateTime;
use Exception;
use lanlj\fw\app\Application;
use lanlj\fw\bean\BeanInstance;
use lanlj\fw\core\Strings;
use lanlj\fw\util\{FileUtil, JsonUtil};

class SqlLogProxy implements BeanInstance
{
    /**
     * @inheritDoc
     */
    public static function newInstance(...$args): ?self
    {
        return new static();
    }

    /**
     *
     * @param array|null $params
     */
    public static function execute(?array $params): void
    {
        extract($params);
        $log = new Strings((new DateTime())->format('Y-m-d H:i:s.u') . ' ----- ');
        $log->concat("$class->$method(" . (count($args) == 0 ? NULL : JsonUtil::toJsonString($args)) . ")");
        $log->concat(": " . self::dealReturned($return));
        $exception instanceof Exception && $log->concat(', Throw exception: [' . $exception->getMessage() . ']');
        is_null($err = $instance->getLastError()) || $log->concat(", Error: [$err]");
        is_null($sql = $instance->getLastQuery()) || $log->concat(", SQL: [$sql]");
        self::saveLog($log->concat("\r\n"));
    }

    /**
     * @param mixed $return
     * @return string
     */
    private static function dealReturned($return): string
    {
        if (is_null($return)) return 'null';
        if (is_object($return)) return 'object';
        if (is_array($return)) return sprintf('array(%u)', count($return));
        return JsonUtil::toJsonString($return);
    }

    /**
     * @param string $log
     */
    public static function saveLog(string $log): void
    {
        $file = Application::getInstance()->getDB()->getLogFile();
        is_dir($dir = dirname($file)) || FileUtil::mkDirs($dir);
        $res = fopen($file, "a+");
        fwrite($res, $log);
        fclose($res);
    }
}