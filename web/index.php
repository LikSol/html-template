<?php

require_once (__DIR__ . '/../asm-init.php');

$config = require(__DIR__ . "/../apps/main/config/config.php");

(new \yii\web\Application($config))->run();
