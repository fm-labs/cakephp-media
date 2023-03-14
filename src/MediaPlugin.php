<?php
declare(strict_types=1);

namespace Media;

use Admin\Admin;
use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Core\PluginApplicationInterface;
use Cake\Database\TypeFactory;
use Cake\Http\MiddlewareQueue;
use Cake\Log\Engine\FileLog;
use Cake\Log\Log;
use Cake\Routing\Middleware\AssetMiddleware;
use Media\Database\Type\MediaFileType;
use Media\Routing\Middleware\MediaMiddleware;

/**
 * MediaPlugin class
 */
class MediaPlugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        $app->addPlugin('Tree');
        $app->addPlugin('Upload');
        $app->addOptionalPlugin('Settings');

        defined('MEDIA') || define('MEDIA', WWW_ROOT . 'media' . DS);
        defined('MEDIA_CACHE_DIR') || define('MEDIA_CACHE_DIR', MEDIA . 'cache' . DS);
        defined('MEDIA_URL') || define('MEDIA_URL', '/media/');
        defined('MEDIA_CACHE_URL') || define('MEDIA_CACHE_URL', MEDIA_URL . 'cache/');

        /**
         * Logger
         */
        if (!Log::getConfig('media')) {
            Log::setConfig('media', [
                'className' => FileLog::class,
                'path' => LOGS,
                'file' => 'media',
                'scopes' => ['media'],
            ]);
        }

        /**
         * Database type maps
         */
        TypeFactory::map('media_file', MediaFileType::class);

        /**
         * Load Media plugin configuration
         * and configure MediaManager
         */
        Configure::load('Media.media');
        if (Plugin::isLoaded('Settings')) {
            Configure::load('Media', 'settings');
        }

        MediaManager::setConfig((array)Configure::read('Media.Files'));

        /**
         * Administration plugin
         */
        if (Plugin::isLoaded('Admin')) {
            Admin::addPlugin(new MediaAdmin());
        }
    }

    /**
     * @inheritDoc
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        $middlewareQueue->insertBefore(AssetMiddleware::class, MediaMiddleware::class);

        return $middlewareQueue;
    }
}
