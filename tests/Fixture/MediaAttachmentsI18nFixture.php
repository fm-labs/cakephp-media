<?php
declare(strict_types=1);

namespace Media\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * MediaAttachmentsI18nFixture
 */
class MediaAttachmentsI18nFixture extends TestFixture
{
    /**
     * Table name
     *
     * @var string
     */
    public $table = 'attachments_i18n';
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'locale' => 'Lore',
                'model' => 'Lorem ipsum dolor sit amet',
                'foreign_key' => 1,
                'field' => 'Lorem ipsum dolor sit amet',
                'content' => 'Lorem ipsum dolor sit amet, aliquet feugiat. Convallis morbi fringilla gravida, phasellus feugiat dapibus velit nunc, pulvinar eget sollicitudin venenatis cum nullam, vivamus ut a sed, mollitia lectus. Nulla vestibulum massa neque ut et, id hendrerit sit, feugiat in taciti enim proin nibh, tempor dignissim, rhoncus duis vestibulum nunc mattis convallis.',
            ],
        ];
        parent::init();
    }
}
