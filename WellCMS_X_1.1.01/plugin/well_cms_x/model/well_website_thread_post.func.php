<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 主题回复(评论)
 */

// hook model_website_post__start.php

// ------------> 原生CURD，无关联其他数据。
function well_post__create($arr = array(), $d = NULL)
{
    // hook model_website_post__create_start.php
    $r = db_insert('website_post', $arr, $d);
    // hook model_website_post__create_end.php
    return $r;
}

function well_post__update($pid, $update = array(), $d = NULL)
{
    // hook model_website_post__update_start.php
    $r = db_update('website_post', array('pid' => $pid), $update, $d);
    // hook model_website_post__update_end.php
    return $r;
}

function well_post__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_post__read_start.php
    $r = db_find_one('website_post', $cond, $orderby, $col, $d);
    // hook model_website_post__read_end.php
    return $r;
}

function well_post__find($cond = array(), $orderby = array('pid' => -1), $page = 1, $pagesize = 20, $key = 'pid', $col = array(), $d = NULL)
{
    // hook model_thread__find_start.php
    $threadlist = db_find('website_post', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_thread__find_end.php
    return $threadlist;
}

function well_post__delete($cond = array(), $d = NULL)
{
    // hook model_website_post__delete_start.php
    $r = db_delete('website_post', $cond, $d);
    // hook model_website_post__delete_end.php
    return $r;
}

function well_post_count($cond = array(), $d = NULL)
{
    // hook model_website_post_count_start.php
    $n = db_count('website_post', $cond, $d);
    // hook model_website_post_count_end.php
    return $n;
}
//--------------------------强相关--------------------------
// 评论回复不支持html标签，不支持附件和图片
// array('tid' => $tid, 'fid' => $fid, 'doctype' => $doctype, 'message' => $message);
function well_post_create($post)
{
    if (empty($post)) return FALSE;
    $time = GLOBALS('time');
    $uid = GLOBALS('uid');
    $gid = GLOBALS('gid');
    $verify = 0;

    // hook model_website_post_create_start.php

    well_data_message_format($post);

    $pid = well_post__create($post);
    if ($pid === FALSE) return FALSE;

    // hook model_website_post_create_center.php

    $forum_update = array('todayposts+' => 1);
    // hook model_website_post_create_forum_update_before.php
    forum__update($post['fid'], $forum_update);
    unset($forum_update);

    // hook model_website_post_create_after.php

    // 我的回复 审核成功写入website_post_pid
    if (!$verify || $gid == 1) {
        // 插入回复小表
        $arr = array('pid' => $pid, 'fid' => $post['fid'], 'tid' => $post['tid'], 'uid' => $uid);
        // hook model_website_post_create_post_pid.php
        well_post_pid_create($arr);

        // 更新最后回复lastuid
        $arr = array('posts+' => 1, 'last_date' => $time, 'lastuid' => $uid);
        // hook model_website_post_create_thread_update.php
        well_thread_update($post['tid'], $arr);
    }

    // hook model_website_post_create_end.php

    runtime_set('website_posts+', 1);
    runtime_set('website_todayposts+', 1);

    // hook model_website_post_create_end.php

    return $pid;
}

// 主键更新 $update = array('gid' => $gid, 'userip' => $longip, 'message' => $message, 'doctype' => $doctype);
function well_post_update($pid, $update)
{
    $gid = GLOBALS('gid');
    $verify = 0;
    if (!$pid || empty($update)) return FALSE;

    // hook model_website_post_update_start.php

    well_data_message_format($update);

    // hook model_website_post_update_before.php

    // 我的回复 审核成功写入website_post_pid
    if (!$verify || $gid == 1) {
        $update['status'] = 0;
    } else {
        // hook model_website_post_update_verify_start.php
        $update['status'] = 1;
        $read = well_post__read(array('pid' => $pid));
        if ($read) {
            well_post_pid__delete(array('pid' => $pid));
            // hook model_website_post_update_verify_before.php
            $r = well_post__read(array('tid' => $read['tid']), array('pid' => -1));
            // 更新最后回复
            $arr = array('posts-' => 1, 'lastuid' => $r['uid']);
            // hook model_website_post_update_verify_center.php
            $r AND well_thread_update($read['tid'], $arr);
            // hook model_website_post_update_verify_after.php
            // 待审核
        }
        // hook model_website_post_update_verify_end.php
    }

    // hook model_website_post_update_after.php

    $r = well_post__update($pid, $update);

    // hook model_website_post_update_end.php

    return $r;
}

// 主题下 所有回复数据详情
function well_post_find_by_tid($tid, $page = 1, $pagesize = 20)
{
    if (!$tid) return NULL;

    // hook model_website_post_find_by_tid_start.php

    $arr = well_post_pid_find($tid, $page, $pagesize, FALSE);

    if (!$arr) return NULL;

    // hook model_website_post_find_by_tid_before.php

    $pidarr = arrlist_values($arr, 'pid');

    $postlist = well_post_find($pidarr, $pagesize, FALSE);
    if ($postlist) {
        $i = 0;
        $floor = ($page - 1) * $pagesize + 2;
        foreach ($postlist as &$post) {
            ++$i;
            $v['i'] = $i;
            $post['floor'] = $floor++;
        }
    }

    // hook model_website_post_find_by_tid_end.php

    return $postlist;
}

// 栏目下所有回复数据详情
function well_post_find_by_fid($fid, $page = 1, $pagesize = 20)
{
    if (!$fid) return NULL;

    // hook model_website_post_find_by_fid_start.php

    // 遍历栏目下所有pid
    $arr = well_post_pid_find_by_fid($fid, $page, $pagesize);

    if (!$arr) return NULL;

    // hook model_website_post_find_by_fid_before.php

    $pidarr = arrlist_values($arr, 'pid');

    // 遍历主题和回复
    $postlist = well_post_find($pidarr, $pagesize);
    if ($postlist) {
        $i = 0;
        $floor = ($page - 1) * $pagesize + 2;
        foreach ($postlist as &$post) {
            ++$i;
            $v['i'] = $i;
            $post['floor'] = $floor++;
        }
    }

    // hook model_website_post_find_by_fid_end.php

    return $postlist;
}

// 遍历所有回复
function well_post_find($pidarr, $pagesize = 20, $desc = TRUE)
{
    if (!$pidarr) return NULL;

    // hook model_website_post_find_start.php

    $orderby = $desc == TRUE ? -1 : 1;
    $postlist = well_post__find(array('pid' => $pidarr), array('pid' => $orderby), 1, $pagesize);

    if ($postlist) {
        $i = 0;
        foreach ($postlist as &$post) {
            ++$i;
            $v['i'] = $i;
            well_post_format($post);
        }
    }

    // hook model_website_post_find_end.php

    return $postlist;
}

// 遍历所有回复
function well_post_find_all($page = 1, $pagesize = 20)
{
    // hook model_website_post_find_all_start.php

    $arr = well_post_pid_find_all($page, $pagesize);

    if (!$arr) return NULL;

    $pidarr = arrlist_values($arr, 'pid');

    // hook model_website_post_find_all_before.php

    // 遍历主题和回复
    $postlist = well_post_find($pidarr, $pagesize);

    if (!$postlist) return NULL;

    // hook model_website_post_find_all_after.php

    $i = 0;
    $floor = ($page - 1) * $pagesize + 2;
    foreach ($postlist as &$post) {
        ++$i;
        $v['i'] = $i;
        $post['floor'] = $floor++;
        // hook model_website_post_find_all_foreach.php
    }

    // hook model_website_post_find_all_end.php

    return $postlist;
}

function well_post_read($pid)
{
    if (!$pid) return NULL;
    // hook model_website_post_read_start.php
    $r = well_post__read(array('pid' => $pid));
    $r AND well_post_format($r);
    // hook model_website_post_read_end.php
    return $r;
}

function well_post_read_by_uid($uid)
{
    if (!$uid) return NULL;
    // hook model_website_post_read_by_uid_start.php
    $r = well_post__read(array('uid' => $uid));
    // hook model_website_post_read_by_uid_end.php
    return $r;
}

// 直接删除回复 彻底删除
function well_post_delete($pid)
{
    if (!$pid) return FALSE;
    // hook model_website_post_delete_start.php
    $r = well_post__delete(array('pid' => $pid));
    if ($r === FALSE) return FALSE;
    // 删除小表
    $r = well_post_pid_delete($pid);
    if ($r === FALSE) return FALSE;
    // hook model_website_post_delete_end.php
    return $r;
}

// 通过删除主题 彻底删除回复 此处也需要删除待验证和回收站回复数据
function well_post_delete_by_tid($tid)
{
    if (!$tid) return FALSE;
    $thread = well_thread_read_cache($tid);
    if (!$thread) return FALSE;

    // hook model_post_delete_by_tid_start.php

    $posts = $thread['posts'];
    if (!$posts) return FALSE;

    $size = 500;
    if ($posts > $size) {
        $n = ceil($posts / $size);
    } else {
        $n = $posts;
    }

    for ($i = 0; $i <= $n; $i++) {
        // 查询回复小表 该主题回复 pid
        $arr = well_post_pid__find(array('tid' => $tid), array('pid' => -1), 1, $size, 'pid', array('pid', 'uid'));

        // hook model_post_delete_by_tid_before.php

        if (!$arr) return FALSE;

        $pidarr = arrlist_values($arr, 'pid');
        // 删除回复小表
        well_post_pid_delete_by_pidarr($pidarr);
        // 删除所有回复
        well_post_delete($pidarr);

        // hook model_post_delete_by_tid_after.php
    }

    // hook model_post_delete_by_tid_end.php

    return $posts;
}

function well_post_format(&$post)
{
    // hook model_website_post_format_start.php

    if (empty($post)) return;
    $uid = GLOBALS('uid');
    $gid = GLOBALS('gid');

    // hook model_website_post_format_before.php

    $thread = well_thread_read_cache($post['tid']);
    $post['fid'] = $thread['fid'];
    $post['closed'] = $thread['closed'];
    $post['subject'] = $thread['subject'];

    $post['create_date_fmt'] = humandate($post['create_date']);
    //$post['message'] = stripslashes(htmlspecialchars_decode($post['message']));

    // hook model_website_post_format_center.php

    $user = user_read_cache($post['uid']);

    $post['username'] = array_value($user, 'username');
    $post['user_avatar_url'] = array_value($user, 'avatar_url');
    $post['user'] = $user ? $user : user_guest();
    !isset($post['floor']) AND $post['floor'] = 0;

    // hook model_website_post_format_after.php

    // 权限判断
    $post['allowupdate'] = ($uid == $post['uid']) || forum_access_mod($thread['fid'], $gid, 'allowupdate');
    $post['allowdelete'] = ($uid == $post['uid']) || forum_access_mod($thread['fid'], $gid, 'allowdelete');

    $post['user_url'] = url('user-' . $post['uid'] . ($post['uid'] ? '' : '-' . $post['pid']));

    $post['classname'] = 'post';

    // hook model_website_post_format_end.php
}

function well_post_highlight_keyword($str, $k)
{
    // hook model_website_post_highlight_keyword_start.php
    $r = str_ireplace($k, '<span class=red>' . $k . '</span>', $str);
    // hook model_website_post_highlight_keyword_end.php
    return $r;
}

// 对内容进行引用
function well_post_quote($quotepid)
{
    // hook model_website_post_quote_start.php

    $quotepost = well_post_read($quotepid);
    if (empty($quotepost)) return '';
    $uid = $quotepost['uid'];
    $s = $quotepost['message'];

    // hook model_website_post_quote_before.php

    $s = well_post_brief($s, 100);
    $userhref = url('user-' . $uid);
    $user = user_read_cache($uid);

    // hook model_website_post_quote_after.php

    $r = '<blockquote class="blockquote">
		<a href="' . $userhref . '" class="text-small text-muted user">
			<img class="avatar-1" src="' . $user['avatar_url'] . '">
			' . $user['username'] . '
		</a>
		' . $s . '
		</blockquote>';

    // hook model_website_post_quote_end.php

    return $r;
}

// 获取内容的简介 0: html, 1: txt; 2: markdown; 3: ubb
function well_post_brief($s, $len = 100)
{
    // hook model_website_post_brief_start.php
    $s = strip_tags($s);
    $s = htmlspecialchars($s);
    $more = xn_strlen($s) > $len ? ' ... ' : '';
    $s = xn_substr($s, 0, $len) . $more;
    // hook model_website_post_brief_end.php
    return $s;
}

// hook model_website_post_end.php

?>