<?php
/**
 * Created by PhpStorm.
 * User: flow
 * Date: 9/6/15
 * Time: 3:09 PM
 */

namespace Media\View\Helper;

use Cake\Core\Plugin;
use Cake\View\Helper;
use Cake\View\View;

class MediaHelper extends Helper
{
    public $helpers = [];

    public function __construct(View $View, array $config = [])
    {
        if (Plugin::loaded('Imagine')) {
            $this->helpers['Imagine.Imagine'];
        }
        parent::__construct($View, $config);
    }

    public function thumbnail($source, $options = [])
    {

    }
}
