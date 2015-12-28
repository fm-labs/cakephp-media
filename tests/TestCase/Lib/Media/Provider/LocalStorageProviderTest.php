<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 12/28/15
 * Time: 6:07 PM
 */

namespace Media\Test\TestCase\Lib\Media\Provider;

use Cake\Filesystem\Folder;
use Media\Lib\Media\Provider\LocalStorageProvider;
use Media\Test\TestCase\MediaTestCase;

class LocalStorageProviderTest extends MediaTestCase
{
    /**
     * @var LocalStorageProvider
     */
    public $provider;

    public static function setupBeforeClass()
    {

        parent::setUpTestFiles();
    }

    public function setUp()
    {
        parent::setUp();


        $config = [
            'path' => self::$targetPath,
            'url' => 'http://example.org/media/'
        ];

        $this->provider = new LocalStorageProvider($config);
    }


    public function testConnect()
    {

    }

    public function testListFiles()
    {
        $list = $this->provider->listFiles('/');
        debug($list);
    }

    public function testListFilesRecursive()
    {
        $list = $this->provider->listFilesRecursive('/');
        debug($list);
    }


    public function testListFolders()
    {
        $list = $this->provider->listFolders('/');
        debug($list);
    }

    public function testListFoldersRecursive()
    {
        $list = $this->provider->listFoldersRecursive('/');
        debug($list);
    }


    public function tearDown()
    {
        $this->provider->disconnect();

        parent::tearDown();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownTestFiles();
    }

}