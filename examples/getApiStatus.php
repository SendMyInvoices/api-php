<?php
namespace SendMyInvoices;

require_once '../vendor/autoload.php';

//config file

require_once 'inc/config.php';

// Check API status.

$client = new Client(array(
    'apiKey' => SENDMYINVOICES_API_KEY
));
$response = $client->getApiStatus();
print_r($response);
