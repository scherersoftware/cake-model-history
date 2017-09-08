<?php

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Database\Type;

Configure::write('debug', true);
/**
 * Test suite bootstrap for ModelHistory.
 */
// Customize this to be a relative path for embedded plugins.
// For standalone plugins, this should point at a CakePHP installation.
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}
define('APP', 'tests' . DS . 'ModelHistoryTestApp' . DS);
define('CONFIG', __DIR__ . DS . '..' . DS . 'config' . DS);

$vendorPos = strpos(__DIR__, 'vendor/codekanzlei/cake-model-history');
if ($vendorPos !== false) {
    // Package has been cloned within another composer package, resolve path to autoloader
    $vendorDir = substr(__DIR__, 0, $vendorPos) . 'vendor/';
    $loader = require $vendorDir . 'autoload.php';
} else {
    // Package itself (cloned standalone)
    $loader = require __DIR__ . '/../vendor/autoload.php';
}

require_once 'vendor/cakephp/cakephp/src/basics.php';

Cake\Datasource\ConnectionManager::config('test', [
    'className' => 'Cake\Database\Connection',
    'driver' => 'Cake\Database\Driver\Mysql',
    'persistent' => false,
    'host' => 'localhost',
    'username' => 'root',
    'password' => '',
    'database' => 'model_history_test',
    'encoding' => 'utf8',
    'timezone' => 'UTC'
]);

Cache::config('_cake_core_', [
    'className' => 'File',
    'prefix' => 'mh_cake_core_',
    'path' => 'cache/persistent/',
    'serialize' => true,
    'duration' => '+2 minutes',
]);
Type::map('json', '\CkTools\Database\Type\JsonType');
