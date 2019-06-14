<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// ------------> 最原生的 CURD，无关联其他数据。

// hook model_website_modelog_start.php

function well_modelog__create($arr, $d = NULL)
{
    // hook model_website_modelog__create_start.php
    $r = db_insert('website_modelog', $arr, $d);
    // hook model_website_modelog__create_end.php
    return $r;
}

function well_modelog__update($logid, $arr, $d = NULL)
{
    // hook model_website_modelog__update_start.php
    $r = db_update('website_modelog', array('logid' => $logid), $arr, $d);
    // hook model_website_modelog__update_end.php
    return $r;
}

function well_modelog__read($logid, $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_modelog__read_start.php
    $modelog = db_find_one('website_modelog', array('logid' => $logid), $orderby, $col, $d);
    // hook model_website_modelog__read_end.php
    return $modelog;
}

function well_modelog__delete($logid, $d = NULL)
{
    // hook model_website_modelog__delete_start.php
    $r = db_delete('website_modelog', array('logid' => $logid), $d);
    // hook model_website_modelog__delete_end.php
    return $r;
}

function well_modelog__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'id', $col = array(), $d = NULL)
{
    // hook model_website_modelog__find_start.php
    $modeloglist = db_find('website_modelog', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_modelog__find_end.php
    return $modeloglist;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理。

function well_modelog_create($arr)
{
    // hook model_website_modelog_create_start.php
    $r = well_modelog__create($arr);
    // hook model_website_modelog_create_end.php
    return $r;
}

function well_modelog_update($logid, $arr)
{
    // hook model_website_modelog_update_start.php
    $r = well_modelog__update($logid, $arr);
    // hook model_website_modelog_update_end.php
    return $r;
}

function well_modelog_read($logid)
{
    // hook model_website_modelog_read_start.php
    $modelog = well_modelog__read($logid);
    $modelog AND well_modelog_format($modelog);
    // hook model_website_modelog_read_end.php
    return $modelog;
}

function well_modelog_delete($logid)
{
    // hook model_website_modelog_delete_start.php
    $r = well_modelog__delete($logid);
    // hook model_website_modelog_delete_end.php
    return $r;
}

function well_modelog_find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_website_modelog_find_start.php
    $modeloglist = well_modelog__find($cond, $orderby, $page, $pagesize);
    if ($modeloglist) {
        $i = 0;
        foreach ($modeloglist as &$modelog) {
            ++$i;
            $v['i'] = $i;
            well_modelog_format($modelog);
        }
    }
    // hook model_website_modelog_find_end.php
    return $modeloglist;
}

// ----------------> 其他方法

function well_modelog_format(&$modelog)
{
    // hook model_website_modelog_format_start.php
    $conf = GLOBALS('conf');
    $modelog['create_date_fmt'] = date('Y-n-j', $modelog['create_date']);
    // hook model_website_modelog_format_end.php
}

function well_modelog_count($cond = array())
{
    // hook model_website_modelog_count_start.php
    $n = db_count('website_modelog', $cond);
    // hook model_website_modelog_count_end.php
    return $n;
}

function well_modelog_maxid()
{
    // hook model_website_modelog_maxid_start.php
    $n = db_maxid('website_modelog', 'logid');
    // hook model_website_modelog_maxid_end.php
    return $n;
}

// hook model_website_modelog_end.php

?>