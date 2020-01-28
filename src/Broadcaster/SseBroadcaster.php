<?php
namespace trideout\Broadcaster;

use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;
use Illuminate\Contracts\Redis\Factory as Redis;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $response = new StreamedResponse();
        $response->headers->set('Content-Type','text/event-stream');
        $response->headers->set('Cache-Control','no-cache');
        $response->headers->set('Connection','keep-alive');
        $response->headers->set('X-Accel-Buffering', 'no');

        $response->setCallback(function() {
            (new SSE())->start(new Update(function () {
                $id = random_int(1, 1000);
                $message = $this->redis->connection()->command('LPOP', [$this->channelName]);
                if (!empty($message)) {
                    return json_encode($message);
                }
                return false;//no new messages
            }), 'new-msgs');
        });

        return $response;
    }
}