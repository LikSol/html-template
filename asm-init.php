<?php

use cronfy\asm\Asm;

function D($var = null, $vardump = null) { 'hack: autoload Asm.php --> ' . \cronfy\asm\Asm::$debug; call_user_func_array('\cronfy\asm\D', [$var, $vardump, 2]); }

require_once(__DIR__ . "/vendor/autoload.php");

$environment = require(__DIR__ . '/.env.php');

Asm::setEnvData($environment);

// init yii
define('YII_ENABLE_ERROR_HANDLER', true);
define('YII_DEBUG', Asm::isDebug() ? true : false);
define('YII_ENV',   Asm::isDebug() ? 'dev' : 'prod');
require(Asm::getVendorDir() . '/yiisoft/yii2/Yii.php');

require __DIR__ . "/apps/common/config/_aliases.php";
