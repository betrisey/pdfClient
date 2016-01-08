# HTMLtoPDF Client
PHP client for Axianet's HTMLtoPDF service
## Installation
``` bash
composer require betrisey/pdf-client
```
## Usage
``` php
<?php
require_once 'vendor/autoload.php';
use Axianet\pdfConverter\pdfClient;

$pdf = new pdfClient([
	'url' => 'http://localhost/htmltopdf/',
	'authUrl' => 'http://localhost/htmltopdf/auth/token.php',
	'clientId' => '        ',
	'clientSecret' => '        '
]);

$pdf->fromFile('file.html');
$pdf->fromString('<h1>Content</h1>');
$pdf->fromUrl('google.com');
```

### Send email
``` php
$pdf->fromFile('file.html', 'samuel@axianet.ch', 'Suject', 'Content');
$pdf->fromFile('file.html', ['samuel@axianet.ch', 'info@axianet.ch'], 'Suject', 'Content');
```