<?php

namespace Media;

use Banana\Application;
use Banana\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Routing\RouteBuilder;
use Media\Lib\Media\MediaManager;
use Media\Model\Entity\MediaFile;

class MediaPlugin extends BasePlugin implements EventListenerInterface
{
    protected $_name = "Media";

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
            //'Backend.Menu.build.admin_primary' => ['callable' => 'buildBackendMenu', 'priority' => 90],
            'Backend.Menu.build.admin_primary' => ['callable' => 'buildBackendMenu', 'priority' => 90],
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

        /* @var \Cake\View\View $view */
        $view = $event->subject();
        $view->eventManager()->on('View.beforeRender', function ($ev) {
            $ev->subject()->loadHelper('Media.Media');
            $ev->subject()->loadHelper('Media.MediaPicker');
        });
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->extensions(['json']);

        $routes->connect(
            '/browser/',
            ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree', 'config' => 'default']
        );
        $routes->connect(
            '/browser/:config/',
            ['plugin' => 'Media', 'controller' => 'MediaBrowser', 'action' => 'tree']
        );
        $routes->connect(
            '/browser/:config/:action',
            ['plugin' => 'Media', 'controller' => 'MediaBrowser']
        );

        //$routes->connect('/:controller');
        $routes->fallbacks('DashedRoute');
    }

    public function buildBackendMenu(Event $event, \Banana\Menu\Menu $menu)
    {
        $menu->addItem([
            'title' => 'Media',
            'url' => ['plugin' => 'Media', 'controller' => 'Files', 'action' => 'index'],
            'data-icon' => 'picture-o',
            'children' => [
                'media_upload' => [
                    'title' => 'Upload',
                    'url' => ['plugin' => 'Media', 'controller' => 'Upload', 'action' => 'index'],
                    'data-icon' => 'upload',
                ]
            ]
        ]);
    }

    public function bootstrap(Application $app)
    {
        parent::bootstrap($app);

        MediaManager::config(Configure::read('Media'));
        EventManager::instance()->on($this);
    }
}
