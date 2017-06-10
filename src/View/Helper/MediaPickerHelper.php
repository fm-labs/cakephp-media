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

        $this->Form->templater()->load('Media.form_templates');
        $this->Form->addWidget('media_picker', ['Media\View\Widget\MediaPickerWidget', '_view', 'button', 'select']);
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

        $this->_assetsLoaded = true;
    }

    public function beforeLayout(Event $event)
    {
    }
}
