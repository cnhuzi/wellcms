<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
// 主题
// hook model_website_thread_start.php

// ------------> 最原生的 CURD，无关联其他数据。

function well_thread__create($arr, $d = NULL)
{
    // hook model_website_thread__create_start.php
    $r = db_insert('website_thread', $arr, $d);
    // hook model_website_thread__create_end.php
    return $r;
}

function well_thread__update($tid, $update, $d = NULL)
{
    // hook model_website_thread__update_start.php
    $r = db_update('website_thread', array('tid' => $tid), $update, $d);
    // hook model_website_thread__update_end.php
    return $r;
}

function well_thread__read($tid, $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_thread__read_start.php
    $thread = db_find_one('website_thread', array('tid' => $tid), $orderby, $col, $d);
    // hook model_website_thread__read_end.php
    return $thread;
}

// 最大tid
function well_thread_read_max_tid($col = array('tid'), $d = NULL)
{
    // hook model_website_thread_read_max_tid_start.php
    $thread = db_find_one('website_thread', array(), array('tid' => -1), $col, $d);
    // hook model_website_thread_read_max_tid_end.php
    return $thread;
}

// 彻底删除
function well_thread__delete($tid, $d = NULL)
{
    // hook model_website_thread__delete_start.php
    $r = db_delete('website_thread', array('tid' => $tid), $d);
    // hook model_website_thread__delete_end.php
    return $r;
}

function well_thread__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_website_thread__find_start.php
    $threadlist = db_find('website_thread', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_thread__find_end.php
    return $threadlist;
}

function well_thread_count($cond = array(), $d = NULL)
{
    // hook model_website_thread_count_start.php
    $n = db_count('website_thread', $cond, $d);
    // hook model_website_thread_count_end.php
    return $n;
}

//--------------------------强相关--------------------------

