<?php
/** @var \Media\Model\Entity\MediaFile $mediaFile */
$mediaFile = $this->get('mediaFile');
if (!$mediaFile) {
    echo __d('media', "No file selected");
    return;
}
?>
<div class="box box-default with-border">
    <div class="box-header">
        <?= h($mediaFile->getBasename()); ?>
    </div>
    <div class="box-body">
        <table class="table">
            <tr>
                <td>Full path</td>
                <td><?= h($mediaFile->getFilePath()); ?></td>
            </tr>
            <tr>
                <td>Path</td>
                <td><?= h($mediaFile->getPath()); ?></td>
            </tr>
            <tr>
                <td>Name</td>
                <td><?= h($mediaFile->getBasename()); ?></td>
            </tr>
            <tr>
                <td>Size</td>
                <td><?= $this->Number->toReadableSize($mediaFile->getSize()); ?></td>
            </tr>
            <tr>
                <td>Preview</td>
                <td>
                    <?= $this->Image->thumbnail(
                        $mediaFile->getFilePath(),
                        ['height' => 200, 'width' => 200]
                    ); ?>
                    <br />
                    <?= $this->Html->link(
                        __d('media', 'View Original'),
                        $mediaFile->getUrl(),
                        ['target' => '_blank']
                    ); ?>
                </td>
            </tr>
            <tr>
                <td>Url</td>
                <td><?= $this->Html->link(
                        $mediaFile->getUrl(true),
                        $mediaFile->getUrl(true),
                        ['target' => '_blank']
                    ); ?></td>
            </tr>
        </table>
    </div>
</div>