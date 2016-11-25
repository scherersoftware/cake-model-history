<?php
// /**
//  * Test suite bootstrap for ModelHistory.
//  *
//  * This function is used to find the location of CakePHP whether CakePHP
//  * has been installed as a dependency of the plugin, or the plugin is itself
//  * installed as a dependency of an application.
//  */
// $findRoot = function ($root) {
//     do {
//         $lastRoot = $root;
//         $root = dirname($root);
//         if (is_dir($root . '/vendor/cakephp/cakephp')) {
//             return $root;
//         }
//     } while ($root !== $lastRoot);
//
//     throw new Exception("Cannot find the root of the application, unable to run tests");
// };
// $root = $findRoot(__FILE__);
// unset($findRoot);
//
// chdir($root);
// require $root . '/config/bootstrap.php';

use Cake\Cache\Cache;
use Cake\Core\Configure;

Configure::write('debug', true);
/**
 * Test suite bootstrap for ModelHistory.
 */
// Customize this to be a relative path for embedded plugins.
// For standalone plugins, this should point at a CakePHP installation.
$vendorPos = strpos(__DIR__, 'vendor/codekanzlei/cake-model-history');
if ($vendorPos !== false) {
    // Package has been cloned within another composer package, resolve path to autoloader
    $vendorDir = substr(__DIR__, 0, $vendorPos) . 'vendor/';
    $loader = require $vendorDir . 'autoload.php';
} else {
    // Package itself (cloned standalone)
    $loader = require __DIR__ . '/../vendor/autoload.php';
}

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
