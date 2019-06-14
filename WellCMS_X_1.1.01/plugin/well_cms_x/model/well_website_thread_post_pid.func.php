<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// hook model_website_post_pid_start.php

// ------------> 原生CURD，无关联其他数据。
function well_post_pid_create($arr = array(), $d = NULL)
{
    // hook model_website_post_pid__create_start.php
    $r = db_replace('website_post_pid', $arr, $d);
    // hook model_website_post_pid__create_end.php
    return $r;
}

function well_post_pid__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'pid', $col = array(), $d = NULL)
{
    // hook model_website_post_pid__find_start.php
    $arr = db_find('website_post_pid', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_post_pid__find_end.php
    return $arr;
}

function well_post_pid__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_post_pid_read__start.php
    $r = db_find_one('website_post_pid', $cond, $orderby, $col, $d);
    // hook model_website_post_pid_read__end.php
    return $r;
}

function well_post_pid__delete($cond = array(), $d = NULL)
{
    // hook model_website_post_pid__delete_start.php
    $r = db_delete('website_post_pid', $cond, $d);
    // hook model_website_post_pid__delete_end.php
    return $r;
}

function well_post_pid__count($cond = array(), $d = NULL)
{
    // hook model_website_post_pid__count_start.php
    $n = db_count('website_post_pid', $cond, $d);
    // hook model_website_post_pid__count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_post_pid_read($pid)
{
    // hook model_website_post_pid_read_start.php
    $r = well_post_pid__read(array('pid' => $pid));
    // hook model_website_post_pid_read_end.php
    return $r;
}

// 遍历主题下所有回复
function well_post_pid_find($tid, $page = 1, $pagesize = 20, $desc = TRUE)
{
    if (!$tid) return NULL;
    // hook model_website_post_pid_find_start.php
    $orderby = $desc == TRUE ? -1 : 1;
    $arr = well_post_pid__find(array('tid' => $tid), array('pid' => $orderby), $page, $pagesize);
    // hook model_website_post_pid_find_end.php
    return $arr;
}

// 遍历栏目下所有回复
function well_post_pid_find_by_fid($fid, $page = 1, $pagesize = 20, $desc = TRUE)
{
    if (!$fid) return NULL;
    $orderby = $desc == TRUE ? -1 : 1;
    // hook model_website_post_pid_find_by_fid_start.php
    $arr = well_post_pid__find(array('fid' => $fid), array('pid' => $orderby), $page, $pagesize);
    // hook model_website_post_pid_find_by_fid_end.php
    return $arr;
}

// 遍历栏目下所有回复
function well_post_pid_find_by_uid($uid, $page = 1, $pagesize = 20, $desc = TRUE)
{
    if (!$uid) return NULL;
    $orderby = $desc == TRUE ? -1 : 1;
    // hook model_website_post_pid_find_by_uid_start.php
    $arr = well_post_pid__find(array('uid' => $uid), array('pid' => $orderby), $page, $pagesize);
    // hook model_website_post_pid_find_by_uid_end.php
    return $arr;
}

// 遍历栏目下所有回复
function well_post_pid_find_all($page = 1, $pagesize = 20, $desc = TRUE)
{
    $orderby = $desc == TRUE ? -1 : 1;
    // hook model_website_post_pid_find_by_fid_start.php
    $arr = well_post_pid__find(array(), array('pid' => $orderby), $page, $pagesize);
    // hook model_website_post_pid_find_by_fid_end.php
    return $arr;
}

// 彻底删除 pid
function well_post_pid_delete($pid)
{
    if (!$pid) return FALSE;
    // hook model_website_post_pid_delete_start.php
    $r = well_post_pid__delete(array('pid' => $pid));
    if ($r === FALSE) return FALSE;
    // hook model_website_post_pid_delete_end.php
    return $r;
}

function well_post_pid_count()
{
    // hook model_website_post_pid_count_start.php
    $n = well_post_pid__count();
    // hook model_website_post_pid_count_end.php
    return $n;
}

// 统计主题回复数 可直接调用website_thread表该主题回复数
function well_post_pid_count_by_tid($tid)
{
    // hook model_website_post_pid_count_by_tid_start.php
    $n = well_post_pid__count(array('tid' => $tid));
    // hook model_website_post_pid_count_by_tid_end.php
    return $n;
}

// 统计用户回复数 可直接调用user表该主题回复数posts
function well_post_pid_count_by_uid($uid)
{
    // hook model_website_post_pid_count_by_uid_start.php
    $n = well_post_pid__count(array('uid' => $uid));
    // hook model_website_post_pid_count_by_uid_end.php
    return $n;
}

// 统计栏目下回复数
function well_post_pid_count_by_fid($fid)
{
    // hook model_website_post_pid_count_by_fid_start.php
    $n = well_post_pid__count(array('fid' => $fid));
    // hook model_website_post_pid_count_by_fid_end.php
    return $n;
}

//--------------------------cache--------------------------

// hook model_website_post_pid_end.php
?>