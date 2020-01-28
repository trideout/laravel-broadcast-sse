<?php
namespace trideout\Broadcaster;


use Hhxsv5\SSE\SSE;
use Hhxsv5\SSE\Update;

class SseChannel
{
    public function startService()
    {
        (new SSE())->start(new Update(function () {

        }));
    }
}