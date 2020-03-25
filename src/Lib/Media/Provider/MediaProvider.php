<?php
declare(strict_types=1);

namespace Media\Lib\Media\Provider;

use Cake\Core\InstanceConfigTrait;

abstract class MediaProvider implements MediaProviderInterface
{
    use InstanceConfigTrait;

    public function __construct(array $config)
    {
        $this->setConfig($config);
        $this->initialize();
    }

    public function initialize()
    {
        // Override in sub classes
    }
}
