<?php
/**
 * Created by PhpStorm.
 * User: cronfy
 * Date: 17.02.17
 * Time: 18:21
 */

return [
    // By default Yii sets @vendor depending on basePath of application. When applications
    // reside in /apps/APP_NAME/, it will set vendorPath as /apps/APP_NAME/vendor, which is
    // incorrect.
    // Let's force configure it by Asm envronment.
    'vendorPath' => \cronfy\asm\Asm::getVendorDir(),

    // These aliases will be redefined on application initialization
    // (e. g. `new yii\web\Application($config)`).
    // By this reason they should be defined in application configuration,
    // and not in `@common/config/_aliases.php`.
    'aliases' => [
        '@runtime' => '@tmp/runtime',
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],

    // By default Yii sets @webroot depending on entry php script (as dirname() of it).
    // Let's force configure it by .env
    'extensions' => [
        'asm' => [
            'alias' => [
                '@webroot' => \cronfy\asm\Asm::getEnv('WEB_ROOT'),
            ]
        ]
    ],

];
