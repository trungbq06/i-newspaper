<?php
date_default_timezone_set('Asia/Saigon');
define('_APP_PATH_', dirname(__FILE__));
$yiic=dirname(__FILE__).'/../yii-1.1.8.r3324/framework/yii.php';
$config=dirname(__FILE__).'/protected/config/console.php';

// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yiic);
// creating and running console application
Yii::createConsoleApplication($config)->run();
