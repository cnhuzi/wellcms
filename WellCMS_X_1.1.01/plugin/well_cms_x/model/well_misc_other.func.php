<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

// hook model_website_misc_other_start.php

//------------- cache and cookie set / start--------------
function well_cookie_set($key, $value, $life = 8640000)
{
    $time = GLOBALS('time');
    $conf = GLOBALS('conf');
    is_array($value) AND $value = well_json_encode($value);
    setcookie($key, $value, ($time + $life), $conf['cookie_path']);
}

// 清空内存缓存和Cookie
function well_cookie_cache_remove($key, $cookie = TRUE)
{
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    $cookie == TRUE AND setcookie($key, '', $time - 86400, $conf['cookie_path']);
    $conf['cache']['type'] != 'mysql' AND cache_delete($key);
}

// 直接更新 缓存 一维或二维数组 20180501
// 统计 array('键名 + or -' => '加数');
// well_cache_set($arr, $update = array('trash_threads' => 1));
// well_cache_set($arr, $update = array('trash_threads+' => 1));
function well_cache_set($key = NULL, $arr = array(), $life = 0)
{
    if (!$key || empty($arr)) return;

    $conf = GLOBALS('conf');
    $time = GLOBALS('time');

    if ($conf['cache']['type'] != 'mysql') {
        $cache = cache_get($key);
        if ($cache) {
            $cache = well_cache_set_array($cache, $arr);
            cache_set($key, $cache, $life);
        }
    }
}

// 直接更新 缓存 一维或二维数组 20180501
// 统计 array('键名 + or -' => '加数');
// well_cache_set_arr($arr, $update = array('trash_threads' => 1));
// well_cache_set_arr($arr, $update = array('trash_threads+' => 1));
function well_cache_set_array($arr = array(), $update = array())
{
    if (empty($arr) || empty($update)) return TRUE;

    if (count($update) == count($update, 1)) {
        $arr = well_cache_set_one($arr, $update);
    } else {
        foreach ($update as $k => $v) {
            !isset($arr[$k]) AND $arr[$k] = array();
            $arr = well_cache_set_one($arr[$k], $v);
        }
    }
    return $arr;
}

// 直接更新 单条一维数组缓存20180501
// well_cache_set_one($arr, $update = array('trash_threads' => 1));
// well_cache_set_one($arr, $update = array('trash_threads+' => 1));
function well_cache_set_one($arr = array(), $update = array())
{
    if (empty($arr) || empty($update)) return TRUE;
    foreach ($update as $k => $v) {
        $op = substr($k, -1);
        if ($op == '+' || $op == '-') {
            $k = substr($k, 0, -1);
            !isset($arr[$k]) AND $arr[$k] = 0;
            $v = $op == '+' ? ($arr[$k] + $v) : ($arr[$k] - $v);
        }
        $arr[$k] = $v;
    }
    return $arr;
}

//------------- cache and cookie set / end--------------

//---------------表单安全过滤---------------
/*
 * 专门处理表单多维数组安全过滤 指定最终级一维数组key为字符串安全处理
    $string 为需要按照字符串处理的key数组 array('key')
    如需按照int型处理时 $string 数组为空或省略
    $string = array('name','message','brief');
	well_param(1, array(), $string);
    well_param('warm_up', array(), array('name','message','brief'));
*/
function well_param($key, $defval = '', $string = array(), $htmlspecialchars = TRUE, $addslashes = FALSE)
{
    if (!isset($_REQUEST[$key]) || ($key === 0 && empty($_REQUEST[$key]))) {
        if (is_array($defval)) {
            return array();
        } else {
            return $defval;
        }
    }
    $val = $_REQUEST[$key];
    $val = well_param_force($val, $string, $htmlspecialchars, $addslashes);
    return $val;
}

function well_param_force($val, $string, $htmlspecialchars, $addslashes)
{
    if (empty($val)) return array();

    foreach ($val as $k => &$v) {
        if (is_array($v)) {
            $v = well_mulit_array_safe($v, array(), $string, $htmlspecialchars, $addslashes);
        } else {
            $defval = well_safe_defval($k, $string);
            $v = well_safe($v, $defval, $htmlspecialchars, $addslashes);
        }
    }

    return $val;
}

