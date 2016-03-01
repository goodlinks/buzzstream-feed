<?php

namespace GoodLinks\BuzzStreamFeed;

class Project extends ApiResource
{
    public function getName()
    {
        if (! $this->_resourceUrl) {
            throw new \Exception("Can't getName() because resourceUrl not set on this Project object");
        }

        $this->load($this->_resourceUrl);
        return $this->_data['name'];
    }

    public function fetchHistory()
    {
        $history = History::getList($this->getId());;
        return $history;
    }
}