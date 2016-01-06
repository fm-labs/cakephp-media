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

    public function testUrlPropertyGetter()
    {
        $class = '\\Media\\Model\\Entity\\MediaFile';

        $table = new Table();
        $table->entityClass($class);

        $entity = $table->newEntity();

        $this->assertInstanceOf($class, $entity);

        $entity->config = 'test';
        $entity->path = 'dir2/image1.jpg';

        $this->assertEquals('/media/test/dir2/image1.jpg', $entity->url);
    }
}