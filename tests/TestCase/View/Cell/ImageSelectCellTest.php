<?php
namespace Media\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Media\View\Cell\ImageSelectCell;

/**
 * Media\View\Cell\ImageSelectCell Test Case
 */
class ImageSelectCellTest extends TestCase
{

    /**
     * Request mock
     *
     * @var \Cake\Http\ServerRequest|\PHPUnit\Framework\MockObject\MockObject
     */
    public $request;

    /**
     * Response mock
     *
     * @var \Cake\Http\Response|\PHPUnit\Framework\MockObject\MockObject
     */
    public $response;

    /**
     * Test subject
     *
     * @var \Media\View\Cell\ImageSelectCell
     */
    public $ImageSelect;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Cake\Http\ServerRequest')->getMock();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->ImageSelect = new ImageSelectCell($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ImageSelect);

        parent::tearDown();
    }

    /**
     * Test display method
     *
     * @return void
     */
    public function testDisplay()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
