<?php
namespace LaravelBroadcastSse\Broadcaster;

use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;
use Illuminate\Contracts\Redis\Factory as Redis;

class SseBroadcaster {
    private $redis;
    private $channelName;
    private $running = false;

    public function __construct(Redis $redis,string $channelName)
    {
        $this->redis = $redis;
        $this->channelName = $channelName;
    }

    public function start()
    {
        $this->running = true;
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        (new SSE())->start(new Update(function (){
            $id = random_int(1, 1000);
            $message = $this->redis->connection()->command('LPOP', [$this->channelName]);
            if(!empty($message)){
                return json_encode($message);
            }
            return false;//no new messages
        }));
    }

    public function stop()
    {
        $this->running = false;
    }
}