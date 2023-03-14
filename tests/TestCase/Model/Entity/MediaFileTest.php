<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Model\Entity;

use Cake\ORM\Table;
use Media\Model\Entity\MediaFile;
use Media\Test\TestCase\MediaTestCase;

class MediaFileTest extends MediaTestCase
{
    public static $setupTestFiles = true;

    public function setUp(): void
    {
        parent::setUp();
    }

    public function newEmptyEntity()
    {
        //$class = MediaFile::class;
        //$table = new Table();
        //$table->setEntityClass($class);
        //$entity = $table->newEmptyEntity();
        $entity = new MediaFile();

        return $entity;
    }

    public function testNewEntity()
    {
        $class = MediaFile::class;
        $entity = $this->newEmptyEntity();
        $this->assertInstanceOf($class, $entity);
    }

    public function testPathProperty()
    {
        // test with config + path
        $entity = $this->newEmptyEntity();
        $entity->config = 'test';
        $entity->path = 'dir2/image1.jpg';

        $this->assertEquals('dir2/image1.jpg', $entity->path);

        // test with media url
//        $entity = $this->newEmptyEntity();
//        $entity->config = null;
//        $entity->path = 'media://test/dir2/image1.jpg';
//
//        $this->assertEquals('test', $entity->config);
//        $this->assertEquals('dir2/image1.jpg', $entity->path);
    }

    public function testUrlProperty()
    {
        $entity = $this->newEmptyEntity();
        $entity->config = 'test';
        $entity->path = 'dir2/image1.jpg';

        $this->assertEquals('http://localhost/media/test/dir2/image1.jpg', $entity->url);

//        $entity->config = null;
//        $entity->path = 'media://test/dir2/image1.jpg';
//
//        $this->assertEquals('/media/test/dir2/image1.jpg', $entity->url);
    }
}
