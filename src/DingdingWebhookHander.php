<?php
/**
 * WeiWorkRobotServer.
 * User: coder.yee <Coder.yee@gmail.com>
 * Date: 2019/8/12
 */
namespace Zhufq\Logfordingding;;

use Monolog\Handler\AbstractProcessingHandler;
use Monolog\Logger;

class DingdingWebhookHander extends AbstractProcessingHandler
{
    private static $config = [];

    public function __construct($level = Logger::DEBUG, $bubble = true, $capSize = false)
    {
        parent::__construct($level, $bubble);
    }

    /**
     * 载入配置
     * @param $config
     */
    public function setConfig($config) {
        self::$config = $config;
    }

    /**
     * 调用机器人发送日志消息
     * @param array $record
     */
    protected function write(array $record):void
    {
        $message = json_encode(['app_url' => config('app.url')] + $record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT |
                                                                           JSON_UNESCAPED_SLASHES);
        //消息过长时直接发送格式化后的日志。
        if(strlen($message) > self::$config['msg_length_max']) {
            $message = $record['formatted'] ?? ($record['message'] ?? substr($message, 0, self::$config['msg_length_max']).'...');
        }
        $data = [
            'msgtype' => 'text',
            'text'    => [
                'content' => $message
            ]
        ];
        $data_string = json_encode($data);
        $this->request_by_curl(self::$config['host'], $data_string);
    }

    /**
     * CURL
     * @param $remote_server
     * @param $post_string
     * @return mixed
     */
    private function request_by_curl($remote_server, $post_string) {
        try{
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $remote_server);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, (self::$config['connect_timeout'] ?? 5));
            curl_setopt($ch, CURLOPT_TIMEOUT, (self::$config['timeout'] ?? 5));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Content-Type: application/json;charset=utf-8'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // 线下环境不用开启curl证书验证, 未调通情况可尝试添加该代码
            // curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
            // curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $data = curl_exec($ch);
            curl_close($ch);
        }catch (\Exception $e) {
            $data = false;
        }
        return $data;
    }
}
