<?php
/**
 * Created by PhpStorm.
 * User: khoalengoc
 * Date: 6/7/16
 * Time: 11:04 AM
 */

namespace VietnamWorks;

use Http\Client\HttpClient;


/**
 * This class implement api call of VietnamWorks
 * Class VnwApi
 * @package VNW
 */
class VnwRestAPI
{
    /**
     * @var RestClient
     */
    protected $restClient;

    /**
     * @var string
     */
    protected $apiKey;

    /**
     * @var string
     */
    protected $apiSecret;


    public function __construct($apiKey, $apiSecret,$apiHost ="dev.webservice.vietnamworks.com" ,HttpClient $httpClient = null)
    {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->restClient = new VnwRestClient($apiKey, $apiSecret, $apiHost, $httpClient);
    }

    public function getToken(){

    }
}