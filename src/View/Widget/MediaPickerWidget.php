<?php

namespace Media\View\Widget;

use Bootstrap\View\Widget\ButtonWidget;
use Cake\Routing\Router;
use Cake\View\Helper\FormHelper;
use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Widget\DateTimeWidget as CakeDateTimeWidget;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use DateTime;
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

    public function __construct(StringTemplate $templates, View $view, ButtonWidget $button)
    {
        parent::__construct($templates);

        $this->view = $view;
        $this->button = $button;

        // make sure the MediaPickerHelper is attached to the current view
        if (!$this->view->helpers()->has('MediaPicker')) {
            $this->view->loadHelper('Media.Media');
            $this->view->loadHelper('Media.MediaPicker');
        }


        // lazy load helper, css and script dependencies via MediaPickerHelper
        //$view->MediaPicker->loadDependencies();
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
            'wrap' => [],
        ];

        $data['id'] = (isset($data['id'])) ? $data['id'] : uniqid('mediapicker');
        $data['type'] = 'text'; // @TODO change to hidden

        $config = $data['config'];
        unset($data['config']);

        $wrap = $data['wrap'];
        unset($data['wrap']);

        $defaultClass = 'form-control media-picker ';
        $data['class'] = ($data['class']) ? $defaultClass . $data['class'] : trim($defaultClass);

        //$data['readonly'] = 'readonly';

        // input html
        // add some media file meta data as html data attributes
        if (is_object($data['val']) && $data['val'] instanceof MediaFile) {
            $inputData['data-fileid'] = $data['val']->path;
            $inputData['data-filename'] = $data['val']->basename;
            $inputData['data-fileurl'] = $data['val']->url;
        }
        $input = parent::render($data, $context);

        if ($wrap === false) {
            return $input;
        }

        // actions html
        $btnSelect = $this->button->render(['text' => 'Select File', 'class' => 'default media-picker-btn-select'], $context);
        $btnRemove = $this->button->render(['text' => 'Remove', 'class' => 'danger media-picker-btn-remove'], $context);
        $actions = $this->_templates->format('media_mediapicker_actions', [
            'select' => $btnSelect,
            'remove' => $btnRemove
        ]);


        // javascript
        $treeUrl =['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'treeData', 'config' => $config, '_ext' => 'json'];
        $filesUrl = ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'filesData', 'config' => $config, '_ext' => 'json'];
        $mediapicker = [
            'modal' => true,
            'treeUrl' => Router::url($treeUrl),
            'filesUrl' => Router::url($filesUrl)
        ];
        $template = "$(document).ready(function() { $('#%s').mediapicker(%s); });";
        $script = sprintf($template, $data['id'], json_encode($mediapicker));

        // wrapper
        $wrap = (is_bool($wrap)) ? [] : $wrap;
        $wrap = array_merge([
            'id' => $data['id'] . '-wrapper',
            'class' => 'media-picker-container',
            'data-name' => $data['name'],
            'data-input' => $data['id'],
        ], $wrap);
        $html = $this->_templates->format('media_mediapicker', [
            'attrs' => $this->_templates->formatAttributes($wrap),
            'input' => $input,
            'actions' => $actions,
            'script' => $script
        ]);
        return $html;
    }

}
