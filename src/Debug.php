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
class Debug
{
    /**
     * 动态的获得对象当前的私有属性值
     *
     * 用法:
     * ```
     * $value=Debug::getPrivateValue('orWhere',$ap)
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

        while(true){
            if (!$r->hasProperty($fieldName)) {
                $p=$r->getParentClass();
                if(!$p){
                    break;
                }else{
                    $r=$p;
                }
            }else{
                break;
            }
        }
        $sec = $r->getProperty($fieldName);
        $sec->setAccessible(true);
        $value = $sec->getValue($obj);
        return $value;
    }

}