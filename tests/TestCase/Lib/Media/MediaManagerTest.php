<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Lib\Media;

use Cake\Core\Configure;
use Media\Lib\Media\Provider\LocalStorageProvider;
use Media\MediaManager;
use Media\Test\TestCase\MediaTestCase;

class MediaManagerTest extends MediaTestCase
{
    /**
     * @var MediaManager
     */
    public $mm;

    public function setUp(): void
    {
        parent::setUp();
    }

    protected function getMediaManager(): MediaManager
    {
        if (!$this->mm) {
            $testProvider = new LocalStorageProvider(Configure::read('Media.test'));
            $this->mm = new MediaManager($testProvider);
        }

        return $this->mm;
    }

    public function testPathGetterSetter()
    {
        $this->getMediaManager()->setPath('/');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('/test');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test/');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('/test/dir');
        $this->assertEquals('test/dir/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test/dir/');
        $this->assertEquals('test/dir/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test/dir');
        $this->assertEquals('test/dir/', $this->getMediaManager()->getPath());
    }

    public function testBadPathGetterSetter()
    {
        $this->getMediaManager()->setPath('/../');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('../');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('/..');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test/..');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test/../');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('//');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('////////');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('/../../../../../../');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('/../../../../../../');
        $this->assertEquals('', $this->getMediaManager()->getPath());

        $this->getMediaManager()->setPath('test//');
        $this->assertEquals('test/', $this->getMediaManager()->getPath());
    }

    public function testListFiles()
    {
        $list = $this->getMediaManager()->listFiles('/');
        $this->assertEquals([
            (int)0 => 'file.txt',
        ], $list);

        $list = $this->getMediaManager()->listFiles('dir1/');
        $this->assertEquals([
            (int)0 => 'dir1/file1.txt',
            (int)1 => 'dir1/file2.txt',
        ], $list);
    }

    public function testListFilesRecursive()
    {
        $list = $this->getMediaManager()->listFilesRecursive('/');
        debug($list);
        $this->assertEquals([
            (int)0 => 'file.txt',
            (int)1 => 'dir1/file1.txt',
            (int)2 => 'dir1/file2.txt',
            (int)3 => 'dir2/image1.jpg',
            (int)4 => 'dir2/image2.png',
            (int)5 => 'dir2/dir3/hello.txt',
            (int)6 => 'dir2/dir3/world.txt',
            (int)7 => 'dir2/dir3/dir4/empty',
        ], $list);

        $list = $this->getMediaManager()->listFilesRecursive('dir2/');
        $this->assertEquals([
            (int)0 => 'dir2/image1.jpg',
            (int)1 => 'dir2/image2.png',
            (int)2 => 'dir2/dir3/hello.txt',
            (int)3 => 'dir2/dir3/world.txt',
            (int)4 => 'dir2/dir3/dir4/empty',
        ], $list);
    }

    public function testListFolders()
    {
        $list = $this->getMediaManager()->listFolders('/');
        $this->assertEquals([
            (int)0 => 'dir1',
            (int)1 => 'dir2',
        ], $list);

        $list = $this->getMediaManager()->listFolders('dir2/');
        $this->assertEquals([
            'dir3',
        ], $list);
    }

    public function testListFoldersRecursive()
    {
        $list = $this->getMediaManager()->listFoldersRecursive('/');
        $this->assertEquals([
            (int)0 => 'dir1',
            (int)1 => 'dir2',
            (int)2 => 'dir2/dir3',
            (int)3 => 'dir2/dir3/dir4',
        ], $list);
    }

    public function testListFoldersRecursiveDepth()
    {
        $list = $this->getMediaManager()->listFoldersRecursive('/', 0);
        $this->assertEquals([
            (int)0 => 'dir1',
            (int)1 => 'dir2',
        ], $list);

        $list = $this->getMediaManager()->listFoldersRecursive('/', 1);
        $this->assertEquals([
            (int)0 => 'dir1',
            (int)1 => 'dir2',
            (int)2 => 'dir2/dir3',
        ], $list);

        $list = $this->getMediaManager()->listFoldersRecursive('/', 2);
        $this->assertEquals([
            (int)0 => 'dir1',
            (int)1 => 'dir2',
            (int)2 => 'dir2/dir3',
            (int)3 => 'dir2/dir3/dir4',
        ], $list);
    }
}
