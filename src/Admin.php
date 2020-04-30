<?php
declare(strict_types=1);

namespace Media;

use Admin\Core\BaseAdminPlugin;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\Routing\RouteBuilder;
use Media\Model\Entity\MediaFile;

class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * {@inheritDoc}
     */
    public function routes(RouteBuilder $routes): void
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
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_primary' => ['callable' => 'adminMenuInit', 'priority' => 90],
            'Admin.View.initialize' => 'adminViewInit',

        ];
    }

    /**
     * @param \Cake\Event\Event $event The event.
     * @return void
     */
    public function adminViewInit(Event $event): void
    {
        \Admin\View\Helper\FormatterHelper::register('media_file', function ($val, $extra, $params, $view) {
            if ($val instanceof MediaFile) {
                return $view->Media->thumbnail($val->getFilePath(), ['height' => 50, 'width' => 75]);
            }

            return h($val);
        });

        \Admin\View\Helper\FormatterHelper::register('media_files', function ($val, $extra, $params) {
            return h($val);
        });

        /** @var \Cake\View\View $view */
        $view = $event->getSubject();
        //$view->loadHelper('Media.Media');
        $view->getEventManager()->on('View.beforeRender', function ($ev) {
            $ev->getSubject()->loadHelper('Media.Media');
        });
    }

    /**
     * @param \Cake\Event\Event $event The event.
     * @param \Cupcake\Menu\Menu $menu The menu.
     * @return void
     */
    public function adminMenuInit(Event $event, \Cupcake\Menu\Menu $menu): void
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
