<?php
declare(strict_types=1);

namespace Media;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Http\MiddlewareQueue;
use Cake\Log\Log;
use Cake\Routing\Middleware\AssetMiddleware;
use Cake\Routing\Middleware\RoutingMiddleware;
use Media\Lib\Media\MediaManager;
use Media\Routing\Middleware\MediaMiddleware;

class Plugin extends BasePlugin
{
    /**
     * @inheritDoc
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

        defined('MEDIA') || define('MEDIA', WWW_ROOT . 'media' . DS);
        defined('MEDIA_CACHE_DIR') || define('MEDIA_CACHE_DIR', MEDIA . 'cache' . DS);
        defined('MEDIA_URL') || define('MEDIA_URL', '/media/');
        defined('MEDIA_CACHE_URL') || define('MEDIA_CACHE_URL', MEDIA_URL . 'cache/');

        $app->addOptionalPlugin('Upload');

        /**
         * Logger
         */
        if (!Log::getConfig('media')) {
            Log::setConfig('media', [
                'className' => 'Cake\Log\Engine\FileLog',
                'path' => LOGS,
                'file' => 'media',
                'scopes' => ['media'],
            ]);
        }

        /**
         * Database type maps
         */
        \Cake\Database\TypeFactory::map('media_file', 'Media\Database\Type\MediaFileType');

        /**
         * Load Media plugin configuration
         * and configure MediaManager
         */
        Configure::load('Media.media');
        MediaManager::setConfig((array)Configure::read('Media'));

        /**
         * Administration plugin
         */
        if (\Cake\Core\Plugin::isLoaded('Admin')) {
            \Admin\Admin::addPlugin(new \Media\Admin());
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
