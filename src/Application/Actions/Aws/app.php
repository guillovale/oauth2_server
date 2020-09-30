<?php

namespace App\Application\Actions\Aws;
use App\Domain\AwsApp\Aws;
//require __DIR__ . '/vendor/autoload.php';
$path = $_SERVER['HOME'] . '/.aws_php7.json';
$jsonCredentials = file_get_contents($path);
$credentials = json_decode($jsonCredentials, true);
$aws = new Aws($credentials['key'], $credentials['secret']);

$twits = $aws->obtenerUsuarios('neiltyson', 10);
var_dump($twits);

