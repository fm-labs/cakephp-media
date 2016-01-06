<?php

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
        $this->assertEquals([
            (int) 0 => 'file.txt'
        ], $list);

        $list = $this->provider->listFiles('dir1/');
        $this->assertEquals([
            (int) 0 => 'dir1/file1.txt',
            (int) 1 => 'dir1/file2.txt'
        ], $list);
    }

    public function testListFilesRecursive()
    {
        $list = $this->provider->listFilesRecursive('/');
        $this->assertEquals([
            (int) 0 => 'file.txt',
            (int) 1 => 'dir1/file1.txt',
            (int) 2 => 'dir1/file2.txt',
            (int) 3 => 'dir2/image1.jpg',
            (int) 4 => 'dir2/image2.png',
            (int) 5 => 'dir2/dir3/hello.txt',
            (int) 6 => 'dir2/dir3/world.txt'
        ], $list);

        $list = $this->provider->listFilesRecursive('dir2/');
        $this->assertEquals([
            (int) 0 => 'image1.jpg',
            (int) 1 => 'image2.png',
            (int) 2 => 'dir3/hello.txt',
            (int) 3 => 'dir3/world.txt'
        ], $list);
    }


    public function testListFolders()
    {
        $list = $this->provider->listFolders('/');
        $this->assertEquals([
            (int) 0 => 'dir1',
            (int) 1 => 'dir2',
        ], $list);
    }

    public function testListFoldersRecursive()
    {
        $list = $this->provider->listFoldersRecursive('/');
        $this->assertEquals([
            (int) 0 => 'dir1',
            (int) 1 => 'dir2',
            (int) 2 => 'dir2/dir3',
        ], $list);
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