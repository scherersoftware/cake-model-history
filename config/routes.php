<?php
use Cake\Routing\Router;

Router::plugin('ModelHistory', function ($routes) {
    $routes->fallbacks();
});
