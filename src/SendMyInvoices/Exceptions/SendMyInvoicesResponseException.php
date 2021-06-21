<?php
namespace SendMyInvoices\Exceptions;

use GuzzleHttp\Exception\ClientException;
use Throwable;

/**
 * Class SendMyInvoicesResponseException
 * @package SendMyInvoices\Exceptions
 */
class SendMyInvoicesResponseException extends SendMyInvoicesRestException
{
    /**
     * @var array Decoded response
     */
    protected $responseData;
    
    /**
     * @var integer http status code
     */
    protected $statusCode;

    /**
     * ResponseException constructor.
     *
     * @param array          $responseData
     * @param integer        $statusCode
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct($responseData, $statusCode, $message = '', int $code = 0, Throwable $previous = null)
    {
        $this->responseData = $responseData;
        $this->statusCode = $statusCode;
        parent::__construct($message, $code, $this->getException($this->getErrorMessage()));
    }

    /**
     * Retrieves the error message from the response
     * @return string
     */
    public function getErrorMessage(): string
    {
        if (isset($this->responseData['success'], $this->responseData['detail']) && (bool)$this->responseData['success'] === false) {
            return $this->responseData['detail'];
        }
    
        return '';
    }

    /**
     * Returns an exceptions with a message based on the status code
     *
     * @param string $message
     *
     * @return SendMyInvoicesValidationException | SendMyInvoicesAuthenticationException | SendMyInvoicesInvalidContentTypeException | SendMyInvoicesServerException | SendMyInvoicesRequestException | SendMyInvoicesRestException
     */
    public function getException(string $message)
    {
        // make exception based on the status code
        switch ($this->statusCode) {
            case 400:
                return
                    $message?
                        new SendMyInvoicesValidationException($message):
                        new SendMyInvoicesValidationException(
                            'A parameter is missing or invalid endpoint');
            case 403:
                return
                    $message?
                        new SendMyInvoicesAuthenticationException($message):
                        new SendMyInvoicesAuthenticationException('Failed to authenticate while accessing resource');
            case 415:
                return
                    $message?
                        new SendMyInvoicesInvalidContentTypeException($message):
                        new SendMyInvoicesInvalidContentTypeException('Request content-type is not valid.');
            case 405:
                return
                    $message?
                        new SendMyInvoicesRequestException($message):
                        new SendMyInvoicesRequestException('HTTP method used is not allowed to access resource');
            case 503:
                return
                    $message?
                        new SendMyInvoicesServerException($message):
                        new SendMyInvoicesServerException('A server error occurred while accessing resource');
            default:
                return new SendMyInvoicesRestException(json_encode($this->responseData));
        }

    }
}
