<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$tid = param(1, 0);
$page = param(2, 1);
$pagesize = 20;
$extra = array(); // 插件预留

// hook website_read_start.php

!$tid AND message(-1, lang('data_malformation'));

$thread = well_thread_read_cache($tid);
!$thread AND message(-1, lang('thread_not_exists'));

// hook website_read_before.php

$fid = $thread['fid'];
//$forum = forum_read($fid);
$forum = isset($forumlist[$fid]) ? $forumlist[$fid] : NULL;
!$forum AND message(-1, lang('forum_not_exists'));

// hook website_read_forum_after.php

// 用户是否有读取该栏目的权限
forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('user_group_insufficient_privilege'));

// hook website_read_allowread_after.php

// 站外跳转
if ($thread['link'] == 1) {
    well_thread_inc_views($tid);
    http_location($thread['description']); // 外链在brief
}

// hook website_read_http_location_after.php

// 加载默认的数据和模板
$read_default = 1;

// hook website_read_default_before.php

if ($read_default == 1) {

    $postlist = ($forum['well_comment'] AND $thread['closed'] < 2 AND $thread['posts'] > 0) ? well_post_find_by_tid($tid, $page, $pagesize) : NULL;

    // hook website_read_postlist_after.php

    if ($page == 1) {

        $attachlist = $imagelist = $thread['filelist'] = array();
        $thread['files'] > 0 AND list($attachlist, $imagelist, $thread['filelist']) = well_attach_find_by_tid($tid);

        // hook website_read_attach_after.php

        $thread_data = well_data_read_cache($tid);
        !$thread_data AND message(-1, lang('data_malformation'));

        // hook website_read_data_after.php

        // 大站可用单独的点击服务，减少 db 压力 / if request is huge, separate it from mysql server
        well_thread_inc_views($tid);
    }

    // hook website_read_center.php

    $allowpost = forum_access_user($fid, $gid, 'allowpost') ? 1 : 0;
    $allowupdate = forum_access_mod($fid, $gid, 'allowupdate') ? 1 : 0;
    $allowdelete = forum_access_mod($fid, $gid, 'allowdelete') ? 1 : 0;

    // hook website_read_access_after.php
}

// hook website_read_default_after.php

$pagination = pagination(well_url_format($fid, $tid, TRUE, $extra), $thread['posts'], $page, $pagesize);

// hook website_read_other_thread_start.php

// 缓存统一调用模式，类似相关主题等调用。统一遍历需要的tid，然后合并去重，再遍历主题表，随后再分类
$arrlist = well_website_other_thread($thread);

// 主题所在栏目下所有展示属性和属性主题
$flaglist = array_value($arrlist, 'flag');

// hook website_read_other_thread_end.php

// hook website_read_after.php

$header['title'] = $thread['subject'] . '-' . $forum['name'] . '-' . $conf['sitename'];
$header['mobile_title'] = '';
$header['mobile_link'] = well_nav_format($forum, $extra);
$keyword = $thread['keyword'] ? $thread['keyword'] : $thread['subject'];
$header['keywords'] = $keyword;
$description = $thread['description'] ? $thread['description'] : $thread['brief'];
$header['description'] = $description;
$_SESSION['fid'] = $fid;

// hook website_read_end.php

if ($ajax) {
    //message(0, array('thread' => $thread, 'thread_data' => $thread_data, 'flaglist' => $flaglist));
} else {
    // hook website_read_template_htm.php
    // 这里可以直接hook绑定模型，绑定栏目fid well_model = 0文章 直接hook比较暴力，根据需要自行区分各端
    /*if ($forum['well_model'] && $fid == 1) {
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/read.htm');
    exit;
    }*/
    $read_default == 1 AND include _include(well_website_template(3, $forum));
}

?>