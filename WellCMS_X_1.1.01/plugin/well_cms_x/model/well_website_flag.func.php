<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// hook model_website_flag_start.php

// ------------> 最原生的 CURD，无关联其他数据。
function well_website_flag__create($arr = array(), $d = NULL)
{
    // hook model_website_flag__create_start.php
    $r = db_insert('website_flag', $arr, $d);
    // hook model_website_flag__create_end.php
    return $r;
}

function well_website_flag__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_website_flag__update_start.php
    $r = db_update('website_flag', $cond, $update, $d);
    // hook model_website_flag__update_end.php
    return $r;
}

function well_website_flag__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_flag__read_start.php
    $r = db_find_one('website_flag', $cond, $orderby, $col, $d);
    // hook model_website_flag__read_end.php
    return $r;
}

function well_website_flag__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'flagid', $col = array(), $d = NULL)
{
    // hook model_website_flag__find_start.php
    $arr = db_find('website_flag', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_flag__find_end.php
    return $arr;
}

function well_website_flag_delete($flagid, $d = NULL)
{
    // hook model_website_flag_delete_start.php
    $conf = GLOBALS('conf');
    // hook model_website_flag_delete_before.php
    if (is_array($flagid)) {
        $r = db_delete('website_flag', array('flagid' => $flagid), $d);
    } else {
        $read = well_website_flag_read($flagid);
        if (!$read) return FALSE;
        // hook model_website_flag_delete_read_start.php
        $r = db_delete('website_flag', array('flagid' => $flagid), $d);
        if ($r === FALSE) return FALSE;
        // hook model_website_flag_delete_read_before.php
        if ($conf['cache']['type'] != 'mysql') {
            cache_delete('website_flag_' . $flagid);
            cache_delete('website_flag_' . md5($read['name']));
        }
        // hook model_website_flag_delete_after.php
    }
    // hook model_website_flag_delete_end.php
    return $r;
}

function well_website_flag__count($cond = array(), $d = NULL)
{
    // hook model_website_flag_count_start.php
    $n = db_count('website_flag', $cond, $d);
    // hook model_website_flag_count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_website_flag_create($arr)
{
    if (empty($arr)) return FALSE;
    $conf = GLOBALS('conf');
    // hook model_website_flag_create_start.php
    $r = well_website_flag__create($arr);
    if ($r === FALSE) return FALSE;

    if ($conf['cache']['type'] != 'mysql') {
        cache_delete('website_flag_arrlist_' . $arr['fid']);
    }

    cache_delete('website_flag_index');
    cache_delete('website_flag_category_' . $arr['fid']);
    cache_delete('website_flag_forum_' . $arr['fid']);

    // hook model_website_flag_create_end.php
    return $r;
}

function well_website_flag_update($flagid, $update)
{
    if (!$flagid || empty($update)) return FALSE;

    $conf = GLOBALS('conf');

    // hook model_website_flag__update_start.php

    $r = well_website_flag__update(array('flagid' => $flagid), $update);

    $read = well_website_flag_read($flagid);

    if ($conf['cache']['type'] != 'mysql' && !is_array($flagid)) {
        cache_delete('website_flag_arrlist_' . $read['fid']);
        cache_delete('website_flag_arrlist_0');
    }

    if (!is_array($flagid)) {
        cache_delete('website_flag_index');
        cache_delete('website_flag_category_' . $read['fid']);
        cache_delete('website_flag_forum_' . $read['fid']);
    }

    // hook model_website_flag__update_end.php
    return $r;
}

// 属性查询
function well_website_flag_read_by_name($name)
{
    if (!$name) return NULL;
    // hook model_website_flag_read_name_start.php
    $r = well_website_flag__read(array('name' => $name));
    // hook model_website_flag_read_name_end.php
    return $r;
}

// 栏目下属性查询
function well_website_flag_read_by_name_and_fid($name, $fid)
{
    if (!$name) return NULL;
    // hook model_website_flag_read_name_start.php
    $r = well_website_flag__read(array('name' => $name, 'fid' => $fid));
    // hook model_website_flag_read_name_end.php
    return $r;
}

function well_website_flag_read($flagid)
{
    if (!$flagid) return NULL;
    // hook model_website_flag_read_start.php
    $r = well_website_flag__read(array('flagid' => $flagid));
    $r AND well_website_flag_format($r);
    // hook model_website_flag_read_end.php
    return $r;
}

// 栏目所有属性
function well_website_flag_find($fid, $page, $pagesize)
{
    // hook model_website_flag_find_start.php
    $arrlist = well_website_flag__find(array('fid' => $fid), array('flagid' => -1), $page, $pagesize, 'flagid');

    if (!$arrlist) return NULL;

    // hook model_website_flag_find_before.php

    $i = 0;
    foreach ($arrlist as &$val) {
        ++$i;
        $val['i'] = $i;
        well_website_flag_format($val);
        // hook model_website_flag_find_after.php
    }

    // hook model_website_flag_find_end.php

    return $arrlist;
}

function well_website_flag_count($fid)
{
    // hook model_website_flag_count_start.php
    $n = well_website_flag__count(array('fid' => $fid));
    // hook model_website_flag_count_end.php
    return $n;
}

// 需要显示的属性 fid=0 AND display=1为首页显示
function well_website_flag_find_by_fid_display($fid, $display, $page, $pagesize)
{
    // hook model_website_flag_find_by_fid_display_start.php
    //$forumlist = GLOBALS('forumlist');
    $arrlist = well_website_flag__find(array('fid' => $fid, 'display' => $display), array('flagid' => -1), $page, $pagesize, 'flagid');
    if (!$arrlist) return NULL;

    // hook model_website_flag_find_by_fid_display_before.php

    $i = 0;
    foreach ($arrlist as &$val) {
        ++$i;
        $val['i'] = $i;
        well_website_flag_format($val);
        // hook model_website_flag_find_by_fid_display_after.php
    }

    // hook model_website_flag_find_by_fid_display_end.php

    return $arrlist;
}

// 批量查询属性信息
function well_website_flag_find_by_flagid($flagids, $page, $pagesize)
{
    if (!$flagids) return NULL;

    // hook model_website_flag_find_by_flagid_start.php

    $arrlist = well_website_flag__find(array('flagid' => $flagids), array('flagid' => -1), $page, $pagesize, 'flagid');

    // hook model_website_flag_find_by_flagid_before.php

    $i = 0;
    foreach ($arrlist as &$val) {
        ++$i;
        $val['i'] = $i;
        well_website_flag_format($val);
        // hook model_website_flag_find_by_flagid_after.php
    }

    // hook model_website_flag_find_by_flagid_end.php

    return $arrlist;
}

function well_website_flag_count_by_fid_display($fid, $display)
{
    // hook model_website_flag_count_by_fid_display_start.php
    $n = well_website_flag__count(array('fid' => $fid, 'display' => $display));
    // hook model_website_flag_count_by_fid_display_end.php
    return $n;
}

function well_website_flag_format(&$val)
{
    if (empty($val)) return;
    $conf = GLOBALS('conf');
    $forumlist = GLOBALS('forumlist');
    // hook model_website_flag_format_start.php
    $forum = array_value($forumlist, $val['fid']);
    $val['column_name'] = $forum ? $forum['name'] : lang('well_index');
    $val['display_text'] = $val['display'] ? lang('well_display_yes') : lang('well_display_no');
    $val['forum_url'] = $forum ? well_nav_format($forum) : './';
    $val['url'] = well_flag_url($val['flagid']);
    $val['create_date_text'] = date('Y-m-d', $val['create_date']);
    $val['icon_text'] = $val['icon'] ? $conf['upload_path'] . 'website_flag/' . $val['flagid'] . '.png' : '';
    // hook model_website_flag_format_end.php
}

//---------------website_flag_thread-----------------

function well_website_flag_thread_create($arr)
{
    // hook model_website_flag_thread_create_start.php
    $r = db_insert('website_flag_thread', $arr);
    // hook model_website_flag_thread_create_end.php
    return $r;
}

// 通过tid更新栏目，审核主题时可使用
function well_website_flag_thread_update_by_tid($tid, $update)
{
    if (!$tid || empty($update)) return FALSE;
    // hook model_website_flag_thread_update_by_tid_start.php
    $r = db_update('website_flag_thread', array('tid' => $tid), $update);
    if ($r === FALSE) return FALSE;
    // hook model_website_flag_thread_update_by_tid_end.php
    return $r;
}

function well_website_flag_thread_delete($id)
{
    // hook model_website_flag_thread_delete_start.php
    $r = db_delete('website_flag_thread', array('id' => $id));
    if ($r === FALSE) return FALSE;
    // hook model_website_flag_thread_delete_end.php
    return $r;
}

function well_website_flag_thread_delete_by_tid($tid)
{
    // hook model_website_flag_thread_delete_by_tid_start.php
    $r = db_delete('website_flag_thread', array('tid' => $tid));
    if ($r === FALSE) return FALSE;
    // hook model_website_flag_thread_delete_by_tid_end.php
    return $r;
}

// 清空主题 大数据量有可能超时 优化时改成遍历主键删除
function well_website_flag_thread_delete_by_flagid($flagid)
{
    // hook model_website_flag_thread_delete_by_flagid_start.php
    $r = db_delete('website_flag_thread', array('flagid' => $flagid));
    if ($r === FALSE) return FALSE;
    // hook model_website_flag_thread_delete_by_flagid_end.php
    return $r;
}

function well_website_flag_thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = '')
{
    // hook model_website_flag_thread__find_start.php
    $arr = db_find('website_flag_thread', $cond, $orderby, $page, $pagesize, $key);
    // hook model_website_flag_thread__find_end.php
    return $arr;
}

function well_website_flag_thread__count($cond = array())
{
    // hook model_website_flag_thread__count_start.php
    $n = db_count('website_flag_thread', $cond);
    // hook model_website_flag_thread__count_end.php
    return $n;
}

//--------------------------强相关--------------------------
// 遍历主题属性 使用时可以不用统计 一般超不过10个
function well_website_flag_thread_find_by_id($id, $page, $pagesize)
{
    if (!$id) return NULL;
    // hook model_website_flag_thread_find_by_id_start.php
    $threadlist = well_website_flag_thread__find(array('id' => $id), array(), $page, $pagesize, 'id');
    // hook model_website_flag_thread_find_by_id_end.php
    return $threadlist;
}

function well_website_flag_thread_find($tid, $page, $pagesize)
{
    if (!$tid) return NULL;
    // hook model_website_flag_thread_find_start.php
    $threadlist = well_website_flag_thread__find(array('tid' => $tid), array(), $page, $pagesize, 'id');
    // hook model_website_flag_thread_find_end.php
    return $threadlist;
}

function well_website_flag_thread_count($tid)
{
    // hook model_website_flag_thread_count_start.php
    $n = well_website_flag_thread__count(array('tid' => $tid));
    // hook model_website_flag_thread_count_end.php
    return $n;
}

function well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize)
{
    if (!$flagid) return NULL;
    // hook model_website_flag_thread_find_by_flagid_fid_start.php
    $threadlist = well_website_flag_thread__find(array('flagid' => $flagid), array('id' => -1), $page, $pagesize, 'id');
    // hook model_website_flag_thread_find_by_flagid_fid_end.php
    return $threadlist;
}

