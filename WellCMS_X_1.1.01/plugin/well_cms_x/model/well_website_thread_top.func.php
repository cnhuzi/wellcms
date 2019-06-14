<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// hook model_website_thread_top_start.php

// ------------> 最原生的 CURD，无关联其他数据

function well_thread_top_create($arr, $d = NULL)
{
    // hook model_website_thread_top_create_start.php
    $r = db_replace('website_thread_top', $arr, $d);
    // hook model_website_thread_top_create_end.php
    return $r;
}

function well_thread_top__update($tid, $arr, $d = NULL)
{
    // hook model_website_thread_top__update_start.php
    $r = db_update('website_thread_top', array('tid' => $tid), $arr, $d);
    // hook model_website_thread_top__update_end.php
    return $r;
}

function well_thread_top__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_website_thread_top__find_start.php
    $threadlist = db_find('website_thread_top', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_thread_top__find_end.php
    return $threadlist;
}

function well_thread_top_delete($tid, $d = NULL)
{
    // hook model_website_thread_top_delete_start.php
    $thread = well_thread__read($tid);
    if (!$thread) return FALSE;
    if ($thread['top']) {
        well_thread_update($tid, array('top' => 0));
        well_thread_top_cache_delete($thread['fid']);
    }
    $r = db_delete('website_thread_top', array('tid' => $tid), $d);
    // hook model_website_thread_top_delete_end.php
    return $r;
}

/*function well_thread_top__count($tid, $d = NULL)
{
    // hook model_website_thread__count_start.php
    $n = db_count('website_thread_top', array('tid' => $tid), $d);
    // hook model_website_thread__count_end.php
    return $n;
}*/

function well_thread_top__count($cond = array(), $d = NULL)
{
    // hook model_website_thread__count_start.php
    $n = db_count('website_thread_top', $cond, $d);
    // hook model_website_thread__count_end.php
    return $n;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理
// 更改置顶 更新主题和栏目
function well_thread_top_change($tid, $top = 0)
{
    // hook model_website_thread_top_change_start.php
    $thread = well_thread__read($tid);
    if (!$thread) return FALSE;
    if ($top != $thread['top']) {
        well_thread_update($tid, array('top' => $top));

        well_thread_top_cache_delete($thread['fid']);

        $arr = array('fid' => $thread['fid'], 'tid' => $thread['tid'], 'top' => $top);
        $r = well_thread_top_create($arr);
        return $r;
    }
    // hook model_website_thread_top_change_end.php
    return FALSE;
}

// 获取首页 频道 栏目置顶 缓存
function well_thread_top_find_cache($fid = 0, $top = 3)
{
    // hook model_website_thread_top_find_cache_start.php
    $key = 'website_thread_top_list_' . (is_array($fid) ? md5(json_encode($fid)) : $fid);
    $threadlist = cache_get($key);
    if ($threadlist === NULL) {
        $threadlist = well_thread_top_find($fid, $top);
        $threadlist AND cache_set($key, $threadlist, 7200);
    } else {
        // 重新格式化时间
        foreach ($threadlist as &$thread) {
            well_thread_format_last_date($thread);
        }
    }
    // hook model_website_thread_top_find_cache_end.php
    return $threadlist;
}

/*
 * well_thread_top_find($fid = 0, $top = 3);
 * well_thread_top_find($fid = array(1,2,3), array(1,2,3));
 * */
function well_thread_top_find($fid = 0, $top = 3)
{
    // hook model_website_thread_top_find_start.php
    if ($fid == 0) {
        $threadlist = well_website_all_top($top);
    } else {
        $threadlist = well_thread_top__find(array('fid' => $fid, 'top' => $top), array('tid' => -1), 1, 100);
    }

    if (!$threadlist) return NULL;

    $tids = arrlist_values($threadlist, 'tid');
    $threadlist = well_thread_find_by_tids($tids);
    // hook model_website_thread_top_find_end.php
    return $threadlist;
}

// 全站置顶
function well_website_all_top($top)
{
    // hook model_website_all_top_start.php
    $threadlist = well_thread_top__find(array('top' => $top), array('tid' => -1), 1, 100);
    // hook model_website_all_top_end.php
    return $threadlist;
}

function well_thread_top_cache_delete($fid)
{
    // hook model_website_thread_top_cache_delete_start.php
    static $deleted = FALSE;
    if ($deleted) return;

    // hook model_website_thread_top_cache_delete_before.php

    $forumlist = GLOBALS('forumlist');;
    $forum = array_value($forumlist, $fid);
    $well_fup = array_value($forum, 'well_fup');

    // hook model_website_thread_top_cache_delete_after.php

    $well_fup AND cache_delete('website_thread_top_list_' . md5(json_encode($fid, $well_fup)));
    $fid AND cache_delete('website_thread_top_list_' . $fid);
    // 清理全站置顶
    cache_delete('website_thread_top_list_0');

    $deleted = TRUE;

    // hook model_website_thread_top_cache_delete_end.php
}

function well_thread_top_update_by_tid($tid, $newfid)
{
    // hook model_website_thread_top_update_by_tid_start.php
    $r = well_thread_top__update(array('tid' => $tid), array('fid' => $newfid));
    // hook model_website_thread_top_update_by_tid_end.php
    return $r;
}

function well_thread_top_count($tid)
{
    // hook model_website_thread_count_start.php
    $n = well_thread_top__count(array('tid' => $tid));
    // hook model_website_thread_count_end.php
    return $n;
}

function well_thread_top_count_by_top($top)
{
    // hook model_website_thread_count_by_top_start.php
    $n = well_thread_top__count(array('top' => $top));
    // hook model_website_thread_count_by_top_end.php
    return $n;
}

function well_thread_top_count_by_fid($fid)
{
    // hook model_website_thread_count_by_fid_start.php
    $n = well_thread_top__count(array('fid' => $fid));
    // hook model_website_thread_count_by_fid_end.php
    return $n;
}

// hook model_website_thread_top_end.php

?>