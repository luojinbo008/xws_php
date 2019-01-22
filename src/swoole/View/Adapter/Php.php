<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-22
 * Time: 12:04
 */

namespace Swoole\View\Adapter;

use Swoole\Exception\NotFound;
use Swoole\View\Base;
use Swoole\Protocol\Response;

class Php extends Base
{
    private $tplFile;

    public function setTpl($tpl)
    {
        $this->tplFile = $tpl;
    }

    /**
     * @return false|string|null
     * @throws \Exception
     */
    public function display()
    {
        Response::sendHttpHeader();
        $tplPath = \Swoole\Swoole::$php->config['project']['tpl_path'] ??
            \Swoole\Swoole::$app_path . '/template/default/';

        $fileName = $tplPath . $this->tplFile;

        if (!\is_file($fileName)) {
            throw new NotFound("no file {$fileName}");
        }

        if (!empty($this->model) && is_array($this->model)) {
            \extract($this->model);
        }

        \ob_start();
        include "{$fileName}";
        $content = ob_get_contents();
        \ob_end_clean();
        return $content;
    }
}