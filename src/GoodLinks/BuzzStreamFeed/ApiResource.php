<?php

namespace OauthClient;

require_once(dirname(dirname(__DIR__)) . '/OauthClient/Oauth.php');

namespace GoodLinks\BuzzStreamFeed;

abstract class ApiResource
{
    /**
     * Override me
     */
    protected static function _getUrlPath()
    {
        return '/';
    }

    protected static function _getMaxResults()
    {
        return 50;
    }

    public static function getList($offset = 0)
    {
        $object = new static;

        $url = Api::$apiUrl . '/' . $object->_getUrlPath();
        $url .= '?' . http_build_query(array(
            'offset' => $offset,
            'max_results' => self::_getMaxResults(),
        ));

        return $object->_request($url);
    }

    /**
     * @param $apiResourceUrl
     * @return mixed
     * @throws \OAuthException
     * @todo Replace this Oauth class from the BuzzStream docs (https://api.buzzstream.com/docs/api_doc.html#auth) with a newer Oauth component that we can composer in.
     */
    protected static function _request($apiResourceUrl)
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
        return $json_response['list'];
    }
}