<?php
declare(strict_types=1);

namespace Media\View\Widget;

use Cake\Core\Configure;
use Cake\View\Form\ContextInterface;
use Cake\View\StringTemplate;
use Cake\View\View;
use Cake\View\Widget\BasicWidget;
use Cake\View\Widget\SelectBoxWidget;
use Media\MediaManager;
use Media\Model\Entity\MediaFile;

/**
 * Class MediaPickerWidget
 *
 * @package Media\View\Widget
 */
class MediaSelectWidget extends BasicWidget
{
    /**
     * @var \Cake\View\View
     */
    public View $view;

    /**
     * @var \Bootstrap\View\Widget\ButtonWidget
     */
    public SelectBoxWidget $select;

    /**
     * @param \Cake\View\StringTemplate $templates
     * @param \Cake\View\View $view
     * @param \Bootstrap\View\Widget\ButtonWidget $button
     * @param \Cake\View\Widget\SelectBoxWidget $select
     */
    public function __construct(StringTemplate $templates, View $view, SelectBoxWidget $select)
    {
        parent::__construct($templates);

        $this->view = $view;
        $this->select = $select;

    }

    /**
     * @param array $data
     * @param \Cake\View\Form\ContextInterface $context
     * @return string
     */
    public function render(array $data, ContextInterface $context): string
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

        $originalName = $data['name'];
        $originalId = $data['id'] ?? uniqid('mediaselect');

        $data['name'] = $originalName . "__original";
        $data['id'] = $originalId . "__original";
        $data['type'] = Configure::read('Media.debug') ? 'text' : 'hidden';
        $data['class'] = 'form-control';

        // @todo Get media config for field from Table / MediaBehavior config
        $config = $data['config'];
        unset($data['config']);

        $msg = $preview = $script = '';
        $val = $data['val'] ?? null;
        $url = $data['url'] ?? null;

        // input html
        // add some media file meta data as html data attributes
        if (is_object($data['val']) && $data['val'] instanceof MediaFile) {
            $data['data-config'] = $data['val']->config;
            $data['data-fileid'] = $data['val']->path;
            $data['data-filename'] = $data['val']->basename;
            $data['data-fileurl'] = $data['val']->url;

            if ($data['val']->isImage()) {
                $this->view->loadHelper('Html');
            }
            $preview = $this->view->Html->image($data['val']->url, ['height' => 50]);

            if ($data['val']->url) {
                $url = $data['val']->url;
            }

            if ($data['val']->filepath) {
                if (!file_exists($data['val']->filepath)) {
                    $msg = sprintf('<span class="badge bg-danger">%s</span> (%s)',
                        __('File missing'), $data['val']->filepath);
                } else {
                    $msg = sprintf('<span class="badge bg-success">%s</span>',
                        __('File OK'));
                }
            }

            $val = $data['val']->path;
        }

        //debug($data);
        $data['val'] = $val;
        $input = parent::render($data, $context);

        //debug($config);
        $mm = MediaManager::get($config);
        $fileList = $mm->listFilesRecursive('/');
        $fileListOptions = array_combine(array_values($fileList), array_values($fileList));
        //debug($fileListOptions);
        $selectData = [
            'type' => 'select',
            'id' => $originalId,
            'name' => $originalName,
            'val' => $data['val'],
            'escape' => true,
            'templateVars' => [],
            'class' => null,
            'options' => $fileListOptions,
            'width' => '100%'
        ];
        $select = $this->select->render($selectData, $context);

        $link = "";
        if ($url) {
            $link = $this->view->Html->div('', $this->view->Html->link($url, $url, ['target' => '_blank']));
        }

        $out = $input . $select . $msg . $link . $preview . $script;
        //debug($out);
        return $out;
    }
}
