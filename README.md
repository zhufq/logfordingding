# logfordingding
this is a auto push logs to dingding....
#### 在config/logging中的channels中添加如下代码代码
```javascript
'DingDingRobotLog' => [
            'driver' => 'custom',
            'via'    => \Zhufq\Logfordingding\CreateCustomLogger::class,
            //host : 钉钉机器人地址
            'host'   => env('LOG_SLACK_Dinging_URL'),
            //level：错误日志记录级别
            'level'  => 'DEBUG',
            //日志最大长度
            'msg_length_max'  => 5000,
            //push 推送开关，false时不推送日志到钉钉微信机器人。
            'push'   => true,
            // Curl 连接超时时间(秒)
            'connect_timeout' => 5,
            // Curl 响应超时时间(秒)
            'timeout' => 2,
        ],
'stack' => [
            'driver' => 'stack',
            'channels' => ['daily','DingDingRobotLog'],（把上面添加好的配置 加到这里来）
            'ignore_exceptions' => false,
			
			
测试代码
        $logger = Log::stack(['DingDingRobotLog']);
	
        $logger->pushProcessor(function ($record) {
            $record['extra']['dummy'] = 'Hello world!';

            return $record;
        });

        $logger->info('测试日志系统',['ceshi'=>222222]);
		
		其实你可以直接  Log::info("测试日志");

钉钉群显示
{
    "app_url": "http://localhost",
    "message": "测试日志系统",
    "context": {
        "ceshi": 222222
    },
    "level": 200,
    "level_name": "INFO",
    "channel": "local",
    "datetime": {
        "date": "2020-02-14 18:19:13.145053",
        "timezone_type": 3,
        "timezone": "PRC"
    },
    "extra": {
        "dummy": "Hello world!"
    },
    "formatted": "[2020-02-14 18:19:13] local.INFO: 测试日志系统 {\"ceshi\":222222} {\"dummy\":\"Hello world!\"}\n"
}
        ],
```
