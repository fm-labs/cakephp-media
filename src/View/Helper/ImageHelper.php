<?php
declare(strict_types=1);

namespace Media\View\Helper;

use Cake\Core\Configure;
use Cake\Log\Log;
use Cake\Utility\Text;
use Cake\View\Helper;
use Media\Lib\Image\ImageProcessor;
use Media\MediaException;

/**
 * Class MediaHelper
 *
 * @package Media\View\Helper
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\FormHelper $Form
 */
class ImageHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form'];

    /**
     * @var \Media\Lib\Image\ImageProcessor|null
     */
    protected ?ImageProcessor $_processor = null;

    /**
     * @inheritDoc
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
    }

    /**
     * @return ImageProcessor
     */
    protected function imageProcessor(): ImageProcessor
    {
        if (!$this->_processor) {
            $processor = new ImageProcessor();
            //if ($processor->imagine() !== null) {
            //}
            $this->_processor = $processor;
        }

        return $this->_processor;
    }

    /**
     * @param string $source Image source path
     * @param array $options Thumbnail options
     * @param array|bool $urlOpts Url options (also accepts legacy boolean value)
     * @return string
     * @todo Do not accept boolean value for $urlOpts parameter
     */
    public function thumbnailUrl(string $source, array $options = [], $urlOpts = []): ?string
    {
        try {
            $urlOpts = is_bool($urlOpts) ? ['full' => $urlOpts] : $urlOpts;
            $thumbUrl = $this->_generateThumbnail($source, $options);
            if ($thumbUrl) {
                return $this->Url->build($thumbUrl, $urlOpts);
            }
        } catch (\Exception $ex) {
            return $this->_error($ex);
        }
        return null;
    }

    /**
     * @param string $source Image source path
     * @param array $options Thumbnail options
     * @param array $attr Image html attributes
     * @return string
     */
    public function thumbnail(string $source, array $options = [], array $attr = []): string
    {
        try {
            $thumbUrl = $this->_generateThumbnail($source, $options);
            if ($thumbUrl) {
                $attr['alt'] = $attr['alt'] ?? basename($source);
                $attr['title'] = $attr['title'] ?? basename($source);
                return $this->Html->image($thumbUrl, $attr);
            }

        } catch (\Exception $ex) {
            return $this->_error($ex);
        }

        // @todo Thumbnail fallback
        return '<i class="fa fa-5x fa-image text-muted"></i>';
    }

    /**
     * @param string $source Image source path
     * @param array $options Thumbnail options
     * @return string|null
     * @throws \Media\MediaException
     * @todo Use configurable cache path instead of hard-coded path
     */
    protected function _generateThumbnail(string $source, array $options = []): ?string
    {
        //if (!$this->_processor) {
        //    throw new MediaException('generateThumbnail: Image processor not loaded');
        //}

        if (!file_exists($source) || preg_match('/\:\/\//', $source)) {
            throw new MediaException('generateThumbnail: Source image not found at ' . $source);
        }

        $info = pathinfo($source);

        $ext = "";
        if (isset($info['extension'])) {
            $ext = $info['extension'];
        }

        if (!in_array($ext, ['jpeg', 'jpg', 'png'])) {
            //throw new MediaException('generateThumbnail: Source file is not an image');
            return null;
        }

        $options = array_merge(['height' => 100, 'width' => 100], $options);
        $filename = Text::slug($info['filename'], '_');

        $thumbBasename =  $filename . '_' . md5($source . serialize($options)) . "." . $ext;
        $thumbPath = MEDIA_CACHE_DIR . $thumbBasename;
        $thumbUri = MEDIA_CACHE_URL . $thumbBasename;

        // cached thumbnail
        if (file_exists($thumbPath)) {
            return $thumbUri;
        }

        // render thumbnail
        $this->imageProcessor()
            ->open($source)
            ->thumbnail($options)
            ->save($thumbPath);

        if (Configure::read('debug')) {
            Log::write(
                'debug',
                sprintf('MediaHelper: generateThumbnail: CREATED: ' . $thumbPath . ' from: ' . $source),
                ['media']
            );
        }

        return $thumbUri;
    }

    /**
     * @param \Exception $ex Exception object
     * @return string
     */
    protected function _error(\Exception $ex): string
    {
        $msg = $ex->getMessage();
        if (Configure::read('debug')) {
            Log::write('debug', sprintf('MediaHelper: %s', $msg), ['media']);

            return sprintf('<span class="media-thumb-broken" title="%s">[x]</span>', $msg);
        }

        return '';
    }
}
