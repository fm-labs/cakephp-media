<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Model\Behavior;

use Cake\ORM\TableRegistry;
use Media\Model\Behavior\MediaBehavior;
use Media\Model\Entity\MediaFile;
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
     * @var \Cake\ORM\Table
     */
    public $table;

    /**
     * {@inheritDoc}
     */
    public function setUp(): void
    {
        parent::setUp();
    }
    
    public function getTable(): \Cake\ORM\Table
    {
        if (!$this->table) {
            $table = $this->getTableLocator()->get('Media.Posts', ['table' => 'media_posts']);
            $table->setPrimaryKey(['id']);
            //$table->entityClass('\\Attachment\\Test\\TestCase\\Model\\Entity\\ExampleEntity');
            //$table->getSchema()->setColumnType('images', 'media_file');
            $table->addBehavior('Media.Media', [
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
            $this->table = $table;
        }
        //$this->getTable()->getValidator();
        return $this->table;
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
        $post = $this->getTable()->get(1, ['media' => true, 'contain' => []]);

        $this->assertInstanceOf(MediaFile::class, $post->image);
    }

    /**
     * @return void
     */
    public function testImageFieldToMediaFileMultiple()
    {
        $post = $this->getTable()->get(2, ['media' => true]);

        $this->assertIsArray($post->images);
        $this->assertInstanceOf(MediaFile::class, $post->images[0]);
        $this->assertInstanceOf(MediaFile::class, $post->images[1]);
    }

    /**
     * @return void
     */
    public function testSaveText()
    {
        $text = 'Test Media Path in Text: /media/dir2/image1.jpg';

        $entity = $this->getTable()->newEmptyEntity();
        $entity->text = $text;

        $this->assertEquals($text, $entity->text);

        $this->getTable()->save($entity);
    }
}
