<?php

namespace GoodLinks\BuzzStreamFeed;

class Api
{
    public static $consumerKey;
    public static $consumerSecret;

    public static $apiUrl = 'https://api.buzzstream.com/v1';

    public static function getConsumerKey()
    {
        return self::$consumerKey;
    }

    public static function setConsumerKey($consumerKey)
    {
        self::$consumerKey = $consumerKey;
    }

    public static function getConsumerSecret()
    {
        return self::$consumerSecret;
    }

    public static function setConsumerSecret($consumerSecret)
    {
        self::$consumerSecret = $consumerSecret;
    }
}