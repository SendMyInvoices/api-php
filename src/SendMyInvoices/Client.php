<?php

namespace SendMyInvoices;

use SendMyInvoices\Exceptions\SendMyInvoicesResponseException;

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
     * Lang Code
     * Allowed options en_us, de_de. default en_us
     * This is used to use either fetchbill.com or belegsuche.de in qr-code generation
     * @var string
     */
    private $langCode = '';
    
    /**
     * Request Timeout
     * @var Integer
     */
    private $timeout = 60;
    
    /**
     * SSL Verify
     * @var Boolean
     */
    private $sslVerify = false;
    
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
        
        if (!empty($params['langCode'])) {
            $this->langCode = $params['langCode'];
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
     * @param        $endpoint
     * @param string $request_type
     * @param array  $param
     *
     * @return string $response
     * @throws SendMyInvoicesResponseException
     */
    public function request($endpoint, string $request_type = 'POST', array $param = array()): string
    {
        try {
            $result = $this->httpClient->request($request_type, $this->getURL().$endpoint, [
                'verify'      => $this->sslVerify,
                'json'        => $param,
                'http_errors' => false,
                'timeout'     => $this->timeout,
                'headers'     => [
                    'Accept'       => 'application/json',
                    'Content-type' => 'application/json',
                    'X-API-KEY'    => $this->apiKey,
                    'X-LANG-CODE'  => $this->langCode
                ]
            ]);
            
            if ($result->getStatusCode() !== 200) {
                $json_response = $result->getBody()->getContents();
                $response_data = !empty($json_response) ? json_decode($json_response, true) : array();
                throw new SendMyInvoicesResponseException($response_data, $result->getStatusCode());
            } else {
                return $result->getBody()->getContents();
            }
        } catch (GuzzleException $e) {
            return $e->getMessage();
        }
    }
    
    /**
     * Method apiStatus
     * Check API status
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function apiStatus(): string
    {
        return $this->request('apiStatus', 'GET');
    }
    
    /**
     * Method getCode
     * Get Document code with or without QR code, before uploading document
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function getCode(): string
    {
        return $this->request('getCode', 'POST', array(
            'QRCode' => true
        ));
    }
    
    /**
     * Method getDocument
     * Get one document from the account.
     *
     * @param $document_code
     *
     * @return string
     * @throws SendMyInvoicesResponseException
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
     * @throws SendMyInvoicesResponseException
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
     * @throws SendMyInvoicesResponseException
     */
    public function deleteDocument($document_code): string
    {
        return $this->request('documents/'.$document_code, 'DELETE');
    }
}
