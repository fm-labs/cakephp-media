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
if (!isset($actions)) $actions = [];
?>
<div class="row">
    <div class="col-sm-12">
        <?= (isset($label)) ? '<h4>' . $label . '</h4>' : ''; ?>
        <div class="thumbnail">
            <?php
            if ($image) {
                echo $this->Html->image($image->url, $imageOptions);
            } else {
                //echo '<img src="" alt="No image set" />';
            }
            ?>
            <div class="caption">
                <h5><?= ($image) ? h($image->basename) : "No Image selected"; ?></h5>
                <p><small><?= ($image) ? h($image->path) : ""; ?></small></p>
                <p>
                    <?php
                    foreach ($actions as $action):
                        echo $this->Ui->link($action[0], $action[1], $action[2]) . "\n";
                    endforeach;
                    ?>
                </p>
            </div>
        </div>
    </div>
</div>
