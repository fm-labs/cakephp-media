<?php

namespace Media\Test\TestCase\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Media\Test\TestCase\MediaTestCase;

class MediaFileTest extends MediaTestCase
{
    public static $setupTestFiles = true;

    public function setUp()
    {
        parent::setUp();
    }

    public function newEntity()
    {
        $class = '\\Media\\Model\\Entity\\MediaFile';

        $table = new Table();
        $table->entityClass($class);

        $entity = $table->newEntity();
        return $entity;
    }

    public function testNewEntity()
    {
        $class = '\\Media\\Model\\Entity\\MediaFile';
        $entity = $this->newEntity();
        $this->assertInstanceOf($class, $entity);
    }

    public function testPathProperty()
    {
        // test with config + path
        $entity = $this->newEntity();
        $entity->config = 'test';
        $entity->path = 'dir2/image1.jpg';

        $this->assertEquals('dir2/image1.jpg', $entity->path);

        // test with media url
        $entity = $this->newEntity();
        $entity->config = null;
        $entity->path = 'media://test/dir2/image1.jpg';

        $this->assertEquals('test', $entity->config);
        $this->assertEquals('dir2/image1.jpg', $entity->path);
    }

    public function testUrlProperty()
    {
        $entity = $this->newEntity();
        $entity->config = 'test';
        $entity->path = 'dir2/image1.jpg';

        $this->assertEquals('/media/test/dir2/image1.jpg', $entity->url);


        $entity->config = null;
        $entity->path = 'media://test/dir2/image1.jpg';

        $this->assertEquals('/media/test/dir2/image1.jpg', $entity->url);
    }
}