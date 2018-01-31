<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 26.07.17
 * Time: 13:45
 */

/**
 * Aliases for all applications.
 *
 * They may be rquired prior to application initialization (e. g. to get aplication
 * config by `@app_name/config/config.php`).
 *
 * By this reason we define them in separate file and can include it before application
 * initialization.
 */

// Resolve root dynamically, because console tasks such as
//    ./yii migrate
// can be run while deploying, but before project is moved to real project root
Yii::setAlias('@root', dirname(dirname(dirname(__DIR__))));

// core
Yii::setAlias('@var', '@root/var');
Yii::setAlias('@tmp', '@root/tmp');
Yii::setAlias('@log', '@root/log');
Yii::setAlias('@apps', '@root/apps');

// yii apps
Yii::setAlias('@common', '@apps/common');
Yii::setAlias('@main', '@apps/main');

// other
Yii::setAlias('@data', '@var/data');
