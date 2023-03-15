<?php
declare(strict_types=1);

namespace Media\View\Helper;

use Cake\Routing\Router;
use Cake\View\Helper;

/**
 * Class FileBrowserHelper.
 *
 * @package Media\View\Helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\UrlHelper $Url
 * @property \Cake\View\Helper\FormHelper $Form
 */
class FileBrowserHelper extends Helper
{
    public $helpers = ['Html', 'Url', 'Form'];

    /**
     * Current media config name.
     *
     * @return string|null
     */
    public function configName(): ?string
    {
        //return $this->getView()->getRequest()->getParam('config');
        return $this->getView()->get('config');
    }

    /**
     * Current directory path.
     *
     * @return string|null
     */
    public function path(): ?string
    {
        //return $this->getView()->getRequest()->getQuery('path', '/');
        return $this->getView()->get('path');
    }

    /**
     * Current file name.
     *
     * @return string|null
     */
    public function file(): ?string
    {
        //return $this->getView()->getRequest()->getQuery('file');
        return $this->getView()->get('file');
    }

    /**
     * Directory action HTML link.
     * @param string $title
     * @param string|array $url
     * @param array $attrs
     * @return string
     */
    public function directoryLink(string $title, $url, ?string $path = null, array $attrs = []): string
    {
        $path = $path ?: $this->path();
        $url = $this->url($url, $path);
        return $this->Html->link($title, $url, $attrs);
    }

    /**
     * File action HTML link.
     *
     * @param string $title
     * @param string|array $url
     * @param array $attrs
     * @return string
     */
    public function fileLink(string $title, $url, ?string $path = null, ?string $file = null, array $attrs = []): string
    {
        $path = $path ?: $this->path();
        $file = $file ?: $this->file();
        $url = $this->url($url, $path, $file);
        return $this->Html->link($title, $url, $attrs);
    }

    /**
     * Build url for directory or file actions.
     *
     * @param $url
     * @param string|null $path
     * @param string|null $file
     * @param bool $full
     * @return array|string|string[]
     */
    public function url($url, ?string $path = null, ?string $file = null, bool $full = false)
    {
        $query = [];
        if ($path) {
            $query['path'] = $path;
        }
        if ($file) {
            $query['file'] = $file;
        }
        $baseUrl = [
            'plugin' => 'Media',
            'controller' => 'Files',
            'action' => 'index',
            'config' => $this->configName(),
            '?' => $query
        ];

        if (is_string($url)) {
            $url = ['action' => $url];
        }
        $url = array_merge($baseUrl, $url);
        if ($full) {
            $url = Router::url($url, $full);
        }
        return $url;
    }
}
