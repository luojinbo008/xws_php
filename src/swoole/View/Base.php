<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-22
 * Time: 10:26
 */

namespace Swoole\View;

use Swoole\IFace\View as IView;

abstract class Base implements IView
{
    protected $model;

    /**
     * @param $model
     */
    public function setModel($model)
    {
        $this->model = $model;
    }

    /**
     * @return mixed
     */
    public function getModel()
    {
        return $this->model;
    }

    // 数据输出
    abstract public function display();

    /**
     * @return false|string
     */
    public function render()
    {
        \ob_start();
        $this->display();
        $content = \ob_get_contents();
        \ob_end_clean();
        return $content;
    }
}