<?php

namespace Media;

use Backend\Event\RouteBuilderEvent;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\Router;
use Media\Lib\Media\MediaManager;
use Media\Model\Entity\MediaFile;

class MediaPlugin implements EventListenerInterface
{

    /**
     * Returns a list of events this object is implementing. When the class is registered
     * in an event manager, each individual method will be associated with the respective event.
     *
     * @see EventListenerInterface::implementedEvents()
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     */
    public function implementedEvents()
    {
        return [
            //'Backend.Menu.build' => ['callable' => 'buildBackendMenu', 'priority' => 90],
            'Backend.Sidebar.build' => ['callable' => 'buildBackendMenu', 'priority' => 90],
            'Backend.View.initialize' => 'initializeBackendView',
            'Backend.Routes.build' => 'buildBackendRoutes'

        ];
    }

    public function initializeBackendView(Event $event)
    {
        \Backend\View\Helper\FormatterHelper::register('media_file', function ($val, $extra, $params, $view) {
            if ($val instanceof MediaFile) {
                return $view->Media->thumbnail($val->getFilePath(), ['height' => 50, 'width' => 75]);
            }
            return h($val);
        });

        \Backend\View\Helper\FormatterHelper::register('media_files', function ($val, $extra, $params) {
            return h($val);
        });

        $event->subject()->loadHelper('Media.Media');
        $event->subject()->loadHelper('Media.MediaPicker');
    }

    public function buildBackendRoutes(RouteBuilderEvent $event)
    {
        $event->subject()->scope(
            '/media',
            ['plugin' => 'Media', 'prefix' => 'admin', '_namePrefix' => 'media:admin:'],
            function ($routes) {

                $routes->extensions(['json']);

                $routes->connect('/browser/',
                    ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree', 'config' => 'default']
                );
                $routes->connect('/browser/:config/',
                    ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree']
                );
                $routes->connect('/browser/:config/:action',
                    ['plugin' => 'Media', 'controller' => 'MediaBrowser']
                );

                //$routes->connect('/:controller');
                $routes->fallbacks('DashedRoute');
            }
        );
    }

    public function buildBackendMenu(Event $event)
    {
        $event->subject()->addItem([
            'title' => 'Media',
            'url' => ['plugin' => 'Media', 'controller' => 'Media', 'action' => 'index'],
            'data-icon' => 'picture-o',
            'children' => [
                'media_files' => [
                    'title' => 'Files',
                    'url' => ['plugin' => 'Media', 'controller' => 'Media', 'action' => 'index'],
                    'data-icon' => 'picture-o',
                ],
                'media_upload' => [
                    'title' => 'Upload',
                    'url' => ['plugin' => 'Media', 'controller' => 'Upload', 'action' => 'index'],
                    'data-icon' => 'upload',
                ]
            ]
        ]);
    }

    public function __invoke()
    {
        MediaManager::config(Configure::read('Media'));
    }
}