function well_website_flag_thread_count_by_flagid($flagid)
{
    // hook model_website_flag_thread_count_by_flagid_fid_start.php
    $n = well_website_flag_thread__count(array('flagid' => $flagid));
    // hook model_website_flag_thread_count_by_flagid_fid_end.php
    return $n;
}

function well_website_flag_thread_find_by_flagid_fid($flagid, $fid, $page, $pagesize)
{
    if (!$flagid || !$fid) return NULL;
    // hook model_website_flag_thread_find_by_flagid_fid_start.php
    $threadlist = well_website_flag_thread__find(array('flagid' => $flagid, 'fid' => $fid), array('id' => -1), $page, $pagesize, 'id');
    //$threadlist = krsort($threadlist);
    // hook model_website_flag_thread_find_by_flagid_fid_end.php
    return $threadlist;
}

function well_website_flag_thread_count_by_tid($tid)
{
    // hook model_website_flag_thread_count_by_tid_fid_start.php
    $n = well_website_flag_thread__count(array('tid' => $tid));
    // hook model_website_flag_thread_count_by_tid_fid_end.php
    return $n;
}

function well_website_flag_thread_delete_all_by_tid($tid)
{
    // hook model_website_flag_thread_delete_all_by_tid_start.php
    if (!$tid) return FALSE;

    $conf = GLOBALS('conf');
    // hook model_website_flag_thread_delete_all_by_tid_before.php

    $n = well_website_flag_thread_count_by_tid($tid);
    if ($n) {
        $arrlist = well_website_flag_thread_find($tid, 1, $n);
        // hook model_website_flag_thread_delete_all_by_tid_foreach_before.php
        $flagarr = $ids = array();
        foreach ($arrlist as $val) {
            $flagarr[] = $val['flagid'];
            $ids[] = $val['id'];
            $conf['cache']['type'] != 'mysql' AND cache_delete('website_flag_arrlist_' . $val['fid']);
            // hook model_website_flag_thread_delete_all_by_tid_center.php
        }

        // hook model_website_flag_thread_delete_all_by_tid_foreach_after.php

        // 主键更新
        well_website_flag_update($flagarr, array('count-' => 1));

        // 主键删除
        well_website_flag_thread_delete($ids);

        // hook model_website_flag_thread_delete_all_by_tid_after.php
    }

    // hook model_website_flag_thread_delete_all_by_tid_end.php

    return TRUE;
}

