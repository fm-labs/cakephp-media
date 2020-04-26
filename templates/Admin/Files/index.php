<?php
$this->loadHelper('Media.MediaBrowser');
$this->MediaBrowser->setMediaManager($this->get('manager'));
$this->Html->css('Media.filebrowser', ['block' => true]);

$manager = $this->get('manager');
$folders = $this->get('folders');
?>

<div class="files-container">
    <div class="index">

        <div id="browser-wrapper">

            <div id="browser-toolbar" class="actions">
                <?= $this->Html->link(
                    __d('media', 'New Folder'),
                    ['action' => 'add', 'type' => 'folder'],
                    ['data-icon' => 'folder', 'class' => 'folder-add btn btn-default']
                ); ?>
                <?= $this->Html->link(
                    __d('media', 'New File'),
                    ['action' => 'add', 'type' => 'file'],
                    ['data-icon' => 'file', 'class' => 'file-add btn btn-default']
                ); ?>
                <?= $this->Html->link(
                    __d('media', 'Upload'),
                    ['action' => 'upload'],
                    ['data-icon' => 'upload', 'class' => 'file-upload btn btn-default']
                ); ?>
            </div>
            <h4 id="browser-path">
                <?php
                $tmp = "/";
                echo $this->Html->link('<i class="fa fa-home"></i>', ['path' => $tmp], ['escape' => false]);
                //echo '<span class="separator">&nbsp;/&nbsp;</span>';

                if (\Cake\Core\Configure::read('debug')) {
                    echo '<small>' . h(rtrim($manager->getBasePath(), '/')) . '</small>';
                }

                foreach (explode('/', trim($path, '/')) as $_path) {
                    $tmp .= $_path . '/';
                    echo '<span class="separator">&nbsp;/&nbsp;</span>';
                    echo $this->Html->link($_path, ['path' => $tmp]);
                }
                ?></h4>

            <div id="browser-container">
                <div class="row">
                    <div class="col-md-8">
                        <div id="browser-folders">
                            <table class="table table-hover">
                                <?php foreach ($folders as $folder) : ?>
                                    <tr>
                                        <td width="20">
                                            <i class="fa fa-folder"></i>
                                        </td>
                                        <td>
                                            <?= $this->Html->link($folder, ['?' => ['path' => $path . $folder]]) ?>
                                        </td>
                                        <td class="actions">&nbsp;</td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php foreach ($files as $file) : ?>
                                    <tr>
                                        <td width="20">
                                            <?= $this->MediaBrowser->fileIcon($path, $file); ?>
                                        </td>
                                        <td>
                                            <?= $this->Html->link($file, ['action' => 'index', '?' => ['path' => $path, 'file' => $file]]) ?>
                                        </td>
                                        <td class="actions text-right">
                                            <?= $this->Html->link('<i class="fa fa-eye"></i>', ['action' => 'view', '?' => ['path' => $path, 'file' => $file]], ['escape' => false]) ?>
                                            <?= $this->Html->link('<i class="fa fa-pencil"></i>', ['action' => 'edit', '?' => ['path' => $path, 'file' => $file]], ['escape' => false]) ?>
                                            <?= $this->Html->link('<i class="fa fa-trash"></i>', ['action' => 'delete', '?' => ['path' => $path, 'file' => $file]], ['escape' => false, 'confirm' => 'Sure?']) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <?php if (isset($selectedFile)) : ?>
                        <div id="browser-file">
                            <div class="box box-default with-border">
                                <div class="box-header">
                                    <?= h($selectedFile->name() . '.' . $selectedFile->ext()); ?>
                                </div>
                                <div class="box-body">
                                    <table class="table">
                                        <tr>
                                            <td>Path</td>
                                            <td><?= h($path); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Name</td>
                                            <td><?= h($selectedFile->name() . '.' . $selectedFile->ext()); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Size</td>
                                            <td><?= $this->Number->toReadableSize($selectedFile->size()); ?></td>
                                        </tr>
                                        <tr>
                                            <td>Preview</td>
                                            <td>
                                                <?= $this->Media->thumbnail($selectedFile->path, ['height' => 200, 'width' => 200]); ?>
                                                <br />
                                                <?= $this->Html->link(__('View Fullsize'), '#'); ?>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>

                        <div class="browser-upload">
                            <?= $this->cell('Media.MediaUpload'); ?>
                        </div>
                    </div>


                </div>
            </div>
        </div>

    </div>

</div>