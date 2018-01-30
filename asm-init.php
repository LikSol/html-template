<?php

use cronfy\env\Env;

function D($var = null, $vardump = null) {call_user_func_array('\cronfy\debug\Debug::D', [$var, $vardump, (PHP_VERSION_ID >= 70000) ? 1 : 2]);}
function E($var = null, $vardump = null) { call_user_func_array('\cronfy\debug\Debug::E', [$var, $vardump, (PHP_VERSION_ID >= 70000) ? 1 : 2]); }

require_once(__DIR__ . "/vendor/autoload.php");

$environment = require(__DIR__ . '/.env.php');

Env::load($environment);
Env::isDebug(Env::get('ENVIRONMENT') == 'dev');

\cronfy\debug\Debug::$debug = Env::isDebug();

define('YII_ENABLE_ERROR_HANDLER', true);
define('YII_DEBUG', Env::isDebug() ? true : false);
define('YII_ENV',   Env::isDebug() ? 'dev' : 'prod');
require(__DIR__ . '/vendor/yiisoft/yii2/Yii.php');

require __DIR__ . "/apps/common/config/_aliases.php";
