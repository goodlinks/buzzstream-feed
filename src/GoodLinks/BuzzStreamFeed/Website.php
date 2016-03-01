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
                    return "https://app.buzzstream.com/twitterAvatar?id=$twitterUsername&h=24&w=24";
                }
            }
        }

        return "http://style.anu.edu.au/_anu/4/images/placeholders/person.png";
    }
}