//--------------------------cache--------------------------

function well_website_flag_read_by_name_cache($name)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_read_by_name_cache_start.php
    if (!$name) return NULL;
    $key = 'website_flag_' . md5($name);
    // hook model_website_flag_read_by_name_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_website_flag_read_by_name($name);
            $r AND cache_set($key, $r, 300);
        }
    } else {
        $r = well_website_flag_read_by_name($name);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_flag_read_by_name_cache_end.php
    return $cache[$key];
}

function well_website_flag_read_by_name_and_fid_cache($name, $fid)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_read_by_name_and_fid_cache_start.php
    if (!$name) return NULL;
    $key = 'website_flag_' . $fid . '_' . md5($name);
    // hook model_website_flag_read_by_name_and_fid_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_website_flag_read_by_name_and_fid($name, $fid);
            $r AND cache_set($key, $r, 300);
        }
    } else {
        $r = well_website_flag_read_by_name_and_fid($name, $fid);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_flag_read_by_name_and_fid_cache_end.php
    return $cache[$key];
}

// 批量查询属性信息 有缓存
function well_website_flag_find_by_flagid_cache($flagids, $page, $pagesize)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_find_by_flagid_cache_start.php

    $key = 'website_flag_' . md5(well_json_encode($flagids));
    // hook model_website_flag_find_by_flagid_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_website_flag_find_by_flagid($flagids, $page, $pagesize);
            $r AND cache_set($key, $r, 180);
        }
    } else {
        $r = well_website_flag_find_by_flagid($flagids, $page, $pagesize);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_flag_find_by_flagid_cache_end.php
    return $cache[$key];
}

