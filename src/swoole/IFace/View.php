<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-22
 * Time: 10:27
 */

namespace Swoole\IFace;

interface View
{
    // 存入数据
    public function setModel($model);

    // 获取数据
    public function getModel();

    // 渲染数据
    public function render();
}