function well_thread_create($arr)
{
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    $longip = GLOBALS('longip');
    $gid = GLOBALS('gid');
    $uid = GLOBALS('uid');
    $forumlist = GLOBALS('forumlist');
    $verify = 0;

    $fid = $arr['fid'];
    $message = array_value($arr, 'message');
    $mainpic = array_value($arr, 'mainpic', 0); // 获取内容主图
    $delete_pic = array_value($arr, 'delete_pic', 0); // 删除主图
    $save_image = array_value($arr, 'save_image', 0); // 图片本地化

    // hook model_website_thread_create_start.php

    // 创建主题
    $thread = array('fid' => $fid, 'subject' => $arr['subject'], 'type' => $arr['type'], 'link' => $arr['link'], 'brief' => $arr['brief'], 'uid' => $uid, 'create_date' => $time, 'closed' => $arr['closed'], 'keyword' => $arr['keyword'], 'description' => $arr['description'], 'last_date' => $time, 'userip' => $longip);

    // hook model_website_thread_create_thread_after.php

    $upload_mianpic = well_attach_assoc_type(0); // 主图
    $upload_picture = well_attach_assoc_type(1); // 内容附件

    ((!empty($upload_mianpic) AND !$delete_pic) OR ($mainpic AND !$delete_pic AND !empty($upload_picture))) AND $thread['icon'] = $time;

    // hook model_website_thread_create_before.php

    // 主题入库
    $tid = well_thread__create($thread);
    if ($tid === FALSE) return FALSE;
    unset($thread);

    // hook model_website_thread_create_after.php

    // 关联主图 type 0:内容图片或附件 1:内容主图 8:节点主图 9:节点tag主图 教练套课主图
    if (!$delete_pic) {
        if (!empty($upload_mianpic)) {
            $arr = array('tid' => $tid, 'uid' => $uid, 'type' => 0);
            // hook model_website_thread_create_mainpic_before.php
            well_attach_assoc_data($arr);
            unset($arr);
        } else {
            // 获取内容第一张图为主图
            $mainpic AND well_attach_create_mainpic($tid, $fid);
        }
    }

    // hook model_website_thread_create_save_image_before.php

    $save_image AND $message = well_save_remote_image(array('tid' => $tid, 'fid' => $fid, 'uid' => $uid, 'mainpic' => $mainpic, 'message' => $message));

    // hook model_website_thread_create_attach_before.php

    // 关联附件
    $arr = array('tid' => $tid, 'uid' => $uid, 'type' => 1, 'images' => 0, 'files' => 0, 'message' => $message);
    // hook model_website_thread_create_attach_before.php
    well_attach_assoc_data($arr);

    // hook model_website_thread_create_data_before.php

    // 主题数据入库
    $data = array('tid' => $tid, 'gid' => $gid, 'message' => $arr['message'], 'doctype' => array_value($arr, 'doctype', 0));

    // hook model_website_thread_create_data_after.php

    $tid = well_data_create($data);
    if ($tid === FALSE) return FALSE;
    unset($data);

    $forum_update = array('threads+' => 1, 'todaythreads+' => 1);
    // hook model_website_thread_create_forum_update_before.php
    forum__update($fid, $forum_update);
    unset($forum_update);

    // hook model_website_thread_create_verify_before.php

    // 我的主题 审核成功写入该表 website_thread_tid表
    if (!$verify || $gid == 1) {
        $uid AND well_thread_tid_create(array('tid' => $tid, 'fid' => $fid, 'uid' => $uid));
        // hook model_website_thread_create_verify_middle.php
    } else {
        // hook model_website_thread_create_waiting_for_verify.php
    }

    // hook model_website_thread_create_verify_after.php

    // 全站内容数
    runtime_set('website_threads+', 1);
    runtime_set('website_todaythreads+', 1);

    // 更新板块信息
    forum_list_cache_delete();

    // 删除首页所有缓存
    cache_delete('website_index_list');
    // 删除首页属性调用缓存
    cache_delete('website_flag_index');
    // 删除栏目属性调用缓存
    cache_delete('website_flag_forum_' . $fid);
    // 删除频道属性调用缓存
    $forumlist[$fid]['well_fup'] AND cache_delete('website_flag_forum_' . $forumlist[$fid]['well_fup']);

    // hook model_website_thread_create_end.php

    return $tid;
}

// 仅更新主题表数据和缓存 如更新 tag 等
function well_thread_update($tid, $update)
{
    // hook model_website_thread_update_start.php

    $conf = GLOBALS('conf');

    if (!$tid || empty($update)) return FALSE;

    // hook model_website_thread_update_before.php

    $r = well_thread__update($tid, $update);
    if ($r === FALSE) return FALSE;

    // hook model_website_thread_update_after.php

    if ($conf['cache']['type'] != 'mysql' && is_array($tid)) {
        foreach ($tid as $_tid) cache_delete('website_thread_' . $_tid);
    } else {
        $conf['cache']['type'] != 'mysql' AND cache_delete('website_thread_' . $tid);
    }

    // hook model_website_thread_update_end.php

    return $r;
}

// 更新全部数据
function well_thread_update_all($tid, $update)
{
    // hook model_website_thread_update_all_start.php

    if (!$tid || empty($update)) return FALSE;

    // hook model_website_thread_update_all_before.php

    $r = well_thread_update($tid, $update);
    if ($r === FALSE) return FALSE;

    // hook model_website_thread_update_all_after.php

    $n = well_post_pid_count_by_tid($tid);
    if ($n) {
        $arrlist = well_post_pid_find($tid, 1, $n, FALSE);

        $pids = arrlist_values($arrlist, 'pid');

        $r = well_post__update($pids, $update);
        if ($r === FALSE) return FALSE;
    }

    // hook model_website_thread_update_all_end.php

    return $r;
}

