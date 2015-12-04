# HTMLtoPDF Client
## Installation
``` bash
composer require betrisey/pdf-client
```
## Utilisation
``` php
<?php
require_once 'vendor/autoload.php';
use Axianet\pdfClient\pdfClient;

$pdf = new pdfClient([
	'url' => 'http://localhost/htmltopdf/',
	'authUrl' => 'http://localhost/htmltopdf/auth/token.php',
	'clientId' => '        ',
	'clientSecret' => '        '
]);

$pdf->fromFile('file.html');
$pdf->fromString('&lt;h1&gt;Content&lt;/h1&gt;');
$pdf->fromUrl('google.com');
```