// 单独调用某个属性获取主题ID
function well_website_flag_thread_find_by_flagid_cache($flagid, $page, $pagesize)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_thread_find_by_flagid_cache_start.php

    $key = 'website_flag_' . $flagid . '_' . $page;
    // hook model_website_flag_thread_find_by_flagid_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize);
            $r AND cache_set($key, $r, 180);
        }
    } else {
        $r = well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_flag_thread_find_by_flagid_cache_end.php
    return $cache[$key];
}

//--------------------------其他方法--------------------------
// 各栏目所有需要显示的flag   $fid = 0首页
function well_website_flag_find_by_fid_display_cache($fid, $display = 1, $page = 1, $pagesize = 10)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_find_by_fid_display_cache_start.php

    $key = 'website_flag_arrlist_' . $fid;
    static $cache = array(); // 跨进程，需再加一层缓存： redis/memcached/xcache/apc/

    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_flag_find_by_fid_display_cache_before.php

    if ($conf['cache']['type'] != 'mysql') {
        $arr = cache_get($key);
        if ($arr === NULL) {
            $arr = well_website_flag_find_by_fid_display($fid, $display, $page, $pagesize);
            cache_set($key, $arr);
        }
    } else {
        $arr = well_website_flag_find_by_fid_display($fid, $display, $page, $pagesize);
    }

    // hook model_website_flag_find_by_fid_display_cache_after.php

    $cache[$key] = !empty($arr) ? $arr : NULL;

    // hook model_website_flag_find_by_fid_display_cache_end.php

    return $cache[$key];
}

