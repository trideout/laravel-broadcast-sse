<?php
namespace trideout\Broadcaster;

use Illuminate\Broadcasting\Broadcasters\Broadcaster;
use Illuminate\Broadcasting\Broadcasters\UsePusherChannelConventions;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class SseBroadcaster extends Broadcaster {

    use UsePusherChannelConventions;

    public function auth($request)
    {
        $channelName = $this->normalizeChannelName(
            $request->channel_name
        );

        if ($this->isGuardedChannel($request->channel_name) &&
            !$this->retrieveUser($request, $channelName)) {
            throw new AccessDeniedHttpException;
        }

        return $this->verifyUserCanAccessChannel(
            $request, $channelName
        );
    }

    public function validAuthenticationResponse($request, $result)
    {
        if (is_bool($result)) {
            return json_encode($result);
        }

        $channelName = $this->normalizeChannelName($request->channel_name);

        return json_encode(['channel_data' => [
            'user_id' => $this->retrieveUser($request, $channelName)->getAuthIdentifier(),
            'user_info' => $result,
        ]]);
    }

    public function broadcast(array $channels, $event, array $payload = [])
    {
    }
}