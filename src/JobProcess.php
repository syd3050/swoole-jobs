<?php
namespace Ethan\Job;

class JobProcess
{
    public  $mpid = 0;
    private $maxProcesses = 600;
    public  $workers = [];

    public function run(){
        //$begin = microtime(true);
        //mac os不重命名进程
        if (function_exists('swoole_set_process_name') && PHP_OS != 'Darwin') {
            swoole_set_process_name(sprintf('php-ps:%s', 'master'));
        }
        $this->mpid = posix_getpid();
        for($i=1; $i <= $this->maxProcesses; $i++) {
            echo PHP_EOL."\t Starting new child | now we de have {$i} child processes ".PHP_EOL;
            $this->createProcess($i);
        }
        $this->processWait();
    }

    public function processWait(){
        while(1) {
            if(count($this->workers)){
                $ret = \swoole_process::wait();
                if ($ret) {
                    //echo PHP_EOL."Process: {$ret['pid']} Exit ";
                    $this->rebootProcess($ret);
                }
            }else{
                break;
            }
        }
    }

    public function createProcess($index=null) {
        //不重定向子进程输出，不起用管道
        $process = new \swoole_process(function(\swoole_process $worker)use($index){
            //mac os不重命名进程
            if (function_exists('swoole_set_process_name') && PHP_OS != 'Darwin') {
                swoole_set_process_name(sprintf('php-ps:%s',$index));
            }
            //执行实际工作
            $config = array(
                'key'=>'order:list',
                'cache'=>['type'=>'Redis','host'=>'127.0.0.1','port'=>6379],
                'time'=>microtime(true)
            );
            $job = new Job($config);
            $job->run();
            $worker->exit(0);
        }, false, false);
        //创建进程
        $pid = $process->start();
        if($pid > 0)
            $this->workers[] = $pid;
        return $pid;
    }

    /**
     * 重建进程
     * @param $ret
     * @throws \Exception
     */
    public function rebootProcess($ret){
        $pid = $ret['pid'];
        $index = array_search($pid, $this->workers);
        if($index !== false){
            $index = intval($index);
            $new_pid = $this->createProcess($index);
            echo PHP_EOL."rebootProcess: {$index}={$new_pid} Done".PHP_EOL;
            return;
        }
        throw new \Exception('rebootProcess Error: no pid');
    }

}