// 首页属性主题调用缓存
function well_website_flag_index_cache()
{
    // hook model_website_flag_index_cache_start.php

    $key = 'website_flag_index';
    static $cache = array(); // 跨进程，需再加一层缓存： redis/memcached/xcache/apc/

    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_flag_index_cache_before.php

    $arr = cache_get($key);
    if ($arr === NULL) {
        $arr = well_website_flag_index();
        cache_set($key, $arr);
    }

    // hook model_website_flag_index_cache_after.php

    $cache[$key] = !empty($arr) ? $arr : NULL;

    // hook model_website_flag_index_cache_end.php

    return $cache[$key];
}

// 获取首页属性主题
function well_website_flag_index()
{
    // hook model_website_flag_index_start.php

    // 获取首页展示的flag
    $arrlist = well_website_find_flag(0);
    if (!$arrlist) return NULL;

    // 自定义排序
    $arrlist = well_array_multisort_key($arrlist, 'rank', FALSE, 'flagid');
    // hook model_website_flag_index_after.php

    $arr = well_website_flag_find_thread($arrlist);

    // hook model_website_flag_index_end.php

    return $arr;
}

// 获取频道显示的主题数据 自定义模式时使用
function well_website_flag_category_thread_cache($fid)
{
    // hook model_website_flag_category_thread_cache_start.php

    $key = 'website_flag_category_' . $fid;
    static $cache = array(); // 跨进程，需再加一层缓存： redis/memcached/xcache/apc/

    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_flag_category_thread_cache_before.php

    $arr = cache_get($key);
    if ($arr === NULL) {
        $arr = well_website_flag_category_thread($fid);
        $arr AND cache_set($key, $arr);
    }

    // hook model_website_flag_category_thread_cache_after.php

    $cache[$key] = !empty($arr) ? $arr : NULL;

    // hook model_website_flag_category_thread_cache_end.php

    return $cache[$key];
}

// 获取频道显示的主题数据 自定义模式时使用
function well_website_flag_category_thread($fid)
{
    // hook model_website_flag_category_thread_start.php

    // 获取首页展示的flag
    $arrlist = well_website_find_flag($fid);
    if (!$arrlist) return NULL;

    // 自定义排序
    $arrlist = well_array_multisort_key($arrlist, 'rank', FALSE, 'flagid');

    // hook model_website_flag_category_thread_after.php

    $arr = well_website_flag_find_thread($arrlist);

    // hook model_website_flag_category_thread_end.php

    return $arr;
}

// 获取栏目属性主题
function well_website_flag_forum($fid)
{
    // hook model_website_flag_forum_start.php
    $forumlist = GLOBALS('forumlist');
    $forum = array_value($forumlist, $fid);

    if (!$forum) return NULL;

    if ($forum['well_flag']) {
        /*$n = count($forum['well_flag_text']);
        // 遍历属性
        $arrlist = well_website_flag_find_by_flagid($forum['well_flag_text'], 1, $n);*/
        $arrlist = $forum['well_flag_text'];
    } else {
        // 遍历所有需要在首页显示的flag
        $arrlist = well_website_find_flag($fid);
        // 自定义排序
        $arrlist = well_array_multisort_key($arrlist, 'rank', FALSE, 'flagid');
    }

    if (!$arrlist) return NULL;

    // hook model_website_flag_forum_after.php

    $arr = well_website_flag_find_thread($arrlist);

    // hook model_website_flag_forum_end.php

    return $arr;
}

// 获取各版属性 fid = 0 为首页
function well_website_find_flag($fid = 0)
{
    // hook model_website_find_flag_start.php

    $n = well_website_flag_count($fid, 1);
    // 遍历所有需要显示的flag
    $arrlist = $n ? well_website_flag_find_by_fid_display_cache($fid, 1, 1, $n) : NULL;

    // hook model_website_find_flag_end.php

    return $arrlist;
}

