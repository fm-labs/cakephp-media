<?php
if ($multiple) {
    $out = $this->element('Media.image_files', [
        'label' => $label,
        'images' => $image,
        'imageOptions' => $imageOptions,
        'imageActions' => [
            [
                __d('media','Remove Image'),
                ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'deleteImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config, 'multiple' => true ],
                ['class' => 'btn btn-sm btn-danger', 'data-icon' => 'remove', 'role' => 'button']
            ]
        ],
        'actions' => [
            [
                __d('media','Add Image'),
                ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'setImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config, 'multiple' => true ],
                ['class' => 'btn btn-sm btn-default link-frame-modal', 'data-icon' => 'folder', 'role' => 'button']
            ]
        ]
    ]);
} else {
    $out = $this->element('Media.image_file', [
        'label' => $label,
        'image' => $image,
        'imageOptions' => $imageOptions,
        'imageActions' => [
            [
                __d('media','Select Image'),
                ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'setImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config ],
                ['class' => 'btn btn-sm btn-default link-frame-modal', 'data-icon' => 'folder', 'role' => 'button']
            ],
            [
                __d('media','Remove Image'),
                ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'deleteImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config ],
                ['class' => 'btn btn-sm btn-danger', 'data-icon' => 'remove', 'role' => 'button']
            ]
        ],
        'actions' => []
    ]);
}
echo $out;
?>