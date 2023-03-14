<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Lib\Media\Provider;

use Cake\Core\Configure;
use Media\Lib\Media\Provider\LocalStorageProvider;
use Media\Test\TestCase\MediaTestCase;

class LocalStorageProviderTest extends MediaTestCase
{
    public static $setupTestFiles = true;

    /**
     * @var \Media\Lib\Media\Provider\LocalStorageProvider
     */
    public $provider;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function testRead()
    {
        $config = Configure::read('Media.test');
        $provider = new LocalStorageProvider($config);
        [$dirs, $files] = $provider->read('/');

        $this->assertEquals([
            'dir1',
            'dir2',
        ], $dirs);

        $this->assertEquals([
            'file.txt'
        ], $files);
    }

    public function tearDown(): void
    {
        $this->provider = null;
        parent::tearDown();
    }
}