// 栏目各属性主题调用缓存
function well_website_flag_forum_cache($fid)
{
    $conf = GLOBALS('conf');

    // hook model_website_flag_forum_cache_start.php

    $key = 'website_flag_forum_' . $fid;
    static $cache = array(); // 跨进程，需再加一层缓存： redis/memcached/xcache/apc/

    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_flag_forum_cache_before.php

    $arr = cache_get($key);
    if ($arr === NULL) {
        $arr = well_website_flag_forum($fid);
        cache_set($key, $arr);
    }

    // hook model_website_flag_forum_cache_after.php

    $cache[$key] = !empty($arr) ? $arr : NULL;

    // hook model_website_flag_forum_cache_end.php

    return $cache[$key];
}

/*
Array
(
    [12] => Array
        (
            [name] => 推荐
            [url] => flag-12.htm
            [list] => Array
                (
                    [35] => Array
                        (
                            [tid] => 35
                            [fid] => 1
                            [subject] => eeee
                            [type] => 0
                            [link] => 0
                            [top] => 0
                            [uid] => 1
                            [icon] => 1538906907
                            [userip] => 2130706433
                            [create_date] => 1538906907
                            [views] => 0
                            [posts] => 0
                            [images] => 0
                            [files] => 0
                            [modes] => 0
                            [status] => 0
                            [closed] => 0
                            [lastuid] => 0
                            [last_date] => 1538906907
                            [brief] => eereeee
                            [keyword] =>
                            [description] =>
                            [create_date_fmt] => 2018-10-07
                            [last_date_fmt] =>
                            [username] => admin
                            [user_avatar_url] => view/img/avatar.png
                            [user] => Array
                                (
                                    [uid] => 1
                                    [gid] => 1
                                    [email] => admin@admin.com
                                    [username] => admin
                                    [realname] =>
                                    [idnumber] =>
                                    [password] => ab5bf0c911ae416791b5328cfb0b2543
                                    [password_sms] =>
                                    [salt] => D22S33VCUMX5XMGZ
                                    [mobile] =>
                                    [qq] =>
                                    [threads] => 0
                                    [posts] => 0
                                    [credits] => 0
                                    [golds] => 0
                                    [rmbs] => 0
                                    [create_ip] => 2130706433
                                    [create_date] => 1538291664
                                    [login_ip] => 2130706433
                                    [login_date] => 1538492161
                                    [logins] => 4
                                    [avatar] => 0
                                    [create_ip_fmt] => 127.0.0.1
                                    [create_date_fmt] => 2018-09-30
                                    [login_ip_fmt] => 127.0.0.1
                                    [login_date_fmt] => 2018-10-02
                                    [groupname] =>
                                    [avatar_url] => view/img/avatar.png
                                    [avatar_path] =>
                                    [online_status] => 1
                                )

                            [column_name] => 好文
                            [lastusername] =>
                            [url] => read-35.htm
                            [user_url] => user-35.htm
                            [top_class] =>
                            [icon_text] => upload/website_mainpic/201810/35.jpeg?1538906907
                            [pages] => 0
                            [allowupdate] => 1
                            [allowdelete] => 1
                        )
                )
        )
)
 * */
// 遍历主题
function well_website_flag_find_thread($arrlist)
{
    // hook model_website_flag_find_thread_start.php

    $arr = array();
    foreach ($arrlist as &$val) {

        // hook model_website_flag_find_thread_foreach_start.php

        // 遍历tid
        $tidlist = well_website_flag_thread_find_by_flagid($val['flagid'], 1, $val['number']);
        if (!$tidlist) continue;

        // hook model_website_flag_find_thread_foreach_before.php

        $tidarr = arrlist_values($tidlist, 'tid');
        unset($tidlist);

        // hook model_website_flag_find_thread_foreach_after.php

        // 遍历主题
        $n = count($tidarr);
        $arr[$val['flagid']]['name'] = $val['name'];
        $arr[$val['flagid']]['url'] = well_flag_url($val['flagid']);
        $arr[$val['flagid']]['list'] = well_thread_find($tidarr, $n);
        unset($tidarr);

        // hook model_website_flag_find_thread_foreach_end.php
    }

    // hook model_website_flag_find_thread_end.php

    return $arr;
}

