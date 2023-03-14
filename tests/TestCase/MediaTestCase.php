<?php
declare(strict_types=1);

namespace Media\Test\TestCase;

use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\TestSuite\TestCase;
use Media\MediaManager;

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

    public static function setupBeforeClass(): void
    {
        self::$sourcePath = dirname(__DIR__) . DS . '_testfiles' . DS;
        self::$targetPath = TMP . 'tests' . DS . 'media' . DS;

        //@mkdir(self::$targetPath, 0777, true);

        $testMediaConfig = [
            'label' => 'Test Media',
            'provider' => 'Media.LocalStorage',
            'basePath' => self::$targetPath,
            'public' => true,
            'baseUrl' => '/media/test',
        ];
        Configure::write('Media', ['test' => $testMediaConfig]);
        if (!MediaManager::getConfig('test')) {
            MediaManager::setConfig('test', $testMediaConfig);
        }

        if (static::$setupTestFiles === true) {
            self::setUpTestFiles();
        }
    }

    public static function tearDownAfterClass(): void
    {
        if (static::$setupTestFiles === true) {
            self::tearDownTestFiles();
        }

        Configure::delete('Media.test');
    }

    public static function setUpTestFiles()
    {
        debug("Copy test files");
        self::$sourceFolder = new Folder(self::$sourcePath, false);
        self::$targetFolder = new Folder(self::$targetPath, true);

        self::$sourceFolder
            ->copy(self::$targetPath, [
                'recursive' => true,
                'scheme' => Folder::OVERWRITE,
            ]);
    }

    public static function tearDownTestFiles()
    {
        //self::$targetFolder->delete(self::$targetPath);
    }
}
