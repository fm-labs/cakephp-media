<?php
declare(strict_types=1);

namespace Media\View\Helper;

use Cake\View\View;

class MediaPickerHelper extends MediaHelper
{
    public $helpers = ['Html', 'Form', 'Url'];

    protected $_assetsLoaded = false;

    public function __construct(View $View, array $config = [])
    {
        parent::__construct($View, $config);

        $this->_View->loadHelper('Backend.JsTree');

        $this->Html->script('/backend/libs/underscore/underscore-min', ['block' => 'script']);
        $this->Html->script('/backend/libs/backbone/backbone-min', ['block' => 'script']);

        $this->Html->css('Media.mediapicker', ['block' => true]);
        $this->Html->script('Media.mediapicker', ['block' => 'script']);
    }
}
