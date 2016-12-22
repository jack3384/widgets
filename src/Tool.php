<?php

namespace glacier\widgets;

/**
 * Author: 火柴同学
 * Date: 2016/9/29
 * Time: 9:40
 */

/**
 * 封装了一些实用的debug函数
 *
 * 用于方便的获得调试信息
 */
class Tool
{
    /**
     * 动态的获得对象当前的私有属性值
     *
     * 用法:
     * ```
     * $value=Tool::getPrivateValue('orWhere',$ap)
     * var_dump($value);
     * ```
     *
     * @param string $fieldName 要获取的属性名。
     * @param object $obj 从哪个对象里获取
     * @return mixed 对象的私有属性值。
     */
    public static function getPrivateValue($fieldName, $obj)
    {
        $r = new \ReflectionObject($obj);

        while (true) {
            if (!$r->hasProperty($fieldName)) {
                $p = $r->getParentClass();
                if (!$p) {
                    break;
                } else {
                    $r = $p;
                }
            } else {
                break;
            }
        }
        $sec = $r->getProperty($fieldName);
        $sec->setAccessible(true);
        $value = $sec->getValue($obj);
        return $value;
    }

    /**
     * @param $cmd
     * @return array|bool
     * 返回的$array['exitCode']为子程序退出码执行成功返回的是0
     * proc_open的封装可以获得stderr提示信息
     */
    public static function proc_exec($cmd)
    {
        $descriptorspec = array(
            0 => array("pipe", "r"),
            1 => array("pipe", "w"),
            2 => array("pipe", "w")
        );
        $process = proc_open(
            $cmd,
            $descriptorspec,
            $pipes
        );
        if ($process === false) {
            return false;
        }
        $stdout = stream_get_contents($pipes[1]);
        $stderr = stream_get_contents($pipes[2]);
        $exitCode = proc_close($process);
        return compact('exitCode', 'stdout', 'stderr');
    }

}