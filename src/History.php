<?php

namespace GoodLinks\BuzzStreamFeed;

class History
{
    public static function all()
    {
        return array(
            'key'   => Api::getConsumerKey(),
        );
    }
}