<?php
/**
 * Convert all errors to exceptions
 */

use \Utils\DummyLogger;

error_reporting(E_ALL);

DEFINE('FOLDER_DATA', __DIR__ . '/../data');
DEFINE('DATE_FORMAT', 'Y-m-d');
DEFINE('TIME_FORMAT', 'H:i:s');
DEFINE('DATETIME_FORMAT', DATE_FORMAT . ' ' . TIME_FORMAT);
DEFINE('LOG_ID', rand(10000, 99999));

// @TODO in case of error, show some info about renaming config.local.example.php to config.local.php
require_once FOLDER_DATA . '/config.local.php';

require_once __DIR__ . '/../vendor/autoload.php';

Tracy\Debugger::enable(DEVELOPMENT_IPS, FOLDER_DATA . '/tracy-log/');
Tracy\Debugger::$strictMode = true;
Tracy\Debugger::$logSeverity = E_ALL;

/**
 * @param $className
 * @throws Exception
 */
function dummyAutoloader($className) {
	$path = str_replace('\\', '/', $className);
	$file = str_replace('\\', '/', __DIR__) . '/libs/' . $path . '.php';
	if (file_exists($file)) {
		require_once $file;
	} else {
		throw new \Exception(sprintf('Class "%s" cannot be loaded, file "%s" does not exists.', $path, $file));
	}
}
spl_autoload_register('dummyAutoloader');

// @TODO this is a lot of data to log but it's ok for alpha/beta phase. Probably should be removed in stable.
DummyLogger::log(DummyLogger::NAME_ALL_REQUESTS, $_SERVER);
