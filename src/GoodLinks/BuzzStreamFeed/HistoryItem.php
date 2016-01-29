<?php

namespace GoodLinks\BuzzStreamFeed;

class HistoryItem
{
    public static function get($historyId)
    {
        return array(
            'key'   => Api::getConsumerKey(),
        );
    }
}