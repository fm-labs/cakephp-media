<?php
/**
 * Render a media image file
 *
 * Params:
 * - image: MediaFile instance
 * - imageOptions: Array of HtmlHelper::image() compatible options
 * - label: Header label
 * - actions: Array of action links: Each action requires the 3 parameters of HtmlHelper::link()
 *
 * @see HtmlHelper
 */
if (!isset($imageOptions)) $imageOptions = [];
if (!isset($imageActions)) $imageActions = [];
?>
<div class="row">
    <div class="col-sm-12">
        <?= (isset($label)) ? '<label>' . $label . '</label>' : ''; ?>
        <?php if ($image): ?>
            <div class="thumbnail">
                <?= $this->Media->thumbnail($image->filepath, $imageOptions); ?>
                <div class="caption">
                    <h5 title="<?= h($image->path); ?>"><?= h($image->basename); ?></h5>
                    <p>
                        <?php
                        foreach ($imageActions as $action):
                            echo $this->Ui->link($action[0], $action[1], $action[2]) . "\n";
                        endforeach;
                        ?>
                    </p>
                </div>
            </div>
        <?php else: ?>
            <div class="thumbnail">
                <?= "No Image selected" ?>
                <p>
                    <?php
                    foreach ($imageActions as $action):
                        echo $this->Ui->link($action[0], $action[1], $action[2]) . "\n";
                    endforeach;
                    ?>
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
