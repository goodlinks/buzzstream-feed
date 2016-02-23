<?php
/**
 * Created by PhpStorm.
 * User: kalenj
 * Date: 2/22/16
 * Time: 8:38 AM
 */

namespace Goodlinks\BuzzStreamFeed;


class ApiUrlBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var ApiUrlBuilder */
    private $builder;

    private $baseUrl = "http://example.com";

    protected function setUp()
    {
        $this->builder = new ApiUrlBuilder($this->baseUrl);
    }

    public function testReturnsUrlForHistoryEndpoint()
    {
        $result = $this->builder->buildUrl('history');
        $this->assertStringStartsWith($this->baseUrl . '/history', $result);
    }

    public function testThrowsExceptionForBaseUrlWithSlash()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ApiUrlBuilder('http://example.com/');
    }

    public function testUsesDefaultOffset()
    {
        $result = $this->builder->buildUrl('history');
        $urlParts = parse_url($result);
        parse_str($urlParts['query'], $queryStringParts);

        $this->assertArrayHasKey('offset', $queryStringParts);
        $this->assertSame('0', $queryStringParts['offset']);
    }

    public function testUsesDefaultMaxResults()
    {
        $result = $this->builder->buildUrl('history');
        $urlParts = parse_url($result);
        parse_str($urlParts['query'], $queryStringParts);

        $this->assertArrayHasKey('max_results', $queryStringParts);
        $this->assertSame('50', $queryStringParts['max_results']);
    }
}