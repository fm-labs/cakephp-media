<?php

namespace Media\Test\TestCase;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;

class MediaTestCase extends TestCase
{
    
    public static $sourcePath;
    
    public static $targetPath;
    
    /**
     * @var Folder
     */
    public static $sourceFolder;

    /**
     * @var Folder
     */
    public static $targetFolder;

    public static $setupTestFiles = false;

    public static function setupBeforeClass()
    {
        self::$sourcePath = dirname(__DIR__) . DS . '_testfiles' . DS;
        self::$targetPath = TMP . 'tests' . DS . 'media' . DS;

        Configure::write('Media', ['test' => [
            'label' => 'Test Media',
            'provider' => 'Media.LocalStorage',
            'path' => self::$targetPath,
            'public' => true,
            'url' => '/media/test',
        ]]);

        if (static::$setupTestFiles === true) {
            self::setUpTestFiles();
        }
    }

    public static function tearDownAfterClass()
    {
        if (static::$setupTestFiles === true) {
            self::tearDownTestFiles();
        }
    }

    public static function setUpTestFiles()
    {

        self::$sourceFolder = new Folder(self::$sourcePath, false);
        self::$targetFolder = new Folder(self::$targetPath, true);

        self::$sourceFolder
            ->copy([
                'to' => self::$targetPath,
                'recursive' => true,
                'scheme' => Folder::OVERWRITE
            ]);
    }

    public static function tearDownTestFiles()
    {
        self::$targetFolder->delete(self::$targetPath);
    }
}