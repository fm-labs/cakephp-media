<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 1/6/16
 * Time: 6:38 PM
 */

namespace Media\Test\TestCase\Model\Behavior;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Media\Test\TestCase\MediaTestCase;

class MediaBehaviorTest extends MediaTestCase
{
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
                ],
                'images' => [
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
    }

    public function testImageFieldToMediaFileMultiple()
    {
        $post = $this->table->get(2);

        //$this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->images);
    }

    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
    }

}