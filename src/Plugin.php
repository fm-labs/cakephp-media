<?php
declare(strict_types=1);

namespace Media;

use Cake\Core\BasePlugin;
use Cake\Core\Configure;
use Cake\Core\PluginApplicationInterface;
use Cake\Event\EventManager;
use Cake\Log\Log;
use Media\Lib\Media\MediaManager;

class Plugin extends BasePlugin
{
    /**
     * {@inheritDoc}
     */
    public function bootstrap(PluginApplicationInterface $app): void
    {
        parent::bootstrap($app);

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
}