// 传入flagid数组 创建属性主题$type 1首页 2频道 3栏目
function well_website_flagidarr_db_create($fid, $type, $tid, $flagarr)
{
    if (!$tid || empty($flagarr)) return FALSE;
    // hook model_website_flagidarr_db_create_start.php
    $time = GLOBALS('time');

    foreach ($flagarr as &$val) {
        // hook model_website_flagidarr_db_create_before.php
        if ($flagid = intval($val)) {
            well_website_flag_update($flagid, array('count+' => 1));
            $arr = array('flagid' => $flagid, 'fid' => $fid, 'tid' => $tid, 'type' => $type, 'create_date' => $time);
            // hook model_website_flagidarr_db_create_center.php
            $r = well_website_flag_thread_create($arr);
            if ($r === FALSE) return FALSE;
            // hook model_website_flagidarr_db_create_after.php
        }
    }

    // hook model_website_flagidarr_db_create_end.php

    return TRUE;
}

// 根据tid返回各栏目属性flagid
function well_website_flag_by_tid_return($tid)
{
    // hook model_website_flag_by_tid_return_start.php

    $forumarr = array();
    $catearr = array();
    $indexarr = array();
    $flagarr = array();

    $n = well_website_flag_thread_count($tid);
    if ($n) {
        $arrlist = well_website_flag_thread_find($tid, 1, $n);

        // hook model_website_flag_by_tid_return_before.php

        foreach ($arrlist as &$val) {

            // hook model_website_flag_by_tid_return_center.php

            $val['type'] == 1 AND $indexarr[] = $val['flagid'];
            $val['type'] == 2 AND $catearr[] = $val['flagid'];
            $val['type'] == 3 AND $forumarr[] = $val['flagid'];
            $flagarr[$val['flagid']] = $val['id'];
            // hook model_website_flag_by_tid_return_after.php
        }
    }

    // hook model_website_flag_by_tid_return_end.php

    return array($indexarr, $catearr, $forumarr, $flagarr);
}

function well_website_flag_thread_delete_by_ids($arr, $ids)
{
    if (empty($arr) || empty($ids)) return FALSE;

    // hook model_website_flag_thread_delete_by_ids_start.php

    $idarr = array();
    foreach ($arr as &$val) {
        // hook model_website_flag_thread_delete_by_ids_before.php
        $r = well_website_flag_update($val, array('count-' => 1));
        if ($r === FALSE) return FALSE;
        $idarr[] = $ids[$val];
        // hook model_website_flag_thread_delete_by_ids_after.php
    }

    $r = well_website_flag_thread_delete($idarr);
    if ($r === FALSE) return FALSE;

    // hook model_website_flag_thread_delete_by_ids_end.php

    return $r;
}

/*
根据需要展示的flag返回tids

属性为键的tid集合
Array
(
    [12] => Array
        (
            [0] => 17
            [1] => 29
            [2] => 31
            [3] => 35
            [4] => 32
        )

)

tid集合
Array
(
    [0] => 17
    [1] => 29
    [2] => 31
    [3] => 35
    [4] => 32
)
 */
function well_website_flag_return_thread_tids($flaglist)
{
    if (!$flaglist) return array();

    // hook model_website_flag_return_thread_tids_start.php

    $flagtidarr = $tids = array();

    foreach ($flaglist as &$val) {

        // hook model_website_flag_return_thread_tids_before.php

        // 遍历tid
        $tidlist = well_website_flag_thread_find_by_flagid_cache($val['flagid'], 1, $val['number']);
        if (!$tidlist) continue;

        // hook model_website_flag_return_thread_tids_center.php

        foreach ($tidlist as $v) {
            // 对应属性的tids
            //$flagtidarr[$val['flagid']][] = $v['tid'];
            $flagtidarr[$val['flagid']]['name'] = $val['name'];
            $flagtidarr[$val['flagid']]['url'] = $val['url'];
            $flagtidarr[$val['flagid']]['tids'][] = $v['tid'];

            // tid集合
            $tids[] = $v['tid'];
        }

        // hook model_website_flag_return_thread_tids_after.php

        unset($tidlist);
    }

    $arr = array('arr' => $flagtidarr, 'tids' => $tids);

    // hook model_website_flag_return_thread_tids_end.php

    return $arr;
}

// hook model_website_flag_end.php

?>
