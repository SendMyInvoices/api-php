<?php
namespace SendMyInvoices;

use SendMyInvoices\Exceptions\SendMyInvoicesResponseException;

require_once '../vendor/autoload.php';

// config file

require_once 'inc/config.php';

// Get document for retrieval code

$document_code = 'xxxxxx';
$client = new Client(array(
    'apiKey' => SENDMYINVOICES_API_KEY
));

try {
    $response = $client->getDocument($document_code);
    print_r($response);
} catch (SendMyInvoicesResponseException $e) {
    print $e->getErrorMessage();
}
