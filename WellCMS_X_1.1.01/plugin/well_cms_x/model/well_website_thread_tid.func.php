<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 *
 * $arrlist = well_thread_tid__find(array('fid' => 1, 'tid' => array('>' => 40000000)), $orderby = array('tid' => 1), $page = 1, $pagesize = 20, $key = 'tid', $col = array('tid'));
 */
// hook model_website_thread_tid_start.php

// ------------> 原生CURD，无关联其他数据。
function well_thread_tid__create($arr = array(), $d = NULL)
{
    // hook model_website_thread_tid__create_start.php
    $r = db_replace('website_thread_tid', $arr, $d);
    // hook model_website_thread_tid__create_end.php
    return $r;
}

function well_thread_tid__update($cond = array(), $update = array(), $d = NULL)
{
    // hook model_website_thread_tid__update_start.php
    $r = db_update('website_thread_tid', $cond, $update, $d);
    // hook model_website_thread_tid__update_end.php
    return $r;
}

function well_thread_tid__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_thread_tid__read_start.php
    $r = db_find_one('website_thread_tid', $cond, $orderby, $col, $d);
    // hook model_website_thread_tid__read_end.php
    return $r;
}

function well_thread_tid__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_website_thread_tid__find_start.php
    $arr = db_find('website_thread_tid', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_thread_tid__find_end.php
    return $arr;
}

function well_thread_tid__delete($cond = array(), $d = NULL)
{
    // hook model_website_thread_tid__delete_start.php
    $r = db_delete('website_thread_tid', $cond, $d);
    // hook model_website_thread_tid__delete_end.php
    return $r;
}

function well_thread_tid__count($cond = array(), $d = NULL)
{
    // hook model_website_thread_tid__count_start.php
    $n = db_count('website_thread_tid', $cond, $d);
    // hook model_website_thread_tid__count_end.php
    return $n;
}

//--------------------------强相关--------------------------
function well_thread_tid_create($arr)
{
    if (empty($arr)) return FALSE;
    // hook model_website_thread_tid_create_start.php
    $r = well_thread_tid__create($arr);
    // hook model_website_thread_tid_create_end.php
    return $r;
}

// 单次查询 tid 正常直接单次查询主表
function well_thread_tid_read($tid)
{
    if (!$tid) return NULL;
    // hook model_website_thread_tid_read_start.php
    $r = well_thread_tid__read(array('tid' => $tid));
    // hook model_website_thread_tid_read_end.php
    return $r;
}

// 主键更新 若移动栏目 则需要更新此表fid
function well_thread_tid_update($tid, $fid)
{
    if (!$tid || !$fid) return FALSE;
    // hook model_website_thread_tid_update_start.php
    $r = well_thread_tid__update(array('tid' => $tid), array('fid' => $fid));
    if ($r === FALSE) return FALSE;
    // hook model_website_thread_tid_update_end.php
    return $r;
}

// 主键更新 若移动栏目 则需要更新此表fid
function well_thread_tid_update_rank($tid, $rank)
{
    if (!$tid || !$rank) return FALSE;
    // hook model_website_thread_tid_update_rank_start.php
    $r = well_thread_tid__update(array('tid' => $tid), array('rank' => $rank));
    if ($r === FALSE) return FALSE;
    // hook model_website_thread_tid_update_rank_end.php
    return $r;
}

// 遍历所有主题tid
function well_thread_tid_find($page = 1, $pagesize = 20, $desc = TRUE)
{
    // hook model_website_thread_tid_find_by_uid_start.php
    $orderby = $desc == TRUE ? -1 : 1;
    $arr = well_thread_tid__find($cond = array(), array('tid' => $orderby), $page, $pagesize, 'tid', array('tid','verify_date'));
    // hook model_website_thread_tid_find_by_uid_end.php
    return $arr;
}

// 遍历用户所有主题tid
//$uid：用户ID
//$page： 页数
//$pagesize：每页记录条数
//$orderby：排序方式 TRUE降序 FALSE升序
//$key：返回的数组用那一列的值作为 key
//$col：查询哪些列
function well_thread_tid_find_by_uid($uid, $page = 1, $pagesize = 10000, $desc = TRUE, $key = 'tid', $col = array())
{
    if (!$uid) return NULL;
    // hook model_website_thread_tid_find_by_uid_start.php
    $orderby = $desc == TRUE ? -1 : 1;
    $arr = well_thread_tid__find($cond = array('uid' => $uid), array('tid' => $orderby), $page, $pagesize, $key, $col);
    // hook model_website_thread_tid_find_by_uid_end.php
    return $arr;
}

// 遍历栏目下tid
function well_thread_tid_find_by_fid($fid, $page = 1, $pagesize = 10000, $desc = TRUE)
{
    // hook model_website_thread_tid_find_by_fid_start.php
    $orderby = $desc == TRUE ? -1 : 1;
    $arr = well_thread_tid__find($cond = array('fid' => $fid), array('tid' => $orderby), $page, $pagesize, 'tid', array('tid','verify_date'));
    // hook model_website_thread_tid_find_by_fid_end.php
    return $arr;
}

function well_thread_tid_delete($tid)
{
    // hook model_website_thread_tid_delete_start.php
    $r = well_thread_tid__delete(array('tid' => $tid));
    if ($r === FALSE) return FALSE;
    // hook model_website_thread_tid_delete_end.php
    return $r;
}

function well_thread_tid_count()
{
    // hook model_website_thread_tid_count_start.php
    $n = well_thread_tid__count(array());
    // hook model_website_thread_tid_count_end.php
    return $n;
}

// 统计用户主题数
function well_thread_uid_count($uid)
{
    // hook model_website_thread_uid_count_start.php
    $n = well_thread_tid__count(array('uid' => $uid));
    // hook model_website_thread_uid_count_end.php
    return $n;
}

// 统计栏目主题数 大数量下最好不使用统计
function well_thread_fid_count($fid)
{
    // hook model_website_thread_fid_count_start.php
    $n = well_thread_tid__count(array('fid' => $fid));
    // hook model_website_thread_fid_count_end.php
    return $n;
}

// hook model_website_thread_tid_end.php

?>