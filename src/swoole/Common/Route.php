<?php
/**
 * Created by IntelliJ IDEA.
 * User: luojinbo
 * Date: 2019-01-21
 * Time: 19:17
 */

namespace Swoole\Common;


class Route
{
    /**
     * 路由匹配
     * @param $route
     * @param $pathInfo
     * @return bool
     */
    public static function match($route, $pathInfo)
    {
        if (empty($route) || empty($pathInfo)) {
            return false;
        }
        $pathInfo = explode('.', $pathInfo);
        $pathInfo = $pathInfo[0];

        if (isset($route['static'][$pathInfo])) {
            return $route['static'][$pathInfo];
        }
        foreach ($route['dynamic'] as $regex => $rule) {
            if (!preg_match($regex, $pathInfo, $matches)) {
                continue;
            }
            if (!empty($matches)) {
                unset($matches[0]);
                foreach ($matches as $index => $val) {
                    $rule[0] = str_replace("{{$index}}", $val, $rule[0], $count1);
                    $rule[1] = str_replace("{{$index}}", $val, $rule[1], $count2);
                    if (($count1 + $count2) > 0) {
                        unset($matches[$index]);
                    }
                }
                if (!empty($rule[2]) && !empty($matches)) {
                    $rule[2] = array_combine($rule[2], $matches);
                }
                return $rule;
            }
        }
        return false;
    }
}