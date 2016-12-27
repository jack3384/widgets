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

    /**
     * !!!重要，此功能对部分httpServer无效，比如IIS和其他小的build-in webServer.Apache2.4.X测试有效
     * 将输出传入的数据，并断开与客户端的连接，然后再执行后面的语句。这样即使后面的语句有输出也将无法显示给客户端
     * 可以用作先输出再做记录日志等耗时操作。
     *
     *
     * @param $outputString
     */
    public static function responseNow($outputString)
    {
        ignore_user_abort(true);
        ob_start();
        ob_clean();
        echo $outputString;
        header('Connection:close');
        header('Content-Length:'.ob_get_length());
        ob_end_flush();
        ob_flush(); //必须加这句或者ob_end_flush() 才能立即返回.
        flush();
    }

}