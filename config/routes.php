<?php
declare(strict_types = 1);
use Cake\Routing\Router;

Router::plugin('ModelHistory', [], function ($routes): void {
    $routes->fallbacks('DashedRoute');
});
