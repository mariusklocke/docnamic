<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Docnamic\Renderer;

$renderer = new Renderer();
$renderer
    ->loadTemplate(__DIR__ . '/invoice.odt')
    ->setData(json_decode(file_get_contents(__DIR__ . '/invoice.json'), true))
    ->render(__DIR__ . '/result.odt');
