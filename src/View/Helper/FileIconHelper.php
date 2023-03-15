<?php

namespace Media\View\Helper;

use Cake\View\Helper;

class FileIconHelper extends Helper
{
    /**
     * @var array|string[]
     */
    public array $map = [
        'pdf' => 'file-pdf-o',
        'jpg' => 'file-image-o',
        'jpeg' => 'file-image-o',
        'gif' => 'file-image-o',
        'png' => 'file-image-o',
        'mp3' => 'file-audio-o',
        'wav' => 'file-audio-o',
        'ogg' => 'file-audio-o',
        'txt' => 'file-text-o',
        'json' => 'file-text-o',
        'xml' => 'file-text-o',
        'html' => 'file-code-o',
        'php' => 'file-code-o',
        'mp4' => 'file-video-o',
        'zip' => 'file-archive-o',
        'tar' => 'file-archive-o',
        'tar.gz' => 'file-archive-o',
        '7z' => 'file-archive-o',
        'xls' => 'file-excel-o',
        'xlsx' => 'file-excel-o',
        'doc' => 'file-word-o',
        'docx' => 'file-word-o',
        'odt' => 'file-word-o',
    ];

    /**
     * @param string|null $icon
     * @return string
     */
    public function render(?string $icon = null): string
    {
        $defaultIcon = 'file-o';
        $icon = $icon ?: $defaultIcon;
        return '<i class="fa fa-' . $icon . '"></i>';
    }

    /**
     * @param string $ext
     * @return string
     */
    public function fromFileExtension(string $ext): string
    {
        $ext = strtolower($ext);
        $icon = null;
        if ($ext && array_key_exists($ext, $this->map)) {
            $icon = $this->map[$ext];
        }
        return $this->render($icon);
    }

    /**
     * @param string $path
     * @return string
     */
    public function fromPath(string $path): string
    {
        $fileName = basename($path);
        $ext = '';
        if (strrpos($fileName, '.') !== false) {
            $dotExt = substr($fileName, strrpos($fileName, '.'));
            $fileName = basename($fileName, $dotExt);
            $ext = substr($dotExt, 1);
        }

        return $this->fromFileExtension($ext);
    }
}