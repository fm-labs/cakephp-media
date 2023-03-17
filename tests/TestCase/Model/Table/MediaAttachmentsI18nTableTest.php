<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Model\Table;

use Cake\TestSuite\TestCase;
use Media\Model\Table\MediaAttachmentsI18nTable;

/**
 * Media\Model\Table\MediaAttachmentsI18nTable Test Case
 */
class MediaAttachmentsI18nTableTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Media\Model\Table\MediaAttachmentsI18nTable
     */
    protected $MediaAttachmentsI18n;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected $fixtures = [
        'plugin.Media.MediaAttachmentsI18n',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('MediaAttachmentsI18n') ? [] : ['className' => MediaAttachmentsI18nTable::class];
        $this->MediaAttachmentsI18n = $this->getTableLocator()->get('MediaAttachmentsI18n', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MediaAttachmentsI18n);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \Media\Model\Table\MediaAttachmentsI18nTable::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test buildRules method
     *
     * @return void
     * @uses \Media\Model\Table\MediaAttachmentsI18nTable::buildRules()
     */
    public function testBuildRules(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
