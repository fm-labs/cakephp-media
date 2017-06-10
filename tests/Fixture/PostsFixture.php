<?php
namespace Media\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * AttachmentTestsFixture
 *
 */
class PostsFixture extends TestFixture
{

    public $table = 'media_posts';

    /**
     * Fields
     *
     * @var array
     */
    // @codingStandardsIgnoreStart
    public $fields = [
        'id' => ['type' => 'integer', 'length' => 11, 'unsigned' => true, 'null' => false, 'default' => null, 'comment' => '', 'autoIncrement' => true, 'precision' => null],
        'title' => ['type' => 'string', 'length' => 255, 'null' => false, 'default' => '', 'comment' => '', 'precision' => null, 'fixed' => null],
        'image' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null, 'fixed' => null],
        'images' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'text' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        'html' => ['type' => 'text', 'length' => null, 'null' => true, 'default' => null, 'comment' => '', 'precision' => null],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB', 'collation' => 'utf8_general_ci'
        ],
    ];
    // @codingStandardsIgnoreEnd

    /**
     * Records
     *
     * @var array
     */
    public $records = [
        [
            'id' => 1,
            'title' => 'Post 1',
            'image' => 'image1.jpg',
            'images' => ''
        ],
        [
            'id' => 2,
            'title' => 'Post 2',
            'image' => '',
            'images' => 'image1.jpg,image2.png'
        ],
        [
            'id' => 3,
            'title' => 'Post 3',
            'image' => 'image1.jpg',
            'images' => 'image1.jpg'
        ],
        [
            'id' => 4,
            'title' => 'Post 4',
            'image' => 'image1.jpg',
            'images' => 'image1.jpg,image2.png'
        ]
    ];
}
