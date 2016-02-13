<?php
use Cake\Routing\Router;

Router::plugin('Media', function ($routes) {
    $routes->prefix('admin', function ($routes) {

        $routes->extensions(['json']);

        $routes->connect('/browser/', ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree', 'config' => 'default']);
        $routes->connect('/browser/:config/', ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree']);
        $routes->connect('/browser/:config/:action', ['plugin' => 'Media', 'controller' => 'MediaBrowser']);

        $routes->connect('/:controller');
        $routes->fallbacks('DashedRoute');
    });
});
