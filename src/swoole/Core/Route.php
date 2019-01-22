<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 17:20
 */

namespace Swoole\Core;

use Swoole\IFace\Controller as IController;
use Swoole\Protocol\Request;
use Swoole\Protocol\Response;

class Route
{

    /**
     * @return mixed
     * @throws \Swoole\Exception\NotFound
     */
    public static function route()
    {
        $action = \Swoole\Swoole::$php->config['project']['ctrl'] . "\\" . Request::getCtrl();
        $class = Factory::getInstance($action);

        try {
            if (!($class instanceof IController)) {
                throw new \Exception("ctrl error");
            } else {
                $view = null;
                if ($class->_before()) {
                    $method = Request::getMethod();
                    if (!method_exists($class, $method)) {
                        throw new \Exception("method error");
                    }
                    $view = $class->$method();
                } else {
                    throw new \Exception($action . ':' . Request::getMethod() . ' _before() no return true');
                }
                $class->_after();
                return Response::display($view);
            }
        } catch (\Exception $e) {
            if ($class instanceof IController) {
                $class->_after();
            }
            throw $e;
        }
    }
}