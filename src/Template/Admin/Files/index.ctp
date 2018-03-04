<?= $this->Html->css('Media.filebrowser', ['block' => true]); ?>
<div class="files-container">
    <div class="index">

        <div id="browser-wrapper">

            <div id="browser-toolbar" class="actions">
                <?= $this->Html->link(__d('media','New Folder'),
                    ['action' => 'add', 'type' => 'folder'],
                    ['data-icon' => 'folder', 'class' => 'folder-add btn btn-default']); ?>
                <?= $this->Html->link(__d('media','New File'),
                    ['action' => 'add', 'type' => 'file'],
                    ['data-icon' => 'file', 'class' => 'file-add btn btn-default']); ?>
                <?= $this->Html->link(__d('media','Upload'),
                    ['action' => 'upload'],
                    ['data-icon' => 'upload', 'class' => 'file-upload btn btn-default']); ?>
            </div>
            <h4 id="browser-path"><?php
                $tmp = "/";
                echo $this->Html->link('ROOT', ['path' => $tmp]);
                echo '<span class="separator">&nbsp;/&nbsp;</span>';

                foreach (explode('/', trim($path,'/')) as $_path) {
                    $tmp .= $_path . '/';
                    echo $this->Html->link($_path, ['path' => $tmp]);
                    echo '<span class="separator">&nbsp;/&nbsp;</span>';
                }
                ?></h4>
            <hr />
            <div id="browser-container">
                <div class="row">
                    <div class="col-md-9">
                        <div id="browser-folders">
                            <table>
                                <?php foreach ($folders as $folder): ?>
                                <tr>
                                    <td>
                                        <i class="fa fa-folder-o"></i>
                                    </td>
                                    <td>
                                        <?= $this->Html->link($folder, ['path' => $path . $folder]) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </table>
                            <table>
                                <?php foreach ($files as $file): ?>
                                    <tr>
                                        <td>
                                            <i class="fa fa-file-o"></i>
                                        </td>
                                        <td>
                                            <?= $this->Html->link($file, ['action' => 'view', 'path' => $path, 'file' => $file]) ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div id="browser-files">
                        </div>
                    </div>

                    <hr />
                    <div id="media-upload">
                        <h3><?= __d('media','Upload') ?></h3>
                        <div class="upload-form form">
                            <?php
                            //            echo $this->Form->create(null, ['type' => 'file']);
                            //            echo $this->Form->input('config', ['type' => 'text', 'value' => $this->get('config')]);
                            //            echo $this->Form->input('upload_file', ['type' => 'file']);
                            echo $this->Form->create($uploadForm, ['type' => 'file']);
                            //echo $this->Form->input('config');
                            echo $this->Form->input('upload_file', ['type' => 'file']);
                            echo $this->Form->submit(__d('media','Upload file'));
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

</div>