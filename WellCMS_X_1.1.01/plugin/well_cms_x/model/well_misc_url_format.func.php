<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
 
// hook model_website_format_start.php

// 导航 格式化URL
function well_nav_format($forum, $extra = array())
{
    // hook model_website_nav_format_start.php
    if ($forum['well_type']) {
        // hook model_website_nav_format_before.php
        if ($forum['well_forum_type'] == 1) {
            // 频道
            // hook model_website_nav_format_category_before.php
            $url = url('category-' . $forum['fid'], $extra);
            // hook model_website_nav_format_category_after.php
        } else {
            // 根据模型 返回列表URL
            // hook model_website_nav_format_list_before.php
            $url = well_nav_list_format($forum, FALSE, $extra);
            // hook model_website_nav_format_list_after.php
        }
        // hook model_website_nav_format_after.php
    } else {
        // hook model_website_nav_format_forum_before.php
        $url = url('forum-' . $forum['fid'], $extra);
        // hook model_website_nav_format_forum_after.php
    }
    // hook model_website_nav_format_end.php
    return $url;
}

// 区分
function well_nav_list_format($forum, $page = FALSE, $extra = array())
{
    // hook model_website_nav_list_format_start.php
    // 自己可以根据需要按照well_model区分路径
    $page = $page === TRUE ? '-{page}' : '';
    $url = url('list-' . $forum['fid'] . $page, $extra);
    /*if ($forum['well_model'] == 0) {
        $url = url('list-' . $forum['fid']);
    }*/
    // hook model_website_nav_list_format_end.php
    return $url;
}

// 单独导航URL
function well_nav_url($fid, $page = FALSE, $extra = array())
{
    $forumlist = GLOBALS('forumlist');
    $forum = array_value($forumlist, $fid);
    if (!$forum) return '';

    // hook model_website_nav_format_start.php
    if ($forum['well_type']) {
        // hook model_website_nav_format_before.php
        if ($forum['well_forum_type'] == 1) {
            // 频道
            // hook model_website_nav_format_category_before.php
            $page = $page === TRUE ? '-{page}' : '';
            $url = url('category-' . $forum['fid'] . $page, $extra);
            // hook model_website_nav_format_category_after.php
        } else {
            // 根据模型 返回列表URL
            // hook model_website_nav_format_list_before.php
            $url = well_nav_list_format($forum, $page, $extra);
            // hook model_website_nav_format_list_after.php
        }
        // hook model_website_nav_format_after.php
    } else {
        // hook model_website_nav_format_forum_before.php
        $url = url('forum-' . $forum['fid'], $extra);
        // hook model_website_nav_format_forum_after.php
    }
    // hook model_website_nav_format_end.php
    return $url;
}

function well_flag_url($flagid, $page = FALSE, $extra = array())
{
    $page = $page === TRUE ? '-{page}' : '';
    // hook model_flag_url_format_start.php
    $url = url('flag-' . $flagid . $page, $extra);
    // hook model_flag_url_format_end.php
    return $url;
}

function well_tag_url($tagid, $page = FALSE, $extra = array())
{
    $page = $page === TRUE ? '-{page}' : '';
    // hook model_tag_url_format_start.php
    $url = url('tag-' . $tagid . $page, $extra);
    // hook model_tag_url_format_end.php
    return $url;
}

// 格式化主题URL
function well_thread_url_format(&$threadlist)
{
    // hook model_website_thread_url_format_start.php
    foreach ($threadlist as &$_thread) {
        $_thread['url'] = well_url_format($_thread['fid'], $_thread['tid']);
        // hook model_website_thread_url_format_after.php
    }
    // hook model_website_thread_url_format_end.php
}

// 一 格式化 URL 根据fid查询别名 xx/tid-{page}.htm
function well_url_format($fid, $tid, $page = FALSE, $extra = array())
{
    // hook model_website_url_format_start.php
    $forumlist = GLOBALS('forumlist');
    if (!isset($forumlist[$fid])) return url('read-' . $tid, $extra);
    // hook model_website_url_format_before.php
    $forum = $forumlist[$fid];
    $page = $page === TRUE ? '-{page}' : '';
    // hook model_website_url_format_after.php
    if ($forum['well_type']) {
        // CMS
        // hook model_website_url_format_model_start.php
        // 自己可以根据需要按照well_model区分路径
        $url = url('read-' . $tid . $page, $extra);
        /*if ($forum['well_model'] == 0) {
            $url = url('read-' . $tid . $page);
        }*/
        // hook model_website_url_format_model_end.php
    } else {
        // BBS
        // hook model_website_url_format_thread_before.php
        $url = url('thread-' . $tid . $page, $extra);
        // hook model_website_url_format_thread_after.php
    }
    // hook model_website_url_format_end.php
    return $url;
}

// 二 格式化 URL 别名 well_alia
function well_url_alias($forum, $tid, $extra = array())
{
    // hook model_website_url_alias_start.php
    $url = url('thread-' . $tid, $extra);
    // hook model_website_url_alias_end.php
    return $url;
}

// hook model_website_format_end.php

?>