<?php
namespace SendMyInvoices;

use SendMyInvoices\Exceptions\SendMyInvoicesResponseException;

require_once '../vendor/autoload.php';

// config file

require_once 'inc/config.php';


// Upload a new document to the account. Supported file types: pdf


// parameters

$param = array();

/*
* @param fileName required (string)
* Name of the file with extension.
*/
$param['fileName'] = 'sample_doc.pdf';

/*
* @param fileContent required (string)
* File content; base64 encoded
*/

$file = file_get_contents('file/Bauverein.pdf');

$param['fileContent'] = base64_encode($file); /* base64 encoded file content */

/*
* @param QRCode optional (boolean)
* Whether needed QRcode also.
*/
$param['QRCode'] = true;

$client = new Client(array(
    'apiKey' => SENDMYINVOICES_API_KEY
));
try {
    $response = $client->uploadDocument($param);
    print_r($response);
} catch (SendMyInvoicesResponseException $e) {
    print $e->getErrorMessage();
}
