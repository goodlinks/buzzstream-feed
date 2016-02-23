<?php

namespace Goodlinks\BuzzStreamFeed;

class History extends ApiResource
{
    /**
     * @param int $offset
     * @param int $maxResults
     * @return HistoryItem[]
     */
    public function getList($offset = 0, $maxResults = 50)
    {
        return parent::getList($offset, $maxResults);
    }

    protected static function _getUrlPath()
    {
        return 'history';
    }
}