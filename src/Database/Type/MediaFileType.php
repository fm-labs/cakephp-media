<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 12/3/15
 * Time: 10:40 PM
 */

namespace Media\Database\Type;

use Cake\Database\Driver;
use Cake\Database\TypeFactory;
use PDO;

class MediaFileType extends \Cake\Database\TypeFactory
{

    public function toPHP($value, Driver $driver)
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);
        if (!$value) {
            return [];
        }

        if (preg_match('/^\{(.*)\}$/', $value)) {
            return $value;
            //return json_decode($value, true);
        }

        return explode(',', $value);
    }

    public function marshal($value)
    {
        if (is_array($value) || $value === null) {
            return $value;
        }

        if (is_string($value) && strlen(trim($value)) > 0) {
            $value = explode(',', trim($value));
        }

        return $value;
    }

    public function toDatabase($value, Driver $driver)
    {
        if (is_array($value)) {
            return join(',', $value);
        }

        debug($value);

        return $value;
    }

    public function toStatement($value, Driver $driver)
    {
        if ($value === null) {
            return PDO::PARAM_NULL;
        }

        return PDO::PARAM_STR;
    }
}
