<?php
/**
 * Created by PhpStorm.
 * User: kalenj
 * Date: 2/22/16
 * Time: 9:53 AM
 */

namespace Goodlinks;

use Goodlinks\BuzzStreamFeed\Api;

class BuzzStreamFeedTest extends \PHPUnit_Framework_TestCase
{
    public function testHistoryApiResponse()
    {
        $Loader = new \josegonzalez\Dotenv\Loader('/kj/goodlinks.io/.env');
        $Loader->parse();
        $Loader->putenv();

        Api::setConsumerKey(getenv('BUZZSTREAM_CONSUMER_KEY'));
        Api::setConsumerSecret(getenv('BUZZSTREAM_CONSUMER_SECRET'));

        $app = new BuzzStreamFeed();
        $history = $app->createHistory();
        $historyItems = $history->getList();

        foreach ($historyItems as $historyItem) {
            $this->assertNotEmpty($historyItem);
            $this->assertNotNull($historyItem->getDate());
        }
    }
}