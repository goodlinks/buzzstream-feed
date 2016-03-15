<?php

namespace GoodLinks\BuzzStreamFeed;

class User extends ApiResource
{
    public function getName()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getName() because resourceUrl not set on this Project object");
        }

        $this->load($this->_resourceUrl);
        return $this->_data['firstName'] . " " . $this->_data['lastName'];
    }

    public function getEmail()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getName() because resourceUrl not set on this Project object");
        }

        $this->load($this->_resourceUrl);
        return $this->_data['email'];
    }
}