// 全部（不包含待审核、逻辑删除）按照: 发布时间 倒序，包含置顶帖
function well_thread_find_by_fid($fid, $page = 1, $pagesize = 20, $rank = FALSE)
{
    // hook model_website_thread_find_by_fid_start.php

    if (!$fid) return NULL;

    $forumlist = GLOBALS('forumlist');
    $forum = array_value($forumlist, $fid);

    // hook model_website_thread_find_by_fid_before.php

    $threadlist = ($rank === TRUE AND $forum['well_thread_rank']) ? well_thread_find_rank_desc($fid, $page, $pagesize) : well_thread_find_desc($fid, $page, $pagesize);

    // hook model_website_thread_find_by_fid_after.php

    // 查找置顶
    if ($page == 1) {
        $fids = $forum['well_fup'] ? array($fid, $forum['well_fup']) : $fid;
        //$toplist3 = well_thread_top_find($fid, 3);// 函数已修改支持数组查询
        $toplist = well_thread_top_find_cache($fids, array(1, 3));
        $threadlist = (array)$toplist + (array)$threadlist;
    }

    // hook model_website_thread_find_by_fid_end.php

    return $threadlist;
}

// 按照: 发布时间 倒序，不包含置顶帖
// 查询栏目fid下tid 主题数据详情
function well_thread_find_desc($fid, $page = 1, $pagesize = 20)
{
    // hook model_website_thread_find_desc_start.php

    $conf = GLOBALS('conf');
    $runtime = GLOBALS('runtime');
    $forumlist = GLOBALS('forumlist');

    $forum = array_value($forumlist, $fid);
    $threads = $forum ? $forum['threads'] : $runtime['website_threads'];

    // hook model_website_thread_find_desc_before.php

    $desc = TRUE;
    $limitpage = 50000; // 如果需要防止 CC 攻击，可以调整为 5000
    if ($page > 100) {
        $totalpage = ceil($threads / $pagesize);
        $halfpage = ceil($totalpage / 2);

        if ($halfpage > $limitpage && $page < ($totalpage - $limitpage)) {
            $page = $limitpage;
        }

        if ($page > $halfpage) {
            $page = max(1, $totalpage - $page + 1);
            $arr = well_thread_tid_find_by_fid($fid, $page, $pagesize, FALSE);
            $arr = array_reverse($arr, TRUE);
            $desc = FALSE;
        }
    }

    $desc AND $arr = well_thread_tid_find_by_fid($fid, $page, $pagesize, TRUE);

    // hook model_website_thread_find_desc_after.php

    if (!$arr) return NULL;

    $tidarr = arrlist_values($arr, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_website_thread_find_desc_end.php

    return $threadlist;
}

// 按照: rank 倒序，含置顶帖 查询栏目fid下tid 主题数据详情
function well_thread_find_rank_desc($fid, $page = 1, $pagesize = 20)
{
    if (!$fid) return NULL;

    // hook model_website_thread_find_rank_desc_start.php

    $conf = GLOBALS('conf');
    $forumlist = GLOBALS('forumlist');
    $forum = array_value($forumlist, $fid);
    $threads = $forum['threads'];

    // hook model_website_thread_find_rank_desc_before.php

    $desc = TRUE;
    $limitpage = 5000; // 如果需要防止 CC 攻击，可以调整为 5000
    if ($page > 100) {
        $totalpage = ceil($threads / $pagesize);
        $halfpage = ceil($totalpage / 2);

        if ($halfpage > $limitpage && $page < ($totalpage - $limitpage)) {
            $page = $limitpage;
        }

        if ($page > $halfpage) {
            $page = max(1, $totalpage - $page + 1);
            $arr = well_thread_tid__find(array('fid' => $fid), array('rank' => 1), $page, $pagesize);
            $arr = array_reverse($arr, TRUE);
            $desc = FALSE;
        }
    }

    $desc AND $arr = well_thread_tid__find(array('fid' => $fid), array('rank' => -1), $page, $pagesize);

    // hook model_website_thread_find_rank_desc_after.php

    if (!$arr) return NULL;

    $tidarr = arrlist_values($arr, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize, FALSE);
    $threadlist = well_array2_merge($arr, $threadlist, 'tid');

    // 查找置顶
    if ($page == 1) {
        $toplist = well_thread_top_find_cache($fid, array(1, 3));
        $threadlist = (array)$toplist + $threadlist;
    }

    // hook model_website_thread_find_rank_desc_end.php

    return $threadlist;
}

// 从多个栏目获取列表数据
function well_thread_find_by_fids($fids, $page = 1, $pagesize = 20, $threads = FALSE)
{
    // hook model_website_thread_find_by_fids_start.php

    if (!$fids) return NULL;

    // hook model_website_thread_find_by_fids_before.php

    //$arr = well_thread_tid__find(array('fid' => $fids), array('tid' => -1), $page, $pagesize);
    $arr = well_thread_tid_find_by_fid($fids, $page, $pagesize, TRUE);

    if (!$arr) return NULL;

    // hook model_website_thread_find_by_fids_after.php

    $tidarr = arrlist_values($arr, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_website_thread_find_by_fids_end.php

    return $threadlist;
}

// 查询用户uid下tid 主题数据详情
function well_thread_find_by_uid($uid, $page = 1, $pagesize = 20)
{
    if (!$uid) return NULL;

    // hook model_website_thread_find_by_uid_start.php

    $arr = well_thread_tid_find_by_uid($uid, $page, $pagesize);

    if (!$arr) return NULL;

    // hook model_website_thread_find_by_uid_before.php

    $tidarr = arrlist_values($arr, 'tid');

    // hook model_website_thread_find_by_uid_after.php

    $threadlist = well_thread_find($tidarr, $pagesize);

    // hook model_website_thread_find_by_uid_end.php

    return $threadlist;
}

// tidarr 查询主题数据
// 主题状态0:通过 1~9审核:1待审核 10~19:10退稿 11逻辑删除
function well_thread_find($tidarr, $pagesize = 20, $desc = TRUE)
{
    if (!$tidarr) return NULL;

    // hook model_website_thread_find_start.php
    $orderby = $desc == TRUE ? -1 : 1;
    $threadlist = well_thread__find(array('tid' => $tidarr), array('tid' => $orderby), 1, $pagesize);

    // hook model_website_thread_find_before.php

    if ($threadlist) {
        $i = 0;
        foreach ($threadlist as &$thread) {
            ++$i;
            $thread['i'] = $i;
            well_thread_format($thread);
            // hook model_website_thread_find_format_after.php
        }
    }

    // hook model_website_thread_find_end.php

    return $threadlist;
}

// tidarr 查询主题数据 不给mysql增加压力使用正序 倒叙可以使用array_reverse($threadlist, TRUE);
// 主题状态0:通过 1~9审核:1待审核 10~19:10退稿 11逻辑删除
function well_thread_find_asc($tidarr, $pagesize = 20)
{
    if (!$tidarr) return NULL;

    // hook model_website_thread_find_start.php

    $threadlist = well_thread__find(array('tid' => $tidarr), array('tid' => 1), 1, $pagesize);

    // hook model_website_thread_find_before.php

    if ($threadlist) {
        foreach ($threadlist as &$thread) {
            well_thread_format($thread);
            // hook model_website_thread_find_format_after.php
        }
    }

    // hook model_website_thread_find_end.php

    return $threadlist;
}

function well_thread_find_by_tids($tidarr)
{
    if (!$tidarr) return NULL;
    // hook model_website_thread_find_by_tids_start.php
    $threadlist = well_thread_find($tidarr, 1000);
    // hook model_website_thread_find_by_tids_end.php
    return $threadlist;
}

// views + 1 大站可以单独剥离出来
function well_thread_inc_views($tid, $n = 1)
{
    // hook model_website_thread_inc_views_start.php

    $conf = GLOBALS('conf');
    $db = GLOBALS('db');
    $tablepre = $db->tablepre;
    //if (!$conf['update_views_on']) return TRUE;
    $sqladd = !in_array($conf['cache']['type'], array('mysql', 'pdo_mysql')) ? '' : ' LOW_PRIORITY';
    $r = db_exec("UPDATE$sqladd `{$tablepre}website_thread` SET views=views+$n WHERE tid='$tid'");

    // hook model_website_thread_inc_views_end.php

    return $r;
}

function well_thread_read($tid)
{
    if (!$tid) return NULL;

    // hook model_website_thread_read_start.php

    $thread = well_thread__read($tid);
    $thread AND well_thread_format($thread);

    // hook model_website_thread_read_end.php

    return $thread;
}

// 只删除主题和缓存
function well_thread_delete($tid)
{
    $conf = GLOBALS('conf');

    // hook model_website_thread_delete_start.php

    $r = well_thread__delete($tid);
    if ($r === FALSE) return FALSE;

    if (is_array($tid) && $conf['cache']['type'] != 'mysql') {
        foreach ($tid as $_tid) {
            cache_delete('website_thread_' . $_tid);
        }
    } else {
        $conf['cache']['type'] != 'mysql' AND cache_delete('website_thread_' . $tid);

        runtime_set('website_threads-', 1);
    }

    // hook model_website_thread_delete_end.php

    return $r;
}

// 删除主题全部相关数据
function well_thread_delete_all($tid)
{
    if (!$tid) return FALSE;
    // hook model_thread_delete_all_start.php
    $conf = GLOBALS('conf');
    $thread = well_thread_read($tid);
    if (!$thread) return FALSE;

    // hook model_thread_delete_all_before.php

    // 删除主题
    $r = well_thread_delete($tid);
    if ($r === FALSE) return FALSE;

    // hook model_thread_delete_all_icon_before.php

    // 删除主图
    if ($thread['icon']) {
        $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');
        $day = date($attach_dir_save_rule, $thread['icon']);
        $file = $conf['upload_path'] . 'website_mainpic/' . $day . '/' . $thread['tid'] . '.jpeg';
        file_exists($file) AND @unlink($file);
    }

    // hook model_thread_delete_all_tag_before.php

    // 删除tag
    if ($thread['tag']) {
        $tagids = array_keys($thread['tag_text']);
        well_oldtag_delete($tagids, $tid);
    }

    // hook model_thread_delete_all_top_before.php

    // 删除置顶
    $thread['top'] AND well_thread_top_delete($tid);

    // 删除主题属性
    $r = well_website_flag_thread_delete_all_by_tid($tid);
    if ($r === FALSE) return FALSE;

    // hook model_thread_delete_all_data_before.php

    // 删除内容
    $r = well_data_delete($tid);
    if ($r === FALSE) return FALSE;

    // hook model_thread_delete_all_post_before.php

    // 删除所有回复
    $n = well_post_delete_by_tid($tid);

    // hook model_thread_delete_all_attach_before.php

    // 删除附件
    ($thread['images'] OR $thread['files']) AND well_attach_delete_by_tid($tid);

    // hook model_thread_delete_all_tid_before.php

    $r = well_thread_tid_delete($tid);
    if ($r === FALSE) return FALSE;

    // hook model_thread_delete_all_forum_update_before.php

    // 更新统计
    forum__update($thread['fid'], array('threads-' => 1));

    // hook model_thread_delete_all_forum_cache_before.php

    // 清除相关缓存
    forum_list_cache_delete();

    // hook model_thread_delete_all_runtime_set_before.php

    // 实时缓存 全站统计
    runtime_set('website_threads-', 1);

    // hook model_website_thread_delete_all_end.php

    return $r;
}

// 删除用户时，删除主题 回复 栏目统计 附件 全站统计
function well_thread_delete_all_by_uid($uid)
{
    $conf = GLOBALS('conf');

    // hook model_website_thread_delete_all_by_uid_start.php

    // 统计用户主题数
    $n = well_thread_uid_count($uid);

    $tidarr = $forum_tids = array();
    if ($n) {
        // 如果主题、附件和回复数量太大可能会超时
        $tidlist = well_thread_tid_find_by_uid($uid, 1, $n, FALSE, 'tid', array('fid', 'tid'));
        foreach ($tidlist as $val) {
            // 每个栏目下的主题数
            $forum_tids[$val['tid']] = $val['fid'];
            $tidarr[] = $val['tid'];
        }

        unset($tidlist);

        $threadlist = well_thread__find(array('tid' => $tidarr), array('tid' => 1), 1, $n, 'tid', array('icon', 'images', 'files'));

        $toparr = array();
        foreach ($threadlist as $thread) {
            // 删除主图
            if ($thread['icon']) {
                $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');
                $day = date($attach_dir_save_rule, $thread['icon']);
                $file = $conf['upload_path'] . 'website_mainpic/' . $day . '/' . $thread['tid'] . '.jpeg';
                file_exists($file) AND @unlink($file);
            }

            $thread['top'] AND $toparr[] = $thread['tid'];
        }

        // hook model_website_thread_delete_all_by_uid_before.php

        // 清理置顶
        !empty($toparr) AND db_delete('website_thread_top', array('tid' => $toparr));
    }

    // 删除所有回复
    $posts = well_post_pid_count_by_uid($uid);
    $pidarr = $forum_pids = array();
    if ($posts) {
        $postist = well_post_pid_find_by_uid($uid, 1, $posts, FALSE);

        foreach ($postist as $val) {
            // 每个栏目下的回复数
            $forum_pids[$val['pid']] = $val['fid'];
            $pidarr[] = $val['pid'];
        }
        unset($postist);

        well_post_delete($pidarr);
    }

    // hook model_website_thread_delete_all_by_uid_middle.php

    if (!empty($tidarr)) {
        $forum_tids = array_count_values($forum_tids);
        //$forum_pids = array_count_values($forum_pids);
        // 更新统计
        if ($forum_tids || $forum_pids) {
            foreach ($forum_tids as $k => $v) {
                forum__update($k, array('threads-' => $v));
            }
        }

        // 清除相关缓存
        forum_list_cache_delete();

        // 删除主题
        $r = well_thread_delete($tidarr);
        if ($r === FALSE) return FALSE;

        // 删除主题小表
        $r = well_thread_tid_delete($tidarr);
        if ($r === FALSE) return FALSE;
    }

    // hook model_website_thread_delete_all_by_uid_after.php

    $threads = count($tidarr);
    $posts = count($pidarr);
    // hook model_website_thread_delete_all_by_uid_runtime_before.php
    // 实时缓存 全站统计
    runtime_set('website_threads-', $threads);
    runtime_set('website_posts-', $posts);

    // hook model_website_thread_delete_all_by_uid_end.php

    return TRUE;
}

// 搜索标题
function well_thread_find_by_keyword($keyword)
{
    if (!$keyword) return NULL;
    $db = GLOBALS('db');
    $tablepre = $db->tablepre;
    // hook model_website_thread_find_by_keyword_start.php

    $threadlist = db_sql_find("SELECT * FROM `{$tablepre}website_thread` WHERE subject LIKE '%$keyword%' LIMIT 60;");

    // hook model_website_thread_find_by_keyword_before.php

    if ($threadlist) {
        $threadlist = arrlist_multisort($threadlist, 'tid', FALSE); // PHP 排序

        // hook model_website_thread_find_by_keyword_after.php
        foreach ($threadlist as &$thread) {
            well_thread_format($thread);
            // 关键词标色
            //$thread['subject'] = well_post_highlight_keyword($thread['subject'], $keyword);
        }
    }

    // hook model_website_thread_find_by_keyword_end.php

    return $threadlist;
}

// 主题状态 0:通过 1~9审核:1待审核 10~19:10退稿 11逻辑删除
function well_thread_format(&$thread)
{
    $forumlist = GLOBALS('forumlist');
    $conf = GLOBALS('conf');
    $gid = GLOBALS('gid');
    $uid = GLOBALS('uid');

    if (empty($thread)) return;

    // hook model_website_thread_format_start.php

    $thread['create_date_fmt'] = humandate($thread['create_date']);
    $thread['last_date_fmt'] = humandate($thread['last_date']);
    $thread['create_date_text'] = date('Y-m-d', $thread['create_date']);
    $thread['last_date_text'] = date('Y-m-d', $thread['last_date']);

    $user = user_read_cache($thread['uid']);
    $thread['username'] = $user['username'];
    $thread['user_avatar_url'] = $user['avatar_url'];
    $thread['user'] = user_safe_info($user);
    unset($user);

    $forum = isset($forumlist[$thread['fid']]) ? $forumlist[$thread['fid']] : array('name' => '');
    $thread['column_name'] = $forum['name'];

    if ($thread['last_date'] == $thread['create_date']) {
        $thread['last_date_fmt'] = '';
        $thread['lastuid'] = 0;
        $thread['lastusername'] = '';
    } else {
        $lastuser = $thread['lastuid'] ? user_read_cache($thread['lastuid']) : array();
        $thread['lastusername'] = $thread['lastuid'] ? $lastuser['username'] : lang('guest');
    }

    //$thread['url'] = $thread['link'] ? $thread['description'] : well_url_format($thread['fid'], $thread['tid']); // 外链接写进brief url('read-' . $thread['tid'])
    $thread['url'] = well_url_format($thread['fid'], $thread['tid']); // url('read-' . $thread['tid'])
    $thread['user_url'] = url('user-' . $thread['uid']);

    $thread['top_class'] = $thread['top'] ? 'top_' . $thread['top'] : '';

    if ($thread['icon']) {
        $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');
        $day = date($attach_dir_save_rule, $thread['icon']);
        $thread['icon_text'] = $conf['upload_url'] . 'website_mainpic/' . $day . '/' . $thread['tid'] . '.jpeg?' . $thread['icon'];
    } else {
        $thread['icon_text'] = '';
    }

    // 回复页面
    $thread['pages'] = ceil($thread['posts'] / $conf['postlist_pagesize']);

    $thread['tag_text'] = $thread['tag'] ? xn_json_decode($thread['tag']) : '';

    // SEO描述 此处格式会导致编辑时也调用到该数据
    //$thread['description'] = $thread['description'] ? $thread['description'] : ($thread['link'] ? '' : $thread['brief']);

    // 权限判断
    $thread['allowupdate'] = ($uid == $thread['uid']) || forum_access_mod($thread['fid'], $gid, 'allowupdate');
    $thread['allowdelete'] = ($uid == $thread['uid']) || forum_access_mod($thread['fid'], $gid, 'allowdelete');

    // hook model_website_thread_format_end.php
}

function well_thread_format_last_date(&$thread)
{
    // hook model_website_thread_format_last_date_start.php
    if ($thread['last_date'] != $thread['create_date']) {
        $thread['last_date_fmt'] = humandate($thread['last_date']);
    } else {
        $thread['create_date_fmt'] = humandate($thread['create_date']);
    }
    // hook model_website_thread_format_last_date_end.php
}

function well_thread_maxid()
{
    // hook model_website_thread_maxid_start.php
    $n = db_maxid('website_thread', 'tid');
    // hook model_website_thread_maxid_end.php
    return $n;
}

function well_thread_safe_info($thread)
{
    // hook model_website_thread_safe_info_start.php

    unset($thread['userip']);

    if (!empty($thread['user'])) {
        $thread['user'] = user_safe_info($thread['user']);
    }

    // hook model_website_thread_safe_info_end.php

    return $thread;
}

// 对 $threadlist 权限过滤
function well_thread_list_access_filter(&$threadlist, $gid)
{
    $forumlist = GLOBALS('forumlist');

    if (empty($threadlist)) return;

    // hook model_website_thread_list_access_filter_start.php

    foreach ($threadlist as $tid => $thread) {
        if (empty($forumlist[$thread['fid']]['accesson'])) continue;
        if ($thread['top'] > 0) continue;
        if (!forum_access_user($thread['fid'], $gid, 'allowread')) {
            unset($threadlist[$tid]);
        }
    }

    // hook model_website_thread_list_access_filter_end.php
}

// 查找 最后评论 lastpid
function well_thread_find_lastpid($tid)
{
    if (!$tid) return NULL;

    $arr = well_post_pid_read(array('tid' => $tid), array('pid' => -1), array('pid'));
    $lastpid = empty($arr) ? 0 : $arr['pid'];

    return $lastpid;
}

// 更新最后的 uid
function well_thread_update_last($tid)
{
    if (!$tid) return FALSE;

    $lastpid = well_thread_find_lastpid($tid);
    if (!$lastpid) return FALSE;

    $lastpost = well_post_read($lastpid);
    if (!$lastpost) return FALSE;

    $r = well_thread_update($tid, array('lastuid' => $lastpost['uid']));

    return $r;
}

//--------------------------cache--------------------------
// 从缓存中读取，避免重复从数据库取数据
// 已格式化
function well_thread_read_cache($tid)
{
    $conf = GLOBALS('conf');
    // hook model_website_thread_read_cache_start.php
    $key = 'website_thread_' . $tid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，要跨进程，可以再加一层缓存： memcached/xcache/apc/
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_thread_read($tid);
            $r AND cache_set($key, $r, 1800);
        }
    } else {
        $r = well_thread_read($tid);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_thread_read_cache_end.php
    return $cache[$key];
}

// 未格式化 只有主题数据
function well_thread_read_subject_cache($tid)
{
    $conf = GLOBALS('conf');
    // hook model_website_thread_read_cache_start.php
    $key = 'website_subject_' . $tid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，要跨进程，可以再加一层缓存： memcached/xcache/apc/
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_thread__read($tid);
            $r AND cache_set($key, $r, 600);
        }
    } else {
        $r = well_thread__read($tid);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_thread_read_cache_end.php
    return $cache[$key];
}

