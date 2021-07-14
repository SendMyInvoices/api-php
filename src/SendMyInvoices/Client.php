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
            $headers = [
                'Accept'       => 'application/json',
                'Content-type' => 'application/json',
                'X-API-KEY'    => $this->apiKey
            ];
            if(isset($param['headers'])) {
                if(is_array($param['headers'])) {
                    $headers = array_merge($headers, $param['headers']);
                }
                unset($param['headers']);
            }
            $result = $this->httpClient->request($request_type, $this->getURL().$endpoint, [
                'verify'      => $this->sslVerify,
                'json'        => $param,
                'http_errors' => false,
                'timeout'     => $this->timeout,
                'headers'     => $headers
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
     * @param bool   $qrCode
     * @param string $domain
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function getCode(bool $qrCode = true, string $domain = ''): string
    {
        return $this->request('getCode', 'POST', array(
            'domain' => $domain,
            'qrCode' => $qrCode
        ));
    }
    
    /**
     * Method getQrCode
     * Get QR code of existing document code
     *
     * @param string $documentCode
     * @param string $domain
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function getQrCode(string $documentCode, string $domain = ''): string
    {
        return $this->request('getCode', 'POST', array(
            'qrCode'       => true,
            'domain'       => $domain,
            'documentCode' => $documentCode
        ));
    }
    
    /**
     * Method getDocument
     * Get one document from the account.
     *
     * @param       $document_code
     * @param array $security_params
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function getDocument($document_code, array $security_params=array()): string
    {
        return $this->request('documents/'.$document_code, 'GET', array(
            'headers' => [
                'X-SECURITY-CODE-1' => $security_params['security-code-1'] ?? '',
                'X-SECURITY-CODE-2' => $security_params['security-code-2'] ?? ''
            ]
        ));
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
     * @param $document_id
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function deleteDocument($document_id): string
    {
        return $this->request('documents/'.$document_id, 'DELETE');
    }
    
    /**
     * Method uploadAttachment
     * Upload a new attachment.
     *
     * @param string $document_id
     * @param array  $param
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function uploadAttachment(string $document_id, array $param): string
    {
        return $this->request('documents/'.$document_id.'/attachments', 'POST', $param);
    }
    
    /**
     * Method getAttachment
     * Get single attachment from document.
     *
     * @param       $document_code
     * @param       $attachmentUid
     * @param array $security_params
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function getAttachment($document_code, $attachmentUid, array $security_params=array()): string
    {
        return $this->request('documents/'.$document_code.'/attachments/'.$attachmentUid, 'GET', array(
            'headers' => [
                'X-SECURITY-CODE-1' => $security_params['security-code-1'] ?? '',
                'X-SECURITY-CODE-2' => $security_params['security-code-2'] ?? ''
            ]
        ));
    }
    
    /**
     * Method deleteAttachment
     * Delete one attachment of a document.
     *
     * @param $document_id
     * @param $attachment_id
     *
     * @return string
     * @throws SendMyInvoicesResponseException
     */
    public function deleteAttachment($document_id, $attachment_id): string
    {
        return $this->request('documents/'.$document_id.'/attachments/'.$attachment_id, 'DELETE');
    }
}
