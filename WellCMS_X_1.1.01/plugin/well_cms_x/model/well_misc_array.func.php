<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
 
// hook model_website_misc_array_start.php

// 对二维数组排序 $col排序列 $key索引键
function well_array_multisort_key($arrlist, $col, $asc = TRUE, $key = NULL)
{
    if (empty($arrlist)) return array();

    $colarr = array();
    foreach ($arrlist as $k => $arr) {
        $colarr[$k] = $arr[$col];
    }
    $asc = $asc ? SORT_ASC : SORT_DESC;
    array_multisort($colarr, $asc, $arrlist);
    unset($colarr);

    $key AND $arrlist = well_array_change_key($arrlist, $key);

    return $arrlist;
}

// 更改二维数组key
function well_array_change_key($arrlist, $key = NULL)
{
    $arr = array();
    if (!empty($arrlist) && $key) {
        foreach ($arrlist as $k => $val) {
            $arr[$val[$key]] = $val;
        }
    }
    return $arr;
}

// 二维数组分页，对排序的整个数组分页获取数据
function well_array_pagination($arrlist, $page = 1, $pagesize = 20)
{
    if (empty($arrlist)) return array();

    $page = intval($page);
    $pagesize = intval($pagesize);

    // 输出开始位置 第二页开始 +1
    $start = ($page - 1) * $pagesize + ($page > 1 ? 1 : 0);
    // 输出结束位置 当前页数*每页数量
    $end = $page * $pagesize;

    $arr = array();
    $i = 0;
    foreach ($arrlist as $key => $val) {
        ++$i;
        if ($i >= $start && $i <= $end) {
            $arr[$key] = $val;
        }
    }

    return $arr;
}

// 倒叙 二维关联数整理一维关联数组 col排序列 关联key=>value
function well_array_rank_key($arr = array(), $col = NULL, $key = NULL, $value = NULL)
{
    if (!empty($arr) && $col && $key && $value) {
        $arr = arrlist_multisort($arr, $col, FALSE);
        $arr = arrlist_key_values($arr, $key, $value);
    }
    return $arr;
}

function well_unique_array($array2D, $stkeep = false, $ndformat = true)
{
    // 判断是否保留一级数组键 (一级数组键可以为非数字)
    if ($stkeep) $stArr = array_keys($array2D);
    // 判断是否保留二级数组键 (所有二级数组键必须相同)
    if ($ndformat) $ndArr = array_keys(end($array2D));
    //降维,也可以用implode,将一维数组转换为用逗号连接的字符串
    foreach ($array2D as $v) {
        $v = implode(",", $v);
        $temp[] = $v;
    }
    //去掉重复的字符串,也就是重复的一维数组
    $temp = array_unique($temp);
    //再将拆开的数组重新组装
    foreach ($temp as $k => $v) {
        if ($stkeep) $k = $stArr[$k];
        if ($ndformat) {
            $tempArr = explode(",", $v);
            foreach ($tempArr as $ndkey => $ndval) $output[$k][$ndArr[$ndkey]] = $ndval;
        } else $output[$k] = explode(",", $v);
    }
    return $output;
}

// 合并二维数组 如重复 值以第一个数组值为准
function well_array2_merge($array1, $array2, $key = '')
{
    $arr = array();
    foreach ($array1 as $k => $v) {
        if (isset($v[$key])) {
            $arr[$v[$key]] = array_merge($v, $array2[$k]);
        } else {
            $arr[] = array_merge($v, $array2[$k]);
        }
    }

    return $arr;
}

// hook model_website_misc_array_end.php

?>