<?php
namespace Media\Test\TestCase\Model\Behavior;

use Cake\Core\Configure;
use Cake\ORM\Table;
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
        'plugin.media.posts'
    ];

    /**
     * @var Table
     */
    public $table;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->table = TableRegistry::getTableLocator()->get('Media.Posts', ['table' => 'media_posts']);
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
                ],
                'text' => [
                    'config' => 'test',
                    'mode' => MediaBehavior::MODE_TEXT
                ],
                'html' => [
                    'config' => 'test',
                    'mode' => MediaBehavior::MODE_HTML
                ],
            ]
        ]);
        //$this->table->validator();
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        parent::tearDown();
        TableRegistry::clear();
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

        $this->assertInternalType('array', $post->images);
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
