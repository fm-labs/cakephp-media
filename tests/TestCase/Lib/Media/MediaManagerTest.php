<?php

namespace Media\Test\TestCase\Lib\Media;

use Media\Lib\Media\MediaManager;
use Media\Test\TestCase\Lib\Media\Provider\TestProvider;
use Media\Test\TestCase\MediaTestCase;

class MediaManagerTest extends MediaTestCase
{
    /**
     * @var MediaManager
     */
    public $mm;

    public function setUp()
    {
        parent::setUp();

        $testProvider = new TestProvider([]);
        $this->mm = new MediaManager($testProvider);
    }

    public function testPathGetterSetter()
    {
        $this->markTestIncomplete();

        return;

        $this->mm->setPath('/');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('/test');
        $this->assertEquals('test/', $this->mm->getPath());

        $this->mm->setPath('test/');
        $this->assertEquals('test/', $this->mm->getPath());

        $this->mm->setPath('test');
        $this->assertEquals('test/', $this->mm->getPath());

        $this->mm->setPath('/test/dir');
        $this->assertEquals('test/dir/', $this->mm->getPath());

        $this->mm->setPath('test/dir/');
        $this->assertEquals('test/dir/', $this->mm->getPath());

        $this->mm->setPath('test/dir');
        $this->assertEquals('test/dir/', $this->mm->getPath());
    }

    public function testBadPathGetterSetter()
    {
        $this->markTestIncomplete();

        return;

        $this->mm->setPath('/../');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('../');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('/..');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('test/..');
        $this->assertEquals('test/', $this->mm->getPath());

        $this->mm->setPath('test/../');
        $this->assertEquals('test/', $this->mm->getPath());

        $this->mm->setPath('//');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('////////');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('/../../../../../../');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('/../../../../../../');
        $this->assertEquals('', $this->mm->getPath());

        $this->mm->setPath('test//');
        $this->assertEquals('test/', $this->mm->getPath());
    }
}