// 遍历多维数组安全过滤 $string一维数组中能找到的一律按照字符处理
function well_mulit_array_safe($array, $arr = array(), $string, $htmlspecialchars, $addslashes)
{
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                well_mulit_array_safe($value, $arr[$key], $string, $htmlspecialchars, $addslashes);
            } else {
                $defval = well_safe_defval($key, $string);
                $arr[$key] = well_safe($value, $defval, $htmlspecialchars, $addslashes);
            }
        }
    }
    return $arr;
}

// 返回1则按照字符串处理
function well_safe_defval($key, $string)
{
    $defval = 0;
    if (is_array($string)) {
        // 限定的 key值 按照字符串处理
        $defval = in_array($key, $string) ? 1 : 0;
    }
    return $defval;
}

// 参数安全处理
function well_safe($val, $defval, $htmlspecialchars, $addslashes)
{
    $get_magic_quotes_gpc = _SERVER('get_magic_quotes_gpc');
    // 处理字符串
    if ($defval == 1) {
        //$val = trim($val);
        $addslashes AND !$get_magic_quotes_gpc && $val = addslashes($val);
        !$addslashes AND $get_magic_quotes_gpc && $val = stripslashes($val);
        $htmlspecialchars AND $val = htmlspecialchars($val);
    } else {
        $val = intval($val);
    }
    return $val;
}

// 专门处理表单多维数组安全过滤 哪些表单限定数字提醒
// well_mulit_array_int(array(), array('id','fid'));
function well_mulit_array_int($array = array(), $string = array())
{
    if (empty($array)) return;

    foreach ($array as $key => $value) {
        if (is_array($value)) {
            well_mulit_array_int($value, $string);
        } else {
            if (in_array($key, $string) && !is_numeric($value)) message(1, lang('well_please_fill_in_the_numbers'));
        }
    }
}

//---------------表单安全过滤结束---------------

// 编码转utf-8
function well_code_to_utf8($str)
{
    $encoding = mb_detect_encoding($str, array('GB2312', 'BIG5', 'ASCII', 'GBK', 'UTF-16', 'UCS-2', 'UTF-8'));

    if ($encoding != false) {
        $str = iconv($encoding, 'UTF-8', $str);
    } else {
        $str = mb_convert_encoding($str, 'UTF-8', 'Unicode');
    }

    return $str;
}

// 过滤用户昵称里面的特殊字符
function well_filter_username($username)
{
    $username = preg_replace_callback('/./u', "well_match_username", $username);
    return $username;
}

function well_match_username($match)
{
    return strlen($match[0]) >= 4 ? '' : $match[0];
}

// check plugin installation / $dir插件目录名
function well_check_plugin($dir, $file = NULL, $return = FALSE)
{
    $r = well_pull_plugin_info($dir);
    if (empty($r)) return FALSE;

    $destpath = APP_PATH . 'plugin/' . $dir . '/';

    if ($file) {
        $getfile = $destpath . $file;
        $str = file_get_contents($getfile);
        return $return ? htmlspecialchars($str) : $str;
    } else {
        if ($r['installed'] && $r['enable']) {
            return TRUE;
        } else {
            return FALSE;
        }
    }
}

// pull plugin info
function well_pull_plugin_info($dir)
{
    $destpath = APP_PATH . 'plugin/' . $dir . '/';
    if (!file_exists($destpath)) return FALSE;

    $conffile = $destpath . 'conf.json';
    $r = xn_json_decode(file_get_contents($conffile));
    return $r;
}

// replace xn json placeholder
function well_json_encode($arr)
{
    $search = array(" ", "　", "\n", "\r", "\t");
    $replace = array('', '', '', '', '');
    return str_replace($search, $replace, xn_json_encode($arr));
}

// 0:pc 1:wechat 2:mobile 3:pad
function well_detect_device()
{
    $agent = _SERVER('HTTP_USER_AGENT');

    if (strpos($agent, 'MicroMessenger') !== false) {

        return 1;//微信

    } elseif (isset($_SERVER['HTTP_X_WAP_PROFILE']) || (isset($_SERVER['HTTP_VIA']) && stristr($_SERVER['HTTP_VIA'], "wap") || stripos($agent, 'phone') || stripos($agent, 'mobile') || strpos($agent, 'ipod'))) {

        return 2;// 手机

    } elseif (strpos($agent, 'pad') !== FALSE) {

        return 3;//pad;

    }

    return 0;
}

// random string, no number
function well_rand_str($length)
{
    $str = 'QWERTYUPASDFGHJKLZXCVBNMqwertyupasdfghjkzxcvbnm';
    return substr(str_shuffle($str), 26, $length);
}

