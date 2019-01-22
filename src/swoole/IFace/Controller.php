<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 17:43
 */
namespace Swoole\IFace;

interface Controller
{
    /**
     * 业务逻辑开始前执行
     */
    public function _before();
    /**
     * 业务逻辑结束后执行
     */
    public function _after();
}