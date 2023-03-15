<?php
declare(strict_types=1);

namespace Media\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Media\View\Cell\MediaFileCell;

/**
 * Media\View\Cell\MediaFileCell Test Case
 */
class MediaFileCellTest extends TestCase
{
    /**
     * Request mock
     *
     * @var \Cake\Http\ServerRequest|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $request;

    /**
     * Response mock
     *
     * @var \Cake\Http\Response|\PHPUnit\Framework\MockObject\MockObject
     */
    protected $response;

    /**
     * Test subject
     *
     * @var \Media\View\Cell\MediaFileCell
     */
    protected $MediaFile;

    /**
     * setUp method
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->request = $this->getMockBuilder('Cake\Http\ServerRequest')->getMock();
        $this->response = $this->getMockBuilder('Cake\Http\Response')->getMock();
        $this->MediaFile = new MediaFileCell($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->MediaFile);

        parent::tearDown();
    }

    /**
     * Test display method
     *
     * @return void
     * @uses \Media\View\Cell\MediaFileCell::display()
     */
    public function testDisplay(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
