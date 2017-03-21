<?php

namespace Media\View\Helper;


use Cake\Event\Event;
use Cake\View\View;

class MediaPickerHelper extends MediaHelper
{
    public $helpers = ['Html', 'Form', 'Url'];

    protected $_assetsLoaded = false;

    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        $this->Form->addWidget('media_picker', ['Media\View\Widget\MediaPickerWidget', '_view']);
    }

    public function loadDependencies()
    {
        if ($this->_assetsLoaded === true) {
            return;
        }

        $this->_View->loadHelper('Backend.JsTree');

        $this->Html->script('/backend/libs/underscore/underscore-min', ['block' => 'script']);
        $this->Html->script('/backend/libs/backbone/backbone-min', ['block' => 'script']);

        $this->Html->css('Media.mediapicker', ['block' => true]);
        $this->Html->script('Media.mediapicker', ['block' => 'script']);

        $treeUrl =['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'treeData', 'config' => 'images', '_ext' => 'json'];
        $filesUrl = ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'filesData', 'config' => 'images', '_ext' => 'json'];

        $mediapicker = [
            'modal' => true,
            'treeUrl' => $this->Html->Url->build($treeUrl),
            'filesUrl' => $this->Html->Url->build($filesUrl)
        ];
        $template = "$(document).ready(function() { console.log('media picker loading'); $('.media-picker').mediapicker(%s); });";
        $script = sprintf($template, json_encode($mediapicker));
        $this->Html->scriptBlock($script, ['block' => true]);

        $this->_assetsLoaded = true;
    }

    public function beforeLayout(Event $event)
    {
    }
}