<?php
/**
 * Created by PhpStorm.
 * User: kalenj
 * Date: 2/22/16
 * Time: 9:42 AM
 */

namespace Goodlinks;

use Goodlinks\BuzzStreamFeed\Api;
use Goodlinks\BuzzStreamFeed\ApiUrlBuilder;
use Goodlinks\BuzzStreamFeed\History;

class BuzzStreamFeed
{
    public function createUrlBuilder()
    {
        return new ApiUrlBuilder(Api::$apiUrl);
    }

    public function createHistory()
    {
        return new History($this->createUrlBuilder());
    }
}