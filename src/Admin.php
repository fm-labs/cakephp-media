<?php
declare(strict_types=1);

namespace Media;

use Admin\Core\BaseAdminPlugin;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Event\EventListenerInterface;
use Cake\Routing\RouteBuilder;
use Cupcake\Health\HealthStatus;
use Media\Model\Entity\MediaFile;

/**
 * Class Admin
 *
 * @package Media
 */
class Admin extends BaseAdminPlugin implements EventListenerInterface
{
    /**
     * @inheritDoc
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
     * @return array associative array or event key names pointing to the function
     * that should be called in the object when the respective event is fired
     * @see EventListenerInterface::implementedEvents()
     */
    public function implementedEvents(): array
    {
        return [
            'Admin.Menu.build.admin_primary' => ['callable' => 'adminMenuInit', 'priority' => 90],
            'Admin.View.initialize' => 'adminViewInit',
            'Health.beforeCheck' => 'healthBeforeCheck',
        ];
    }

    /**
     * @param \Cake\Event\EventInterface $event The event
     * @return void
     */
    public function healthBeforeCheck(EventInterface $event): void
    {
        /** @var \Cupcake\Health\HealthManager $hm */
        $hm = $event->getSubject();
        $hm->addCheck('media_data_dir', function () {
            if (!defined('MEDIA')) {
                return HealthStatus::crit('Media data directory is not defined');
            }

            if (!file_exists(MEDIA) || !is_dir(MEDIA)) {
                return HealthStatus::crit('Media data directory not found');
            }

            if (!defined('MEDIA_URL')) {
                return HealthStatus::crit('Media url is not defined');
            }

            return HealthStatus::ok('Media data directory exists');
        });
        $hm->addCheck('media_upload_dir', function () {
            //if (!defined('MEDIA_UPLOAD_DIR')) {
            //    return HealthStatus::crit('Media upload directory is not defined');
            //}
            $uploadDir = MEDIA . 'uploads' . DS;
            if (!file_exists($uploadDir) || !is_dir($uploadDir) || !is_writable($uploadDir)) {
                return HealthStatus::crit(__('Media upload directory not found at {0} or not writeable', $uploadDir));
            }

            return HealthStatus::ok('Media upload directory exists and is writeable');
        });
        $hm->addCheck('media_cache_dir', function () {
            if (!defined('MEDIA_CACHE_DIR')) {
                return HealthStatus::crit('Media cache directory is not defined');
            }
            $cacheDir = MEDIA_CACHE_DIR;
            if (!file_exists($cacheDir) || !is_dir($cacheDir) || !is_writable($cacheDir)) {
                return HealthStatus::crit(__('Media cache directory not found at {0} or not writeable', $cacheDir));
            }
            if (!defined('MEDIA_CACHE_URL')) {
                return HealthStatus::crit('Media cache url is not defined');
            }

            return HealthStatus::ok('Media cache directory exists and is writeable');
        });
        $hm->addCheck('media_paths', function () {
            $mediaWwwRoot = WWW_ROOT . 'media';
            $realWwwRoot = realpath($mediaWwwRoot);
            if (!$realWwwRoot) {
                return HealthStatus::warn("Media PUBLIC data directory does not exist.\nCreate a symlink to webroot/media or move MEDIA dir to webroot for better performance!");
            }

            // check if the media data dir is within webroot
            if (substr(MEDIA, 0, strlen($realWwwRoot)) !== $realWwwRoot) {
                return HealthStatus::warn('Media PUBLIC data directory is not in WWW_ROOT');
            }

            // check if the media data dir is a symlink
            if (is_link($mediaWwwRoot)) {
                return HealthStatus::warn('Media PUBLIC data directory is symlinked to WWW_ROOT');
            }

            return HealthStatus::ok('Media directories are setup correctly');
        });
        $hm->addCheck('media_image_processor_gd', function () {
            if (!extension_loaded('gd')) {
                return HealthStatus::crit('The PHP extension gd is not loaded');
            }

            if (!class_exists('\\Imagine\\Gd\\Imagine')) {
                return HealthStatus::crit('The Imagine image processor GD not found');
            }

            return HealthStatus::ok('Media image processor GD is fully supported');
        });
        $hm->addCheck('media_image_processor_im', function () {
            if (!extension_loaded('gd')) {
                return HealthStatus::crit('The PHP extension imagick is not loaded');
            }

            if (!class_exists('\\Imagine\\Imagick\\Imagine')) {
                return HealthStatus::crit('The Imagine image processor IMAGICK not found');
            }

            return HealthStatus::ok('Media image processor IMAGICK is fully supported');
        });
        $hm->addCheck('media_configuration', function () {
            if (!Configure::check('Media')) {
                return HealthStatus::crit('Media configuration not loaded');
            }

            if (!Configure::check('Media.default')) {
                return HealthStatus::crit('Default Media configuration not defined');
            }

            return HealthStatus::ok('Media plugin is properly configured');
        });
    }

    /**
     * @param \Cake\Event\EventInterface $event The event
     * @return void
     */
    public function adminViewInit(EventInterface $event): void
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
     * @param \Cake\Event\EventInterface $event The event
     * @param \Cupcake\Menu\MenuItemCollection $menu The menu.
     * @return void
     */
    public function adminMenuInit(EventInterface $event, \Cupcake\Menu\MenuItemCollection $menu): void
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
