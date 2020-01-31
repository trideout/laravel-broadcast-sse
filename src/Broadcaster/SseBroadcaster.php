<?php
namespace trideout\SseBroadcaster\Broadcaster;

class SseBroadcaster {
    private $redis;
    private $channelName;

    public function __construct(string $channelName)
    {
        echo 'lala';die();
        $this->redis = app()->make(Redis::class);
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