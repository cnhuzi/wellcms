<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

include _include(APP_PATH . 'plugin/well_cms_x/model/well_website_modelog.func.php');

$action = param(1);

// hook website_mode_start.php

// 对应forum表well_model字段 0新闻
$model = param('model', 0);
// 路径后台传参需要../
$path = param('path', '');

$forumlist_show = well_website_column($forumlist_show, $model = 0);
$forumarr = arrlist_key_values($forumlist_show, 'fid', 'name');

// hook website_mode_before.php

if ($action == 'top') {

    if ($method == 'GET') {

        // hook website_mode_top_get_start.php

        $well_fup = param('fup', 0);
        if ($well_fup == 0) {
            $fid = param('fid', 0);
            $forum = array_value($forumlist_show, $fid);
            $well_fup = array_value($forum, 'well_fup');
        }

        // hook website_mode_top_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/mode_top.htm');

    } else {

        // hook website_mode_top_start.php

        $top = param('top', 0);

        $tidarr = param('tidarr', array(0));
        empty($tidarr) AND message(-1, lang('please_choose_thread'));

        // hook website_mode_top_before.php

        $threadlist = well_thread_find_by_tids($tidarr);

        // hook website_mode_top_after.php

        foreach ($threadlist as &$thread) {
            $fid = $thread['fid'];
            $tid = $thread['tid'];

            // hook website_mode_top_log_create_start.php

            if ($top == 3 && ($gid != 1 && $gid != 2)) continue;

            // hook website_mode_top_log_create_before.php

            if (forum_access_mod($fid, $gid, 'allowtop')) {
                // 清理置顶
                $top == 0 AND well_thread_top_delete($tid);
                $top > 0 AND well_thread_top_change($tid, $top);

                // hook website_mode_top_log_create_center.php

                $arr = array(
                    'type' => ($top ? 3 : 4),
                    'uid' => $uid,
                    'tid' => $thread['tid'],
                    'subject' => $thread['subject'],
                    'comment' => '',
                    'create_date' => $time
                );

                // hook website_mode_top_log_create_after.php

                well_modelog_create($arr);

                // hook website_mode_top_log_create_end.php
            }
        }

        // hook website_mode_top_end.php

        message(0, lang('set_completely'));
    }

} elseif ($action == 'close') {

    if ($method == 'GET') {

        // hook website_mode_close_get_start.php

        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/mode_close.htm');

    } else {

        $close = param('close', 0);

        $tidarr = param('tidarr', array(0));
        empty($tidarr) AND message(-1, lang('please_choose_thread'));
        $threadlist = well_thread_find_by_tids($tidarr);

        // hook website_mode_close_start.php

        $tids = array();

        if ($close == 1) {
            $type = 5;
        } elseif ($close == 2) {
            $type = 6;
        } else {
            $type = 7;
        }

        foreach ($threadlist as &$thread) {

            $thread['top'] AND $thread['closed'] != $close AND well_thread_top_cache_delete($thread['fid']);

            if (forum_access_mod($thread['fid'], $gid, 'allowtop')) {

                $tids[] = $thread['tid'];

                // hook website_mode_close_log_create_before.php

                $arr = array('type' => $type, 'uid' => $uid, 'tid' => $thread['tid'], 'subject' => $thread['subject'], 'comment' => '', 'create_date' => $time);

                // hook website_mode_close_log_create_after.php

                well_modelog_create($arr);
            }
        }

        !empty($tids) AND well_thread_update($tids, array('closed' => $close));

        // hook website_mode_close_end.php

        message(0, lang('set_completely'));
    }

} elseif ($action == 'delete') {

    if ($method == 'GET') {

        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/mode_delete.htm');

    } else {

        $tidarr = param('tidarr', array(0));
        empty($tidarr) AND message(-1, lang('please_choose_thread'));

        $threadlist = well_thread_find_by_tids($tidarr);

        // hook website_mode_delete_start.php

        foreach ($threadlist as &$thread) {
            $fid = $thread['fid'];
            $tid = $thread['tid'];
            if (forum_access_mod($fid, $gid, 'allowdelete')) {
                well_thread_delete_all($tid);
                $arr = array('type' => 1, 'uid' => $uid, 'tid' => $thread['tid'], 'subject' => $thread['subject'], 'comment' => '', 'create_date' => $time);

                // hook website_mode_delete_log_create_after.php

                well_modelog_create($arr);
            }
        }

        // hook website_mode_delete_end.php

        message(0, lang('delete_completely'));
    }


} elseif ($action == 'move') {

    if ($method == 'GET') {

        // hook website_mode_move_get_start.php

        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/mode_move.htm');

    } else {

        $tidarr = param('tidarr', array(0));
        empty($tidarr) AND message(-1, lang('please_choose_thread'));
        $threadlist = well_thread_find_by_tids($tidarr);

        $newfid = param('newfid', 0);
        !forum_read($newfid) AND message(1, lang('forum_not_exists'));

        // hook website_mode_move_start.php

        $tids = $fids = array();
        foreach ($threadlist as &$thread) {

            // hook website_mode_move_foreach_start.php

            if (forum_access_mod($thread['fid'], $gid, 'allowmove')) {
                if ($thread['fid'] == $newfid) continue;
                $tids[] = $thread['tid'];
                $fids[$thread['tid']] = $thread['fid'];

                // hook website_mode_move_foreach_before.php

                $arr = array('type' => 2, 'uid' => $uid, 'tid' => $thread['tid'], 'subject' => $thread['subject'], 'create_date' => $time);

                // hook website_mode_move_foreach_end.php

                well_modelog_create($arr);
            }
        }

        // hook website_mode_move_fids_before.php

        if (!empty($fids)) {
            // 旧栏目主题数需要更新
            $fids = array_count_values($fids);
            foreach ($fids as $k => $v) {
                forum__update($k, array('threads-' => $v));
            }
        }

        // hook website_mode_move_thread_update_before.php

        // 主题主表 附表 回复 所属栏目更新
        !empty($tids) AND well_thread_update_all($tids, array('fid' => $newfid));
        !empty($tids) AND well_thread_tid_update($tids, $newfid);

        // hook website_mode_move_forum_update_before.php

        // 新栏目增加主题数
        forum__update($newfid, array('threads+' => (count($tids))));

        // hook website_mode_move_forum_cache_before.php

        // 清理下缓存
        forum_list_cache_delete();

        // hook website_mode_move_end.php

        message(0, lang('move_completely'));

    }

} elseif ($action == 'search') {

    // hook website_mode_search_start.php

    $keyword = param('keyword');
    !$keyword AND $keyword = param(2);
    $keyword = trim($keyword);
    $range = param(3, 1);
    $page = param(4, 1);
    $pagesize = 20;
    $extra = array(); // 插件预留

    // hook website_mode_search_before.php

    $keyword_decode = well_search_keyword_safe(xn_urldecode($keyword));
    $keyword_arr = explode(' ', $keyword_decode);
    $threadlist = array();
    $pagination = '';
    $active = '';

    // hook website_mode_search_middle.php

    $search_type = 'like';

    if ($keyword) {
        // hook website_mode_search_keyword_start.php
        if ($search_type == 'like') {

            // hook website_mode_search_keyword_like_start.php

            if ($range == 1) {
                $threadlist = well_thread_find_by_keyword($keyword_decode);
            }

            // hook website_mode_search_keyword_like_end.php

        } elseif ($search_type == 'site_url') {

            $site_url = 'https://www.baidu.com/s?wd=site%3A' . _SERVER('HTTP_HOST') . '%20{keyword}';
            $url = str_replace('{keyword}', $keyword_decode, $site_url);
            http_location($url);
        }
        // hook website_mode_search_keyword_end.php
    }

    // hook website_mode_search_end.php

    if ($ajax) {
        if ($threadlist) {
            foreach ($threadlist as &$thread) $thread = well_thread_safe_info($thread);
            message(0, $threadlist);
        }
    } else {
        // hook website_mode_search_template.php
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/search.htm');
    }
}

// hook website_mode_after.php

function well_search_keyword_safe($s)
{
    $s = strip_tags($s);
    $s = str_replace(array('\'', '\\', '"', '%', '<', '>', '`', '*', '&', '#'), '', $s);
    $s = preg_replace('#\s+#', ' ', $s);
    $s = trim($s);
    //$s = preg_replace('#[^\w\-\x4e00-\x9fa5]+#i', '', $s);
    return $s;
}

// hook website_mode_end.php

?>