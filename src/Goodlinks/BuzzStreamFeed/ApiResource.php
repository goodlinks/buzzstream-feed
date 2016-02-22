<?php

namespace OauthClient;

require_once(dirname(dirname(__DIR__)) . '/OauthClient/Oauth.php');

namespace Goodlinks\BuzzStreamFeed;

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

    public static function getList($offset = 0, $maxResults = 50)
    {
        $apiResourceModel = new static;

        $url = Api::$apiUrl . '/' . $apiResourceModel->_getUrlPath();
        $url .= '?' . http_build_query(array(
            'offset'        => $offset,
            'max_results'   => $maxResults,
        ));

        $cachedResponse = $apiResourceModel->_getCachedRequest($url);
        if ($cachedResponse) {
            return $cachedResponse;
        }

        $objects = array();
        $apiResponse = $apiResourceModel->_request($url);
        foreach ($apiResponse['list'] as $resourceUrl) {
            $objects[] = new HistoryItem($resourceUrl);
        }

        $apiResourceModel->_putCachedRequest($url, $objects);

        return $objects;
    }

    public function load($resourceUrl)
    {
        $this->_resourceUrl = $resourceUrl;
        $apiResourceModel = new static;

        $cachedResponse = $apiResourceModel->_getCachedRequest($resourceUrl);
        if ($cachedResponse) {
            $this->setData($cachedResponse);
            return $this;
        }

        $apiResponseData = $apiResourceModel->_request($resourceUrl);
        $apiResourceModel->_putCachedRequest($resourceUrl, $apiResponseData);

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

    protected function _putCachedRequest($apiResourceUrl, $response)
    {
        // The slashes mean something crazy in Stash
        $cacheKey = "api_resource_" . str_replace("/", "_", $apiResourceUrl);

        $item = $this->_getCache()->getItem($cacheKey);

        $hours = 24;
        $minutes = $hours * 60;
        $seconds = $minutes * 60;
        $item->set($response, $seconds);
    }
}