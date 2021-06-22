<?php
namespace SendMyInvoices;

use SendMyInvoices\Exceptions\SendMyInvoicesResponseException;

require_once '../vendor/autoload.php';

//config file

require_once 'inc/config.php';

// Check API status.

$client = new Client(array(
    'apiKey' => SENDMYINVOICES_API_KEY
));
try {
    $response = $client->apiStatus();
    print_r($response);
} catch (SendMyInvoicesResponseException $e) {
    print $e->getErrorMessage();
}
