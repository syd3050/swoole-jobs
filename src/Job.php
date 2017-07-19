<?php
namespace Ethan\Job;

class Job
{
    private $max = 6000;
    private $key;
    private $cache = null;
    private $config;

    private static function checkConfig($config) {
        if(!isset($config['key']) || !isset($config['cache'])){
            return false;
        }
        if( !isset($config['cache']['type']) ||
            !isset($config['cache']['host']) ||
            !isset($config['cache']['port'])){
            return false;
        }

        return true;
    }

    public function __construct($config) {
        if(self::checkConfig($config)) {
            $this->config = $config;
            $this->key = $config['key'];
            if(strtolower($config['cache']['type']) == 'redis'){
                $redis = new \Redis();
                if($redis->connect($config['cache']['host'],$config['cache']['port'])) {
                    $this->cache = $redis;
                }
            }
        }
    }


    public function run() {
        if($this->cache == null){
            return;
        }
        $begin = $this->max;
        while($begin > 0) {
            $data = $this->cache->brPop($this->key,1);
            if (!$data){
                $current = microtime(true);
                $cost = $current - $this->config['time'];
                echo PHP_EOL." cost time:$cost ".PHP_EOL;
                continue;
            }
            //$str = implode(" ",$data);
            $begin--;
            usleep(rand(19000,20000));
            //echo PHP_EOL." Working with data: $str ";
        }
    }
}