// 查询栏目下主题 fid 静态变量 跨进程需要再加一层缓存
function well_thread_find_by_fid_cache($fid, $page = 1, $pagesize = 20)
{
    $conf = GLOBALS('conf');
    // hook model_website_thread_find_by_fid_cache_start.php
    if (!$fid) return NULL;
    $key = 'by_fid_thread_' . $fid . '-' . $page;
    // hook model_website_thread_find_by_fid_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_thread_find_by_fid_cache_after.php

    $cache[$key] = well_thread_find_by_fid($fid, $page, $pagesize);

    // hook model_website_thread_find_by_fid_cache_end.php
    return $cache[$key];
}

// 查询用户主题 uid 静态变量 跨进程需要再加一层缓存
function well_thread_find_by_uid_cache($uid, $page = 1, $pagesize = 20)
{
    // hook model_website_thread_find_by_uid_cache_start.php
    if (!$uid) return NULL;
    $key = 'by_uid_thread_' . $uid . '-' . $page;
    // hook model_website_thread_find_by_uid_cache_before.php
    static $cache = array();
    if (isset($cache[$key])) return $cache[$key];
    // hook model_website_thread_find_by_uid_cache_after.php
    $cache[$key] = well_thread_find_by_uid($uid, $page, $pagesize);
    // hook model_website_thread_find_by_uid_cache_end.php
    return $cache[$key];
}

// hook model_website_thread_end.php

?>