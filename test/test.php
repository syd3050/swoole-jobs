<?php

$redis = new Redis();
$result = $redis->connect("127.0.0.1", 6379);

$num = 5;

if($result) {
    for ($i = 0; $i < $num; $i++){
        $start = rand(0,93);
        $id = substr(str_shuffle(str_repeat("0123456789",10)),$start,6);
        $redis->lpush("order:list", $id);
    }
    exit;
}

