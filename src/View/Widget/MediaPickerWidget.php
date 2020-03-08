<?php

namespace Media\View\Widget;

use Bootstrap\View\Widget\ButtonWidget;
use Cake\Routing\Router;
use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use Cake\View\Widget\SelectBoxWidget;
use Media\Model\Entity\MediaFile;

/**
 * Class MediaPickerWidget
 *
 * @package Media\View\Widget
 */
class MediaPickerWidget extends BasicWidget
{
    /**
     * @var View
     */
    public $view;

    /**
     * @var ButtonWidget
     */
    public $button;

    public function __construct(StringTemplate $templates, View $view, ButtonWidget $button, SelectBoxWidget $select)
    {
        parent::__construct($templates);

        $this->view = $view;
        $this->button = $button;
        $this->select = $select;

        // make sure the MediaPickerHelper is attached to the current view
        if (!$this->view->helpers()->has('MediaPicker')) {
            $this->view->loadHelper('Media.MediaPicker');
        }
    }

    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'config' => 'default',
            'name' => '',
            'val' => null,
            'escape' => true,
            'templateVars' => [],
            'class' => null,
            'type' => null,
        ];

        $data['id'] = (isset($data['id'])) ? $data['id'] : uniqid('mediapicker');
        $data['type'] = 'text'; // @TODO change to hidden

        $config = $data['config'];
        unset($data['config']);

        $input = $image = $button = $script = "";

        // input html
        // add some media file meta data as html data attributes
        if (is_object($data['val']) && $data['val'] instanceof MediaFile) {
            $data['data-fileid'] = $data['val']->path;
            $data['data-filename'] = $data['val']->basename;
            $data['data-fileurl'] = $data['val']->url;

            if ($data['val']->isImage()) {
                $this->view->loadHelper('Html');
            }
            $image = $this->view->Html->image($data['val']->url, ['height' => 50]);

            $data['val'] = $data['val']->path;
            //unset($data['val']);
        }

        $data['class'] = 'form-control';
        $input = parent::render($data, $context);

        $buttonData = [
            'id' => uniqid('mediapickerselect'),
            'class' => 'mediapicker-select-control btn-default',
            'data-target' => '#' . $data['id'],
            'text' => __d('media', 'Select Media'),
            'type' => 'button',
            'escape' => false,
            'templateVars' => [],
        ];
        $button = $this->button->render($buttonData, $context);

        // javascript
        $treeUrl = ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'treeData', 'config' => $config, '_ext' => 'json'];
        $filesUrl = ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'filesData', 'config' => $config, '_ext' => 'json'];
        $mediapicker = [
            'target' => '#' . $data['id'],
            'modal' => true,
            'treeUrl' => Router::url($treeUrl),
            'filesUrl' => Router::url($filesUrl),
        ];
        $template = "$(document).ready(function() { if (typeof($.fn.mediapicker) === 'undefined') { console.warn('Mediapicker not initialized'); return false; } $('#%s').mediapicker(%s); });";
        $script = sprintf($template, $buttonData['id'], json_encode($mediapicker));

        return $input . $image . $button . '<script>' . $script . '</script>';
    }
}
