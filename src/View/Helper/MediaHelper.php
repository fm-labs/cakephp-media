<?php
declare(strict_types=1);

namespace Media\View\Helper;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Utility\Text;
use Cake\View\Helper;
use Media\Lib\Image\ImageProcessor;
use Media\MediaException;
use Media\View\Widget\MediaPickerWidget;
use Media\View\Widget\MediaSelectWidget;

/**
 * Class MediaHelper
 *
 * @package Media\View\Helper
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\FormHelper $Form
 * @property \Media\View\Helper\ImageHelper $Image
 */
class MediaHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form', 'Media.Image'];

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        $this->Form->templater()->load('Media.form_templates');
        $this->Form->addWidget('media_picker', [MediaPickerWidget::class, '_view', 'button', 'select']);
        $this->Form->addWidget('media_select', [MediaSelectWidget::class, '_view', 'select']);
    }

    /**
     * @param string $source Image source path
     * @param array $options Thumbnail options
     * @param array|bool $urlOpts Url options (also accepts legacy boolean value)
     * @return string
     * @todo Do not accept boolean value for $urlOpts parameter
     * @deprecated Use ImageHelper instead
     */
    public function thumbnailUrl(string $source, array $options = [], $urlOpts = []): string
    {
        deprecationWarning("MediaHelper::thumbnailUrl() is deprecated. Use ImageHelper instead.");

        return $this->Image->thumbnailUrl($source, $options, $urlOpts);
    }

    /**
     * @param string $source Image source path
     * @param array $options Thumbnail options
     * @param array $attr Image html attributes
     * @return string
     * @deprecated Use ImageHelper instead
     */
    public function thumbnail(string $source, array $options = [], array $attr = []): string
    {
        deprecationWarning("MediaHelper::thumbnail() is deprecated. Use ImageHelper instead.");

        return $this->Image->thumbnail($source, $options, $attr);
    }
}
