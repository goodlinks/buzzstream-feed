<?php
/**
 * Created by PhpStorm.
 * User: kalenj
 * Date: 2/22/16
 * Time: 8:49 AM
 */

namespace Goodlinks\BuzzStreamFeed;


class ApiUrlBuilder
{
    /** @var string */
    private $baseUrl;

    /**
     * @param $baseUrl string
     */
    public function __construct($baseUrl)
    {
        if (substr($baseUrl, -1) == '/') {
            throw new \InvalidArgumentException("Base URL shouldn't have a trailing slash");
        }

        $this->baseUrl = $baseUrl;
    }

    /**
     * @param $resourceIdentifier string
     * @return string
     */
    public function buildUrl($resourceIdentifier, $offset = 0, $maxResults = 50)
    {
        return $this->baseUrl . '/' . $resourceIdentifier . '?' . http_build_query(array(
            'offset'        => $offset,
            'max_results'   => $maxResults,
        ));
    }
}