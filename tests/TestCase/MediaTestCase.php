<?php

namespace Media\Test\TestCase;

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

    public static function setUpTestFiles()
    {
        self::$sourcePath = dirname(__DIR__) . DS . '_testfiles' . DS;
        self::$targetPath = TMP . 'tests' . DS . 'media' . DS;

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