<?php
namespace SendMyInvoices;

require_once '../vendor/autoload.php';

// config file

require_once 'inc/config.php';

// Get document for retrieval code

$document_code = 'xa19da';
$client = new Client(array(
    'apiKey' => SENDMYINVOICES_API_KEY
));

$response = $client->getDocument($document_code);
print_r($response);
