<?php
/**
 *文件描述
 */

namespace Zhufq\Logfordingding;


use Monolog\Logger;
use Illuminate\Log\LogManager;

class CreateCustomLogger {
    public function __invoke(array $config)
    {
        if(empty($config['push']) || !$config['push'])
            return null;

        $logger = new Logger('DingDingRobotLog');

        //新建一个handel处理器
        $push   = new DingdingWebhookHander();
        $push->setConfig($config);
        $push->setLevel($config['level']);

        //处理器 压入 栈
        $logger->pushHandler($push);

        return $logger;
    }
}
