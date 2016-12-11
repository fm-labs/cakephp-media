<?php

namespace Media\View\Widget;

use Cake\View\Helper\FormHelper;
use Cake\View\Widget\BasicWidget;
use Cake\View\Widget\DateTimeWidget as CakeDateTimeWidget;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use DateTime;
use Media\Model\Entity\MediaFile;

class MediaPickerWidget extends BasicWidget
{
    public function __construct(StringTemplate $templates)
    {
        parent::__construct($templates);
    }

    public function render(array $data, ContextInterface $context)
    {
        $data += [
            'name' => '',
            'val' => null,
            'type' => 'text',
            'escape' => true,
            'templateVars' => [],
            'class' => null,
        ];

        $data['type'] = 'text';

        $class = 'form-control media-picker';
        $data['class'] = ($data['class']) ? $data['class'] . ' ' . $class : $class;

        if (is_object($data['val']) && $data['val'] instanceof MediaFile) {
            //debug($data['val']->toArray());
            $data['data-fileid'] = $data['val']->path;
            $data['data-filename'] = $data['val']->basename;
            $data['data-fileurl'] = $data['val']->url;
        }

        return parent::render($data, $context);
    }

}
