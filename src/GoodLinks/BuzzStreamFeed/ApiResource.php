<?php

namespace OauthClient;

require_once(dirname(dirname(__DIR__)) . '/OauthClient/Oauth.php');

namespace GoodLinks\BuzzStreamFeed;

abstract class ApiResource
{
    protected $_resourceUrl;

    /**
     * Holds the data for an api resource like a HistoryItem.
     */
    protected $_data;

    public function __construct($resourceUrl = null)
    {
        if ($resourceUrl) {
            $this->_resourceUrl = $resourceUrl;
        }
    }

    public function getId()
    {
        return $this->_data['id'];
    }

    public function getResourceUrl()
    {
        return $this->_resourceUrl;
    }

    public function setData($data)
    {
        $this->_data = $data;
    }

    /**
     * Override me
     */
    protected static function _getUrlPath()
    {
        return '/';
    }

    /**
     * @param null $chronicleDateBefore
     * @param null $chronicleDateAfter
     * @param int $offset
     * @param int $maxResults
     * @return HistoryItem[]
     */
    public static function getList($chronicleDateAfter = null, $chronicleDateBefore = null, $offset = 0, $maxResults = 50)
    {
        $apiResourceModel = new static;

        $params = array(
            'offset'        => $offset,
            'max_results'   => $maxResults,
        );

        if ($chronicleDateAfter) {
            $params['created_after_internal_date'] = $chronicleDateAfter;
        }

        if ($chronicleDateBefore) {
            $params['created_before_internal_date'] = $chronicleDateBefore;
        }

        $url = Api::$apiUrl . '/' . $apiResourceModel->_getUrlPath();
        $url .= '?' . http_build_query($params);

        // Disable caching by default for lists - otherwise grabbing the history item
        // list is always out of date.
        // @todo clean this up / refactor
        // $cachedResponse = $apiResourceModel->_getCachedRequest($url);
        $cachedResponse = false;

        if ($cachedResponse) {
            return $cachedResponse;
        }

        $objects = array();
        $apiResponse = $apiResourceModel->_request($url);
        if (! isset($apiResponse['list'])) {
            throw new \Exception("No 'list' found for URL ($url): " . print_r($apiResponse, 1));
        }

        foreach ($apiResponse['list'] as $resourceUrl) {
            $objects[] = new HistoryItem($resourceUrl);
        }

        $apiResourceModel->_putCachedRequest($url, $objects, 0.1);

        return $objects;
    }

    public function load($resourceUrl)
    {
        $this->_resourceUrl = $resourceUrl;
        $apiResourceModel = new static;

        $cachedResponse = $apiResourceModel->_getCachedRequest($resourceUrl);
        if ($cachedResponse !== null) {
            $this->setData($cachedResponse);
            return $this;
        }

        $apiResponseData = $apiResourceModel->_request($resourceUrl);
        $apiResourceModel->_putCachedRequest($resourceUrl, $apiResponseData, 30);

        $this->setData($apiResponseData);
        return $this;
    }

    /**
     * @param $apiResourceUrl
     * @return mixed
     * @throws \OAuthException
     * @todo Replace this Oauth class from the BuzzStream docs (https://api.buzzstream.com/docs/api_doc.html#auth) with a newer Oauth component that we can composer in.
     */
    protected function _request($apiResourceUrl)
    {
        $consumer_key = Api::getConsumerKey();
        $consumer_secret = Api::getConsumerSecret();

        $consumer = new \OAuthConsumer($consumer_key, $consumer_secret);

        $request = \OAuthRequest::from_consumer_and_token($consumer, NULL, "GET", $apiResourceUrl);
        $sig_method = new \OAuthSignatureMethod_HMAC_SHA1();
        $request->sign_request($sig_method, $consumer, NULL);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $request->to_url());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array($request->to_header()));
        $response = curl_exec($curl);
        curl_close($curl);

        $json_response = json_decode($response, true);
        if (! is_array($json_response)) {
            throw new \Exception("JSON response is not an array as expected");
        }

        if (isset($json_response['message'])) {
            throw new \Exception($json_response['message']);
        }

        return $json_response;
    }

    protected function _getCache()
    {
        $driver = new \Stash\Driver\FileSystem();
        $path = dirname(dirname(dirname(__DIR__))) . '/cache';
        $driver->setOptions(array('path' => $path));
        $pool = new \Stash\Pool($driver);

        return $pool;
    }

    protected function _getCachedRequest($apiResourceUrl)
    {
        // The slashes mean something crazy in Stash
        $cacheKey = "api_resource_" . str_replace("/", "_", $apiResourceUrl);

        $item = $this->_getCache()->getItem($cacheKey);
        $data = $item->get();
        if (! $item->isMiss()) {
            return $data;
        }

        return null;
    }

    protected function _putCachedRequest($apiResourceUrl, $response, $cacheLifetimeInDays = 1)
    {
        // The slashes mean something crazy in Stash
        $cacheKey = "api_resource_" . str_replace("/", "_", $apiResourceUrl);

        $item = $this->_getCache()->getItem($cacheKey);

        $hours = $cacheLifetimeInDays * 24;
        $minutes = $hours * 60;
        $seconds = $minutes * 60;
        $item->set($response, $seconds);
    }
}