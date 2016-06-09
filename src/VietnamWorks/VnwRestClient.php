<?php
/**
 * Created by PhpStorm.
 * User: khoalengoc
 * Date: 6/7/16
 * Time: 11:39 AM
 */

namespace VietnamWorks;


use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Request;
use Http\Client\HttpClient;
use Http\Discovery\HttpClientDiscovery;
use Psr\Http\Message\ResponseInterface;

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
    protected $sslEnabled = false;

    protected $accessToken = null;

    public function __construct($apiKey, $apiSecret, $apiHost, HttpClient $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->apiHost = $apiHost;
        $this->httpClient = $httpClient;
    }

    public function send($method, $uri, $accessToken='', $body = null, $headers = array(),$files=array())
    {
        if (!empty($accessToken)) {
            $headers['Authorization'] = 'Bearer '. $accessToken;
        }

        if (!empty($files)) {
            $body = new MultipartStream($files);
            $headers['Content-Type'] = 'multipart/form-data; boundary='.$body->getBoundary();
        }

        $request = new Request($method, $this->getApiUrl($uri), $headers, $body);
        $response = $this->getHttpClient()->sendRequest($request);

        return $this->responseHandler($response);
    }

    public function get($endpointUrl, $accessToken = '' ,$queryString = array())
    {
        return $this->send('GET',$endpointUrl.'?'.http_build_query($queryString) , $accessToken);
    }

    public function post($endpointUrl, $accessToken, $postData = array(), $files = array())
    {
        $postDataMultipart = [];
        $postDataMultipart[]=array(
            'name'=>'json',
            'contents'=>json_encode($postData)
        );


        $body['json'] = json_encode($postData);
        if (!empty($files)) {
            $postDataMultipart[]=array(
                'name'=>$files['name'],
                'contents'=> fopen($files['file'], 'r'),
                'filename'=>$files['file_name']
            );
        }

        return $this->send('POST', $endpointUrl, $accessToken, [],[],$postDataMultipart);
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
     * @param bool $ssl
     *
     * @return string
     */
    private function generateEndpoint($apiEndpoint, $apiVersion, $ssl)
    {
        if (!$ssl) {
            return 'http://'.$apiEndpoint.'/';
        } else {
            return 'https://'.$apiEndpoint.'/';
        }
    }


    private function responseHandler(ResponseInterface $response)
    {
        $httpResponseCode = $response->getStatusCode();
        $result = new \stdClass();
        $data = (string)$response->getBody();
        $jsonResponseData = json_decode($data, false);

        // return response data as json if possible, raw if not
        $result->http_response_body = $data && $jsonResponseData === null ? $data : $jsonResponseData;
        $result->http_response_code = $httpResponseCode;

        return $result;
    }

}