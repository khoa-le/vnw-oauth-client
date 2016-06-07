<?php
/**
 * Created by PhpStorm.
 * User: khoalengoc
 * Date: 6/7/16
 * Time: 11:39 AM
 */

namespace VietnamWorks;


use GuzzleHttp\Psr7\Request;
use Http\Discovery\HttpClientDiscovery;

class VnwRestClient
{
    protected $httpClient;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;

    /**
     * @var string
     */
    protected $apiHost;

    /**
     * The version of the API to use
     * @var string
     */
    protected $apiVersion = 'v2';

    /**
     * If we should use SSL or not
     * @var bool
     */
    protected $sslEnabled = true;

    public function __construct($apiKey, $apiSecret,$apiHost, HttpClient $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->apiHost = $apiHost;
        $this->httpClient = new $httpClient;
    }

    public function send($method, $uri, $body, $headers)
    {
        $request = new Request($method, $this->getApiUrl($uri), $headers, $body);
        $response = $this->getHttpClient()->sendRequest($request);
        var_dump($response);die;
    }

    /**
     *
     * @return HttpClient
     */
    protected function getHttpClient()
    {
        if ($this->httpClient === null) {
            $this->httpClient = HttpClientDiscovery::find();
        }

        return $this->httpClient;
    }

    /**
     * @param mixed $httpClient
     */
    public function setHttpClient($httpClient)
    {
        $this->httpClient = $httpClient;
    }


    /**
     * @param $uri
     *
     * @return string
     */
    private function getApiUrl($uri)
    {
        return $this->generateEndpoint($this->apiHost, $this->apiVersion, $this->sslEnabled).$uri;
    }


    /**
     * @param string $apiEndpoint
     * @param string $apiVersion
     * @param bool   $ssl
     *
     * @return string
     */
    private function generateEndpoint($apiEndpoint, $apiVersion, $ssl)
    {
        if (!$ssl) {
            return 'http://'.$apiEndpoint.'/'.$apiVersion.'/';
        } else {
            return 'https://'.$apiEndpoint.'/'.$apiVersion.'/';
        }
    }

    /**
     * @param string $apiVersion
     *
     * @return RestClient
     */
    public function setApiVersion($apiVersion)
    {
        $this->apiVersion = $apiVersion;

        return $this;
    }

    /**
     * @param boolean $sslEnabled
     *
     * @return RestClient
     */
    public function setSslEnabled($sslEnabled)
    {
        $this->sslEnabled = $sslEnabled;

        return $this;
    }

}