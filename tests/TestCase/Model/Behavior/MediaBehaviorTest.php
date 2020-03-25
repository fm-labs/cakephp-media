<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Media\Model\Behavior\MediaBehavior;
use Media\Test\TestCase\MediaTestCase;

/**
 * Class MediaBehaviorTest
 *
 * @package Media\Test\TestCase\Model\Behavior
 */
class MediaBehaviorTest extends MediaTestCase
{
    public static $setupTestFiles = true;

    /**
     * fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.Media.Posts',
    ];

    /**
     * @var Table
     */
    public $table;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->table = TableRegistry::getTableLocator()->get('Media.Posts', ['table' => 'media_posts']);
        $this->table->setPrimaryKey(['id']);
        //$this->table->entityClass('\\Attachment\\Test\\TestCase\\Model\\Entity\\ExampleEntity');
        //$this->table->getSchema()->setColumnType('images', 'media_file');
        $this->table->addBehavior('Media.Media', [
            'fields' => [
                'image' => [
                    'config' => 'test',
                ],
                'images' => [
                    'config' => 'test',
                    'multiple' => true,
                ],
                'text' => [
                    'config' => 'test',
                    'mode' => MediaBehavior::MODE_TEXT,
                ],
                'html' => [
                    'config' => 'test',
                    'mode' => MediaBehavior::MODE_HTML,
                ],
            ],
        ]);
        //$this->table->getValidator();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown(): void
    {
        parent::tearDown();
        TableRegistry::getTableLocator()->clear();
    }

    /**
     * @return void
     */
    public function testImageFieldToMediaFile()
    {
        $post = $this->table->get(1, ['media' => true, 'contain' => []]);

        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->image);
    }

    /**
     * @return void
     */
    public function testImageFieldToMediaFileMultiple()
    {
        $post = $this->table->get(2, ['media' => true]);

        $this->assertIsArray($post->images);
        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->images[0]);
        $this->assertInstanceOf('\\Media\\Model\\Entity\\MediaFile', $post->images[1]);
    }

    /**
     * @return void
     */
    public function testSaveText()
    {
        $text = 'Test Media Path in Text: /media/dir2/image1.jpg';

        $entity = $this->table->newEntity();
        $entity->text = $text;

        $this->assertEquals($text, $entity->text);

        $this->table->save($entity);
    }
}
