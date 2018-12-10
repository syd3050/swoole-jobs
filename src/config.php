<?php
/****************************************************
 * 配置文件
 *
 * Date: 18-12-10
 * Time: 下午4:20
 * **************************************************
 */

return [
    'jobs-register' => [
        [
            'name'    => 'example',
            'cache'   => ['type'=>'Redis','host'=>'127.0.0.1','port'=>6379],
            'channel' => 'order:list',
            'handler' => [
                'type'=> 'local',
                'uri' => 'Ethan\\Job\\handler\\Example'
            ]
        ],
        //...
    ],
];