// html换行转换为\r\n
function well_br_to_code($data)
{
    //$data = htmlspecialchars_decode($data);
    return str_replace("<br>", "\r\n", $data);
}

// 直接传message 也可以传数组$arr = array('message' => message, 'doctype' => 1, 'gid' => $gid)
// 格式转换: 类型，0: html, 1: txt; 2: markdown; 3: ubb
// 入库时进行转换，编辑时再转码
function well_code_safe($arr)
{
    if (!$arr) return array();

    // 如果没有传doctype变量 默认为 1 txt安全格式
    $doctype = isset($arr['doctype']) ? $arr['doctype'] : 1;
    $gid = !empty($arr['gid']) ? $arr['gid'] : 0;
    $_message = isset($arr['message']) ? $arr['message'] : $arr;

    if ($_message) {
        // 格式转换: 类型，0: html, 1: txt; 2: markdown; 3: ubb
        $message = htmlspecialchars($_message, ENT_QUOTES);
        //$message = htmlspecialchars($message);
        // html格式过滤不安全代码 管理员html格式时不转换
        $doctype == 0 && $message = ($gid == 1 ? $_message : xn_html_safe($_message));
        // text转html格式\r\n会被转换html代码
        $doctype == 1 && $message = xn_txt_to_html($_message);
    }

    return $message;
}

// 过滤所有html标签
function well_filter_all_html($text)
{
    $text = trim($text);
    $text = stripslashes($text);
    $text = strip_tags($text);
    $text = str_replace(array("\r\n", "\r", "\n", '  ', '   ', '    ', '	'), '', $text);
    //$text = htmlspecialchars($text, ENT_QUOTES); // 入库前保留干净，入库时转码 输出时无需htmlspecialchars_decode()
    return $text;
}

function well_filter_html($text)
{
    $well_conf = GLOBALS('website_conf');
    $filter = array_value($well_conf, 'filter');
    $arr = array_value($filter, 'content');
    $html_enable = array_value($arr, 'html_enable');
    $html_tag = array_value($arr, 'html_tag');

    if ($html_enable == 0 || empty($html_tag)) return TRUE;
    $html_tag = htmlspecialchars_decode($html_tag);

    $text = trim($text);
    $text = stripslashes($text);
    // 过滤动态代码
    //$text = preg_replace('/<\?|\?' . '>/', '', $text);
    //$text = preg_replace('@<script(.*?)</script>@is', '', $text);
    //$text = preg_replace('@<iframe(.*?)</iframe>@is', '', $text);
    //$text = preg_replace('@<style(.*?)</style>@is', '', $text);
    $text = strip_tags($text, "$html_tag"); // 需要保留的字符在后台设置
    $text = str_replace(array("\r\n", "\r", "\n", '  ', '   ', '    ', '	'), '', $text);
    //$text = preg_replace('/\s+/', '', $text);//空白区域 会过滤图片等
    //$text = preg_replace("@<(.*?)>@is", "", $text);
    // 过滤所有的style
    $text = preg_replace("/style=.+?['|\"]/i", '', $text);
    // 过滤所有的class
    $text = preg_replace("/class=.+?['|\"]/i", '', $text);
    // 获取img= 过滤标签中其他属性
    $text = preg_replace("/<img\s*src=(\"|\')(.*?)\\1[^>]*>/is", '<img src="$2" />', $text);

    return $text;
}

// filter keyword
function well_filter_keyword($keyword, $type, &$error)
{
    $well_conf = GLOBALS('website_conf');
    $filter = array_value($well_conf, 'filter');
    $arr = array_value($filter, $type);
    $enable = array_value($arr, 'enable');
    $wordarr = array_value($arr, 'keyword');

    if ($enable == 0 || empty($wordarr)) return FALSE;

    foreach ($wordarr as $_keyword) {
        $r = strpos(strtolower($keyword), strtolower($_keyword));
        if ($r !== FALSE) {
            $error = $_keyword;
            return TRUE;
        }
    }
    return FALSE;
}

function well_http_location($url = NULL)
{
    !$url AND $url = './';
    header('Location:' . $url);
    exit;
}

// 返回判断https域名
function well_return_domain()
{
    $http = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https')) ? 'https://' : 'http://';
    return $http . $_SERVER['HTTP_HOST'];
}

