<?php
/**
 * Created by PhpStorm.
 * User: syd
 * Date: 18-12-10
 * Time: 下午4:52
 */

namespace Ethan\Job\handler;


class HandlerFactory
{
    private $types = ['local', 'api', 'socket'];
    private $handler = null;

    public function build($handler) {
        if(!$this->_check($handler)) {
            return null;
        }
        $this->handler = $handler;
        return call_user_func($handler['type']);
    }

    private function _check($handler) {
        if(!isset($handler['type'],$handler['uri']))
            return false;
        if(!in_array($handler['type'],$this->types))
            return false;
        return true;
    }

    private function local() {
        $func = $this->handler['uri']."::run";
        if(is_callable($func)) {
            return call_user_func_array($func,[$this->handler]);
        }
        return null;
    }

    private function api() {

    }

    private function socket() {

    }
}