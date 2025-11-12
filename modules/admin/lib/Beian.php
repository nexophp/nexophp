<?php

/**
 * 输出备案号
 * @author sunkangchina <68103403@qq.com>
 * @license MIT <https://mit-license.org/>
 * @date 2025
 */

namespace modules\admin\lib;


class Beian
{
    /**
     * 输出所有备案号
     */
    public static function output()
    {
        $footer = self::footer();
        $gx = self::gx();
        $ga = self::ga();
        $str = '';
        if ($footer || $gx || $ga) {
            $str = "<div class='beian'>";
            if ($gx) {
                $str .= $gx;
            }
            if ($ga) {
                $str .= $ga;
            }
            if ($footer) {
                $str .= $footer;
            }
            $str .= "</div>";
        }
        return $str;
    }
    /**
     * 网站统计代码
     */
    public static function footer()
    {
        $code = get_config("app_footer");
        return $code;
    }
    /**
     * 工信部备案号
     */
    public static function gx()
    {
        $code = get_config("app_beian");
        return "<a class='beian_gx' href='https://beian.miit.gov.cn/' target='_blank'>" . $code . "</a>";
    }

    /**
     * 公安备案号
     */
    public static function ga()
    {
        //京公网安备11010802020134号
        $code = get_config("app_ga_beian");
        //正则取数字
        $number = preg_replace("/[^0-9]/", "", $code);
        return "<a class='beian_ga' href='http://www.beian.gov.cn/portal/registerSystemInfo?recordcode=" . $number . "' target='_blank'>" . $code . "</a>";
    }
}
