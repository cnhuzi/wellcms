<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');
// 主题回复

// hook website_reply_start.php

$action = param(1);

if ($action == 'create') {
    // 创建回复
    // hook website_reply_create_start.php

    $tid = param(2, 0);

    $thread = well_thread_read($tid);
    !$thread AND message(-1, lang('thread_not_exists'));

    // hook website_reply_create_before.php

    $fid = $thread['fid'];

    //$forum = forum_read($fid);
    $forum = isset($forumlist[$fid]) ? $forumlist[$fid] : NULL;
    !$forum AND message(-1, lang('forum_not_exists'));

    $forum['well_type'] == 0 AND message(-1, lang('user_group_insufficient_privilege'));

    // hook website_reply_create_center.php

    $r = forum_access_user($fid, $gid, 'allowpost');
    !$r AND message(-1, lang('user_group_insufficient_privilege'));

    // hook website_reply_create_after.php

    // 已关闭回复
    (($thread['closed'] || !$forum['well_comment']) && ($gid == 0 || $gid > 5)) AND message(-1, lang('thread_has_already_closed'));

    if ($method == 'GET') {
        // hook website_reply_create_get_start.php

        // hook website_reply_create_get_end.php
    } elseif ($method == 'POST') {
        // hook website_reply_create_post_start.php

        $message = param('message', '', FALSE);
        empty($message) AND message('message', lang('please_input_message'));

        $doctype = param('doctype', 0);
        xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));

        // 回复排序需要清空置顶缓存重新排序
        $thread['top'] > 0 AND well_thread_top_cache_delete($fid);

        $quotepid = param('quotepid', 0);
        $quotepost = well_post_pid_read($quotepid);
        (!$quotepost || $quotepost['tid'] != $tid) AND $quotepid = 0;

        $post = array(
            'tid' => $tid,
            'uid' => $uid,
            'fid' => $fid,
            'create_date' => $time,
            'userip' => $longip,
            'doctype' => $doctype,
            'quotepid' => $quotepid,
            'message' => $message,
        );
        $pid = well_post_create($post);
        $pid === FALSE AND message(-1, lang('create_post_failed'));

        // thread_top_create($fid, $tid);

        $post = well_post_read($pid);
        $post['floor'] = $thread['posts'] + 2;
        $postlist = array($post);

        $allowpost = forum_access_user($fid, $gid, 'allowpost');
        $allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
        $allowdelete = forum_access_mod($fid, $gid, 'allowdelete');

        // hook website_reply_create_post_end.php

        // 直接返回帖子的 html
        // return the html string to browser.
        $return_html = param('return_html', 0);
        if ($return_html) {
            $filelist = array();
            ob_start();
            include _include(APP_PATH . 'plugin/well_cms_x/view/htm/reply_list.inc.htm');
            $s = ob_get_clean();

            message(0, $s);
        } else {
            message(0, lang('create_post_sucessfully'));
        }
    }
} elseif ($action == 'update') {
    // 编辑回复
    // hook website_reply_update_start.php

    $pid = param(2);
    $post = well_post_read($pid);
    !$post AND message(-1, lang('post_not_exists'));

    $tid = $post['tid'];
    $thread = well_thread_read($tid);
    !$thread AND message(-1, lang('thread_not_exists'));

    $fid = $thread['fid'];
    //$forum = forum_read($fid);
    $forum = isset($forumlist[$fid]) ? $forumlist[$fid] : NULL;
    !$forum AND message(-1, lang('forum_not_exists'));

    $forum['well_type'] == 0 AND message(-1, lang('user_group_insufficient_privilege'));

    !forum_access_user($fid, $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));

    $allowupdate = forum_access_mod($fid, $gid, 'allowupdate');
    !$allowupdate AND !$post['allowupdate'] AND message(-1, lang('have_no_privilege_to_update'));

    !$allowupdate AND ($thread['closed'] OR !$forum['well_comment']) AND message(-1, lang('thread_has_already_closed'));

    // hook website_reply_update_before.php

    if ($method == 'GET') {

        // hook website_reply_update_get_start.php

        $forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
        $forumarr = xn_json_encode(arrlist_key_values($forumlist_allowthread, 'fid', 'name'));

        // 如果为数据库减肥，则 message 可能会被设置为空。
        // if lost weight for the database, set the message field empty.
        $post['message'] = htmlspecialchars($post['message']);

        ($uid != $post['uid']) AND $post['message'] = xn_html_safe($post['message']);

        // hook website_reply_update_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/post.htm');

    } elseif ($method == 'POST') {

        // hook website_reply_update_post_start.php

        $message = param('message', '', FALSE);
        $doctype = param('doctype', 0);

        // hook website_reply_update_post_before.php

        empty($message) AND message('message', lang('please_input_message'));
        mb_strlen($message, 'UTF-8') > 2048000 AND message('message', lang('message_too_long'));

        $update = array('gid' => $gid, 'userip' => $longip, 'doctype' => $doctype, 'message' => $message);
        // hook website_reply_update_post_after.php
        $r = well_post_update($pid, $update);
        $r === FALSE AND message(-1, lang('update_post_failed'));

        // hook website_reply_update_post_end.php

        message(0, lang('update_successfully'));
    }
} elseif ($action == 'delete') {
    // 删除回复 type = 1支持批量删除，直接传pid一维数组pid = array(1,2,3)

    $type = param('type', 0);
    $pid = $type ? param('pid', array()) : param(2, 0);

    // hook website_reply_delete_start.php

    if ($method != 'POST') message(-1, lang('method_error'));

    if ($type) {

        // hook website_reply_delete_pids_start.php

        $pagesize = 25;

        $arrlist = well_post_find($pid, $pagesize, FALSE);

        // hook website_reply_delete_pids_before.php

        $tidarr = $pidarr = array();
        foreach ($arrlist as $key => &$val) {

            // hook website_reply_delete_pids_access_after.php

            if (!isset($forumlist[$val['fid']])) continue;

            $forum = $forumlist[$val['fid']];

            if ($forum['well_type'] == 0) continue;

            // hook website_reply_delete_pids_access_before.php

            $forum_access = forum_access_user($val['fid'], $gid, 'allowpost');
            $allowdelete = forum_access_mod($val['fid'], $gid, 'allowdelete');

            // hook website_reply_delete_pids_access_center.php

            if ($forum_access && $allowdelete && $val['allowdelete'] && !$val['closed'] && $forum['well_comment']) {
                $pidarr[] = $val['pid'];
                $tidarr[$val['pid']] = $val['tid'];

                $arr = array('type' => 1, 'uid' => $uid, 'tid' => $val['tid'], 'pid' => $val['pid'], 'subject' => $val['subject'], 'comment' => '', 'create_date' => $time);

                // 创建日志
                well_modelog_create($arr);
                // hook website_reply_delete_pids_access_aftre.php
            }

            // hook website_reply_delete_pids_access_end.php
        }

        // hook website_reply_delete_pids_center.php

        !empty($pidarr) AND $r = well_post_delete($pid);

        // hook website_reply_delete_pids_safter.php

        if (!empty($tidarr)) {
            // 更新主题回复数
            $tidarr = array_count_values($tidarr);
            foreach ($tidarr as $tid => $n) {
                $r = well_thread_update($tid, array('posts-' => $n));
            }
        }

        // hook website_reply_delete_pids_end.php

    } else {
        $post = well_post_read($pid);
        !$post AND message(-1, lang('post_not_exists'));

        // hook website_reply_delete_before.php

        $forum = isset($forumlist[$post['fid']]) ? $forumlist[$post['fid']] : NULL;
        !$forum AND message(-1, lang('forum_not_exists'));

        $forum['well_type'] == 0 AND message(-1, lang('user_group_insufficient_privilege'));

        // hook website_reply_delete_after.php

        !forum_access_user($post['fid'], $gid, 'allowpost') AND message(-1, lang('user_group_insufficient_privilege'));

        $allowdelete = forum_access_mod($post['fid'], $gid, 'allowdelete');
        !$allowdelete AND !$post['allowdelete'] AND message(-1, lang('insufficient_delete_privilege'));

        !$allowdelete AND ($post['closed'] OR !$forum['well_comment']) AND message(-1, lang('thread_has_already_closed'));

        $r = well_post_delete($pid);

        // 更新主题回复数
        $r = well_thread_update($post['tid'], array('posts-' => 1));

        include _include(APP_PATH . 'plugin/well_cms_x/model/well_website_modelog.func.php');

        $arr = array('type' => 1, 'uid' => $uid, 'tid' => $post['tid'], 'pid' => $pid, 'subject' => $post['subject'], 'comment' => '', 'create_date' => $time);

        // 创建日志
        well_modelog_create($arr);
    }

    // hook website_reply_delete_end.php

    message(0, lang('delete_successfully'));
}

// hook website_reply_end.php

?>