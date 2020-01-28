<?php
namespace trideout;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use trideout\Broadcaster\SseBroadcaster;

class BroadcastSseServiceProvider extends ServiceProvider implements DeferrableProvider {

    public function register() {

    }

    public function provides() : array {
        return [
            SseBroadcaster::class,
        ];
    }
}