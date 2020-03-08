<?php

namespace Media;

use Banana\Plugin\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\Type;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Cake\Routing\RouteBuilder;
use Media\Lib\Media\MediaManager;
use Media\Model\Entity\MediaFile;

class Plugin extends BasePlugin implements EventListenerInterface
{
    public function bootstrap(PluginApplicationInterface $app)
    {
        parent::bootstrap($app);

        /**
         * Load Media plugin configuration
         */
        Configure::load('Media.media');

        // Register MediaFileType
        Type::map('media_file', 'Media\Database\Type\MediaFileType');

        if (!Log::getConfig('media')) {
            Log::setConfig('media', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'media',
                'scopes' => ['media'],
            ]);
        }

        foreach ((array)Configure::read('Media') as $key => $config) {
            if (!MediaManager::getConfig($key)) {
                MediaManager::setConfig($key, $config);
            }
        }
        EventManager::instance()->on($this);
    }

    public function backendRoutes(RouteBuilder $routes)
    {
        $routes->setExtensions(['json']);

        $routes->connect(
            '/',
            ['plugin' => 'Media', 'controller' => 'Files', 'action' => 'index']
        );
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

        $routes->fallbacks('DashedRoute');
    }

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
            'Backend.Menu.build.admin_primary' => ['callable' => 'backendMenuInit', 'priority' => 90],
            'Backend.View.initialize' => 'backendViewInit',

        ];
    }

    public function backendViewInit(Event $event)
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
        $view = $event->getSubject();
        //$view->loadHelper('Media.Media');
        $view->getEventManager()->on('View.beforeRender', function ($ev) {
            $ev->getSubject()->loadHelper('Media.Media');
        });
    }

    public function backendMenuInit(Event $event, \Banana\Menu\Menu $menu)
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
                ],
            ],
        ]);
    }
}
