<?php

use Cake\Core\Configure;
$this->loadHelper('Media.FileBrowser');
$this->loadHelper('Media.FileIcon');

$this->Html->css('Media.filebrowser', ['block' => true]);

$config = $this->get('config');
$path = $this->get('path', '/');
$file = $this->get('file');

$folders = $this->get('folders', []);
$files = $this->get('files', []);

/** @var \Media\Model\Entity\MediaFile|null $mediaFile */
$mediaFile = $this->get('selectedFile', null);

if ($file) {
    $this->assign('title', $file);
}

?>
<div class="files-container">
    <div class="index">

        <div>
            <?php foreach(array_keys(Configure::read('Media.Files')) as $mediaConfig): ?>
            <?php echo $this->Html->link($mediaConfig, ['controller' => 'Files', 'action' => 'index', 'config' => $mediaConfig]) ?> |
            <?php endforeach; ?>
            <hr />
        </div>

        <div id="browser-wrapper">

            <div id="browser-path">
                <div>
                    <?php
                    $currentDir = '/';
                    echo $this->FileBrowser->directoryLink('<i class="fa fa-home"></i>', 'index', $currentDir, ['escape' => false]);
                    //echo '<span class="separator">&nbsp;/&nbsp;</span>';

                    foreach (explode('/', trim($path, '/')) as $_path) {
                        $currentDir .= $_path . '/';
                        echo '<span class="separator">&nbsp;/&nbsp;</span>';
                        echo $this->FileBrowser->directoryLink($_path, 'index', $currentDir);
                    }

                    if ($file) {
                        echo '<span class="separator">&nbsp;/&nbsp;</span>';
                        //echo $this->FileBrowser->fileLink($file, 'index', $currentDir, $file);
                        echo h($file);
                    }
                    ?>
                </div>
                <hr />
            </div>

            <div id="browser-toolbar" class="actions">
                <?= $this->FileBrowser->directoryLink(
                    __d('media', 'New Folder'),
                    'newFolder',
                    $path,
                    ['data-icon' => 'folder', 'class' => 'folder-add btn btn-sm btn-outline-secondary']
                ); ?>
                <?= $this->FileBrowser->directoryLink(
                    __d('media', 'New File'),
                    ['action' => 'newFile'],
                    $path,
                    ['data-icon' => 'file', 'class' => 'file-add btn btn-sm btn-outline-secondary']
                ); ?>
                <?= $this->FileBrowser->directoryLink(
                    __d('media', 'Upload'),
                    ['action' => 'upload'],
                    $path,
                    ['data-icon' => 'upload', 'class' => 'file-upload btn-sm btn btn-outline-secondary']
                ); ?>
                <hr />
            </div>
            <div id="browser-container">
                <div class="row">
                    <div class="col-md-8">
                        <div id="browser-folders">
                            <?php echo $this->cell('Media.DirectoryListing', [$path, $file], [
                                'mediaConfig' => $config,
                            ]); ?>
                        </div>
                    </div>


                    <div class="col-md-4">
                        <div id="browser-file">
                            <?php echo $this->cell('Media.MediaFile', [$mediaFile], []); ?>
                        </div>

                        <?php echo $this->fetch('content'); ?>

                        <div class="browser-upload">
                            <?php /*echo*/ $this->cell('Media.MediaUpload', [], [
                                    'mediaConfig' => 'default',
                                    'uploadConfig' => Configure::read('Media.Upload.files'),
                            ]); ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>