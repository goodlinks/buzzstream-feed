<?php

namespace GoodLinks\BuzzStreamFeed;

class Website extends ApiResource
{
    public function getUrl()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getUrl() because resourceUrl not set on this Website object");
        }

        $this->load($this->_resourceUrl);
        return $this->_data['url'];
    }

    public function getAvatarUrl()
    {
        if (isset($this->_data['socialNetworks'])) {
            foreach ($this->_data['socialNetworks'] as $socialNetworkData) {
                if ($socialNetworkData['name'] == 'Twitter') {
                    $twitterUsername = $socialNetworkData['profileUrl'];

                    // Sometimes the Twitter username field contains the full URL
                    if (strpos($twitterUsername, "twitter.com") !== false) {
                        $parts = explode("/", $twitterUsername);
                        $twitterUsername = $parts[count($parts) - 1];
                    }

                    return "https://app.buzzstream.com/twitterAvatar?id=$twitterUsername&h=24&w=24";
                }
            }
        }

        return "https://app.buzzstream.com/img/default_avatar_media.png";
    }
}