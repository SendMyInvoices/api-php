<?php

namespace SendMyInvoices;

use SendMyInvoices\Exceptions\SendMyInvoicesRestException;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\Exception\GuzzleException;

/**
 * Class Client
 * @package SendMyInvoices
 */
class Client
{
    
    /**
     * API Base Url
     * @var string
     */
    private $url = 'https://api.sendmyinvoices.com/';
    
    /**
     * API Key
     * @var string
     */
    private $apiKey = '';
    
    /**
     * API Version
     * @var string
     */
    private $apiVersion = 'v1';
    
    /**
     * Request Timeout
     * @var Integer
     */
    private $timeout = 60;
    
    /**
     * SSL Verify
     * @var Boolean
     */
    private $sslVerify = true;
    
    /** @var HttpClient as $httpClient */
    private $httpClient;
    
    public function __construct($params = array())
    {
        $this->httpClient = new HttpClient();
        
        if (!empty($params['apiKey'])) {
            $this->apiKey = $params['apiKey'];
        }
        
        if (!empty($params['apiVersion'])) {
            $this->apiVersion = $params['apiVersion'];
        }
        
        if (!empty($params['sslVerify'])) {
            $this->sslVerify = (bool)$params['sslVerify'];
        }
        
        if (!empty($params['timeout'])) {
            $this->timeout = (int)$params['timeout'];
        }
    }
    
    /**
     *  Get the endpoint url
     *
     * @return string
     */
    public function getURL(): string
    {
        return $this->url.$this->apiVersion.'/';
    }
    
    /**
     *  Make a network request
     *
     * @param $endpoint
     * @param string $request_type
     * @param array $param
     *
     * @return string $response
     */
    public function request($endpoint, string $request_type='POST', array $param=array()): string
    {
        try {
            $result = $this->httpClient->request($request_type, $this->getURL().$endpoint, [
                'verify'  => $this->sslVerify,
                'json'    => json_encode($param),
                'timeout' => $this->timeout,
                'headers' => [
                    'Accept'       => 'application/json',
                    'Content-type' => 'application/json',
                    'X-API-KEY'    => $this->apiKey
                ]
            ]);
            
            return $result->getBody()->getContents();
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Method getApiStatus
     * Check API status
     *
     * @return string
     * @throws GuzzleException
     */
    public function getApiStatus(): string
    {
        return $this->request('apiStatus');
    }
    
    /**
     * Method getDocument
     * Get one document from the account.
     *
     * @param $document_code
     *
     * @return string
     * @throws GuzzleException
     */
    public function getDocument($document_code): string
    {
        return $this->request('documents/'.$document_code, 'GET');
    }
    
    /**
     * Method uploadDocument
     * Upload a new document. Supported file types: pdf
     *
     * @param array $param
     *
     * @return string
     * @throws GuzzleException
     */
    public function uploadDocument(array $param): string
    {
        return $this->request('documents', 'POST', $param);
    }
    
    /**
     * Method deleteDocument
     * Delete one document from the account.
     *
     * @param $document_code
     *
     * @return string
     * @throws GuzzleException
     */
    public function deleteDocument($document_code): string
    {
        return $this->request('documents/'.$document_code, 'DELETE');
    }
}
