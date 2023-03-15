<?php
declare(strict_types=1);

namespace Media\Test\TestCase\View\Cell;

use Cake\TestSuite\TestCase;
use Media\View\Cell\DirectoryListingCell;

/**
 * Media\View\Cell\DirectoryListingCell Test Case
 */
class DirectoryListingCellTest extends TestCase
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
     * @var \Media\View\Cell\DirectoryListingCell
     */
    protected $DirectoryListing;

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
        $this->DirectoryListing = new DirectoryListingCell($this->request, $this->response);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->DirectoryListing);

        parent::tearDown();
    }

    /**
     * Test display method
     *
     * @return void
     * @uses \Media\View\Cell\DirectoryListingCell::display()
     */
    public function testDisplay(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
