<?= $this->element('Media.image_file', [
    'label' => $label,
    'image' => $image,
    'imageOptions' => $imageOptions,
    'actions' => [
        [
            __d('banana','Select Image'),
            ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'setImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config ],
            ['class' => 'iframe-modal', 'icon' => 'folder']
        ],
        [
            __d('banana','Remove Image'),
            ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'deleteImage', $id, 'scope' => $scope, 'model' => $model, 'id' => $id, 'config' => $config ],
            ['icon' => 'remove']
        ]
    ]
]); ?>