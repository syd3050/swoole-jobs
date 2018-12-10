<?php
/**
 * Created by PhpStorm.
 * User: syd
 * Date: 18-12-10
 * Time: 下午4:30
 */

namespace Ethan\Job\handler;


class Example
{
    public static function run(array $params=[]) {
        self::foo($params['msg']);
    }

    private static function foo($msg) {
        echo PHP_EOL."result=>$msg".PHP_EOL;
    }
}