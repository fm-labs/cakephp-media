<?php
declare(strict_types=1);

namespace Media\Test\TestCase\Form;

use Cake\TestSuite\TestCase;
use Media\Form\NewFolderForm;

/**
 * Media\Form\NewFolderForm Test Case
 */
class NewFolderFormTest extends TestCase
{
    /**
     * Test subject
     *
     * @var \Media\Form\NewFolderForm
     */
    protected $NewFolder;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->NewFolder = new NewFolderForm();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->NewFolder);

        parent::tearDown();
    }

    /**
     * Test validationDefault method
     *
     * @return void
     * @uses \Media\Form\NewFolderForm::validationDefault()
     */
    public function testValidationDefault(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
