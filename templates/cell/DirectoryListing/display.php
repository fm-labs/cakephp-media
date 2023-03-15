<?php
$this->loadHelpers('Media.FileBrowser');
$this->loadHelpers('Media.FileIcon');

$config = $this->get('config');
$path = $this->get('path', '/');
$file = $this->get('file');

$folders = $this->get('folders', []);
$files = $this->get('files', []);
?>
<div class="media-files-directory-listing">
    <table class="table table-hover">
        <?php foreach ($folders as $folder) : ?>
            <tr>
                <td width="20">
                    <i class="fa fa-folder"></i>
                </td>
                <td>
                    <?= $this->FileBrowser->directoryLink($folder, 'index', $path . $folder); ?>
                </td>
                <td class="actions">&nbsp;</td>
            </tr>
        <?php endforeach; ?>
        <?php foreach ($files as $file) : ?>
            <tr>
                <td width="20">
                    <?= $this->FileIcon->fromPath($file); ?>
                </td>
                <td>
                    <?= $this->FileBrowser->fileLink($file,
                        'index', $path, $file
                    ) ?>
                </td>
                <td class="actions text-end">
                    <?= $this->FileBrowser->fileLink(
                        '<i class="fa fa-eye"></i>',
                        ['action' => 'view'],
                        $path, $file,
                        ['escape' => false, 'title' => __d('media', 'View')]
                    ) ?>
                    <?= $this->FileBrowser->fileLink(
                        '<i class="fa fa-pencil-square-o"></i>',
                        ['action' => 'edit'],
                        $path, $file,
                        ['escape' => false, 'title' => __d('media', 'Edit')]
                    ) ?>
                    <?= $this->FileBrowser->fileLink(
                        '<i class="fa fa-pencil"></i>',
                        ['action' => 'rename'],
                        $path, $file,
                        ['escape' => false, 'title' => __d('media', 'Rename')]
                    ) ?>
                    <?= $this->FileBrowser->fileLink(
                        '<i class="fa fa-trash"></i>',
                        ['action' => 'delete'],
                        $path, $file,
                        [
                            'escape' => false,
                            'title' => __d('media', 'Delete'),
                            //'confirm' => 'Sure?'
                        ]
                    ) ?>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>