<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/5/15
 * Time: 11:48 PM
 */

namespace Media\Lib\Media\Provider;

use Cake\Core\InstanceConfigTrait;
use Cake\Routing\Router;

abstract class MediaProvider implements MediaProviderInterface
{
    use InstanceConfigTrait;

    public function __construct(array $config)
    {
        $this->config($config);
    }

    public function baseUrl($full = false)
    {
        $baseUrl = $this->config('url');
        if (!$baseUrl) {
            $baseUrl = ['plugin' => 'Media', 'controller' => 'Media', 'action' => 'index'];
        }
        return Router::url($baseUrl, $full);
    }

}
