<?php

namespace Media\View\Helper;


use Cake\Event\Event;
use Cake\View\View;

class MediaPickerHelper extends MediaHelper
{
    public $helpers = ['Html', 'Form'];

    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        $widgets = [
            'media_picker' => ['Media\View\Widget\MediaPickerWidget']
        ];
        foreach ($widgets as $type => $config) {
            $this->Form->addWidget($type, $config);
        }

        //@todo remove the dependency on Backend plugin
        //$this->Html->css('Backend.jstree/themes/backend/style.min', ['block' => true]);
        //$this->Html->script('Backend.jstree/jstree.min', ['block' => true]);
        $this->_View->loadHelper('Backend.JsTree');

        $this->Html->script('/backend/libs/underscore/underscore-min', ['block' => 'script']);
        $this->Html->script('/backend/libs/backbone/backbone-min', ['block' => 'script']);

        $this->Html->css('Media.mediapicker', ['block' => true]);
        $this->Html->script('Media.mediapicker', ['block' => 'script']);
    }

    public function beforeLayout(Event $event)
    {
        $mediapicker = [
            'modal' => true,
            'treeUrl' => $this->Url->build(['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'treeData', 'config' => 'images', '_ext' => 'json']),
            'filesUrl' => $this->Url->build(['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'filesData', 'config' => 'images', '_ext' => 'json'])
        ];
        $template = "$(document).ready(function() { console.log('media picker loading'); $('.media-picker').mediapicker(%s); });";
        $script = sprintf($template, json_encode($mediapicker));
        $this->Html->scriptBlock($script, ['block' => true]);
    }
}