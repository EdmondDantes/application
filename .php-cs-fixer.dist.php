<?php

$config = new IfCastle\CodeStyle\Config;
$config->getFinder()
       ->in(__DIR__ . '/src')
       ->in(__DIR__ . '/tests');

$config->setCacheFile(__DIR__ . '/.php_cs.cache');

return $config;