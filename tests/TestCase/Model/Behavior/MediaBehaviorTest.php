<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 1/6/16
 * Time: 6:38 PM
 */

namespace Media\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Media\Test\TestCase\MediaTestCase;

class MediaBehaviorTest extends MediaTestCase
{
    public static $setupTestFiles = true;

    /**
     * fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.media.posts'
    ];

    /**
     * @var Table
     */
    public $table;

    public function setUp()
    {
        parent::setUp();

        $this->table = TableRegistry::get('Media.Posts');
        $this->table->primaryKey(['id']);
        //$this->table->entityClass('\\Attachment\\Test\\TestCase\\Model\\Entity\\ExampleEntity');
        //$this->table->schema()->columnType('images', 'media_file');
        $this->table->addBehavior('Media.Media', [
            'fields' => [
                'image' => [
                    'config' => 'test'
                ],
                'images' => [
                    'config' => 'test',
                    'multiple' => true,
                ]
            ]
        ]);
        //$this->table->validator();
    }

    public function testImageFieldToMediaFile()
    {
        $post = $this->table->get(1);

        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->image);

        //debug($post->image);
        //debug($post->image->realpath);
    }

    public function testImageFieldToMediaFileMultiple()
    {
        $post = $this->table->get(2);

        $this->assertInternalType('array', $post->images);
        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->images[0]);
        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->images[1]);
    }

    public function testSaveText()
    {
        $text = 'Test Media Path in Text: /media/dir2/image1.jpg';

        $entity = $this->table->newEntity();
        $entity->text = $text;

        $this->assertEquals($text, $entity->text);

        $this->table->save($entity);
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

}