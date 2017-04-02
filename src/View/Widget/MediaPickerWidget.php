<?php

namespace Media\View\Widget;

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
    public function __construct(StringTemplate $templates, View $view)
    {
        parent::__construct($templates);

        // make sure the MediaPickerHelper is attached to the current view
        if (!$view->helpers()->has('MediaPicker')) {
            $view->loadHelper('Media.MediaPicker');
        }

        // lazy load helper, css and script dependencies via MediaPickerHelper
        $view->MediaPicker->loadDependencies();
    }

    public function render(array $data, ContextInterface $context)
    {

        $data += [
            'config' => 'images',
            'name' => '',
            'val' => null,
            'escape' => true,
            'templateVars' => [],
            'class' => null,
        ];

        $data['id'] = (isset($data['id'])) ? $data['id'] : uniqid('mediapicker');
        $data['type'] = 'text';

        $class = 'form-control media-picker';
        $data['class'] = ($data['class']) ? $data['class'] . ' ' . $class : $class;

        if (is_object($data['val']) && $data['val'] instanceof MediaFile) {
            //debug($data['val']->toArray());
            $data['data-fileid'] = $data['val']->path;
            $data['data-filename'] = $data['val']->basename;
            $data['data-fileurl'] = $data['val']->url;
        }
        $out = parent::render($data, $context);


        $treeUrl =['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'treeData', 'config' => $data['config'], '_ext' => 'json'];
        $filesUrl = ['plugin' => 'Media', 'controller' => 'MediaManager', 'action' => 'filesData', 'config' => $data['config'], '_ext' => 'json'];

        $mediapicker = [
            'modal' => true,
            'treeUrl' => Router::url($treeUrl),
            'filesUrl' => Router::url($filesUrl)
        ];
        $template = "$(document).ready(function() { $('#%s').mediapicker(%s); });";
        $script = sprintf($template, $data['id'], json_encode($mediapicker));


        return $out . '<script>' . $script . '</script>';
    }

}
