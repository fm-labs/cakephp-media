<?php
declare(strict_types=1);

namespace Media\Routing\Middleware;

use Cake\Http\Response;
use Cake\Routing\Middleware\AssetMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use SplFileInfo;

/**
 * Applies routing rules to the request and creates the controller
 * instance if possible.
 */
class MediaMiddleware extends AssetMiddleware
{
    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request The request.
     * @param \Psr\Http\Server\RequestHandlerInterface $handler The next handler.
     * @return \Psr\Http\Message\ResponseInterface The response.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $url = $request->getUri()->getPath();

        if (!preg_match('/^\/media\/(.*)$/', $url, $matches)) {
            return $handler->handle($request);
        }

        if (strpos($url, '..') !== false || strpos($url, '.') === false) {
            return $handler->handle($request);
        }

        if (strpos($url, '/.') !== false) {
            return $handler->handle($request);
        }

        $assetFile = $this->_getAssetFile($matches[1]);
        if ($assetFile === null || !file_exists($assetFile)) {
            return $handler->handle($request);
        }

        $file = new SplFileInfo($assetFile);
        $modifiedTime = $file->getMTime();
        if ($this->isNotModified($request, $file)) {
            return (new Response())
                ->withStringBody('')
                ->withStatus(304)
                ->withHeader(
                    'Last-Modified',
                    date(DATE_RFC850, $modifiedTime)
                );
        }

        return $this->deliverAsset($request, $file);
    }

    /**
     * @param string $url Url without /media prefix
     * @return string|null
     */
    protected function _getAssetFile(string $url): ?string
    {
        $file = MEDIA . $url;
        if (file_exists($file)) {
            return $file;
        }

        //return parent::_getAssetFile($url);
        return null;
    }
}
