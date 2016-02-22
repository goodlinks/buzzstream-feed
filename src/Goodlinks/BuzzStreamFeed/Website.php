<?php

namespace Goodlinks\BuzzStreamFeed;

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
}