// 唯一身份ID
function well_uniq_id()
{
    return uniqid(substr(md5(microtime(true) . mt_rand(1000, 9999)), 8, 8));
}

// 生成订单号 14位
function well_trade_no()
{
    $trade_no = str_replace('.', '', microtime(1));
    $strlen = mb_strlen($trade_no, 'UTF-8');
    $strlen = 14 - $strlen;
    $str = '';
    if ($strlen) {
        for ($i = 0; $i <= $strlen; $i++) {
            if ($i < $strlen) $str .= '0';
        }
    }
    return $trade_no . $str;
}

// 生成订单号 16位
function well_trade_no_16()
{
    $explode = explode(' ', microtime());
    $trade_no = $explode[1] . mb_substr($explode[0], 2, 6, 'UTF-8');
    return $trade_no;
}

// 当前年的天数
function well_date_year($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('L', $time) + 365;
}

// 当前年份中的第几天
function well_date_z($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('z', $time);
}

// 当前月份中的第几天，没有前导零 1 到 31
function well_date_j($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('j', $time);
}

// 当前月份中的第几天，有前导零的2位数字 01 到 31
function well_date_d($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('d', $time);
}

// 当前时间为星期中的第几天 数字表示 1表示星期一 到 7表示星期天
function well_date_w_n($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('N', $time);
}

// 当前日第几周
function well_date_d_w($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('W', $time);
}

// 当前几月 没有前导零1-12
function well_date_n($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('n', $time);
}

// 当前月的天数
function well_date_t($time = NULL)
{
    $time = intval($time) ? $time : time();
    return date('t', $time);
}

// 0 o'clock on the day
function well_clock_zero()
{
    return strtotime(date('Ymd'));
}

// 24 o'clock on the day
function well_clock_twenty_four()
{
    return strtotime(date('Ymd')) + 86400;
}

// 8点过期 / expired at 8 a.m.
function well_eight_expired($time = NULL)
{
    $time = intval($time) ? $time : time();
    // 当前时间大于8点则改为第二天8点过期
    $life = date('G') <= 8 ? (strtotime(date('Ymd')) + 28800 - $time) : well_clock_twenty_four() - $time + 28800;
    return $life;
}

// 24点过期 / expired at 24 a.m.
function well_twenty_four_expired($time = NULL)
{
    $time = intval($time) ? $time : time();
    $twenty_four = well_clock_twenty_four();
    $life = $twenty_four - $time;
    return $life;
}

function well_https_request($url, $data = NULL)
{
    if (!function_exists('curl_init')) {
        throw new Exception('server not install curl');
    }

    $curl = curl_init();
    ///////////20161212添加////////////
    //php5.5跟php5.6中的CURLOPT_SAFE_UPLOAD的默认值不同
    if (class_exists('\CURLFile')) {
        curl_setopt($curl, CURLOPT_SAFE_UPLOAD, true);
    } else {
        if (defined('CURLOPT_SAFE_UPLOAD')) {
            curl_setopt($curl, CURLOPT_SAFE_UPLOAD, false);
        }
    }
    ///////////////////////////////////
    curl_setopt($curl, CURLOPT_URL, $url);
    // 兼容HTTPS
    if (stripos($url, 'https://') !== FALSE) {
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        //ssl版本控制
        //curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1);
        curl_setopt($curl, CURLOPT_SSLVERSION, 1);
    }
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        // 使用自动跳转
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        // 自动设置Referer
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    //优先解析 IPv6 超时后IPv4
    //curl_setopt($curl, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

// 绝对路径 获取图片信息:数组反悔[0]宽度 [1]高度 [2]类型 返回数字，其中1 = GIF，2 = JPG，3 = PNG，4 = SWF，5 = PSD，6 = BMP，7 = TIFF(intel byte order)，8 = TIFF(motorola byte order)，9 = JPC，10 = JP2，11 = JPX，12 = JB2，13 = SWC，14 = IFF，15 = WBMP，16 = XBM
function well_image_size($image_url)
{
    return getimagesize($image_url);
}

// 计算字串长度:剧中对齐(字体大小/字串内容/字体链接/背景宽度/倍数)
function well_calculate_str_with($size, $str, $font, $with, $multiple = 2)
{
    $box = imagettfbbox($size, 0, $font, $str);
    return ($with - $box[4] - $box[6]) / $multiple;
}

// hook model_website_misc_other_end.php

?>