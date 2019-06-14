<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

// hook website_list_start.php

$fid = param(1, 0);
$page = param(2, 1);
$threadlist = $flaglist = array();
$extra = array(); // 插件预留
$active = 'default';

// hook website_list_before.php

!$fid AND message(1, lang('data_malformation'));

//$forum = forum_read($fid);
$forum = isset($forumlist[$fid]) ? $forumlist[$fid] : NULL;
!$forum AND message(1, lang('forum_not_exists'));

// 管理时使用
$extra['fid'] = $fid;

// BBS栏目直接跳转至BBS栏目
$forum['well_type'] == 0 AND http_location(url('forum-' . $forum['fid']));

// hook website_list_bbs_before.php

// 用户是否有读取该栏目的权限
forum_access_user($fid, $gid, 'allowread') OR message(-1, lang('insufficient_visit_forum_privilege'));

$pagesize = array_value($forum, 'well_pagesize', 20);

// hook website_list_top_before.php

// 加载默认的数据和模板
$list_default = 1;

// hook website_list_default_before.php

if ($list_default == 1) {

    // 从默认的地方读取主题列表
    $thread_list_from_default = 1;

    // hook website_list_top_after.php

    if ($thread_list_from_default) {
        $pagination = pagination(well_nav_url($fid, TRUE, $extra), $forum['threads'], $page, $pagesize);
        $threadrank = $forum['well_thread_rank'] ? TRUE : FALSE;
        $threadlist = well_thread_find_by_fid($fid, $page, $pagesize, $threadrank);
        unset($threadrank);
    }

    // 栏目下属性
    $flaglist = $forum['well_flag'] ? well_website_flag_forum_cache($fid) : NULL;

    // hook website_list_flag_after.php
}

// hook website_list_after.php

$seo_title = ($forum['seo_title'] ? $forum['seo_title'] : $forum['name']) . '-' . $conf['sitename'];
$header['title'] = strip_tags($seo_title);
$header['mobile_title'] = $forum['name'];
$header['mobile_link'] = well_nav_format($forum, $extra);
$seo_keywords = $forum['seo_keywords'] ? $forum['seo_keywords'] : $forum['name'];
$header['keywords'] = strip_tags($seo_keywords);
$header['description'] = strip_tags($forum['brief']);
$_SESSION['fid'] = $fid;

// hook website_list_end.php

if ($ajax) {
    //message(0, array('threadlist' => $threadlist, 'flaglist' => $flaglist));
} else {
    // 加载默认的模板
    // hook website_list_template_htm.php
    // 这里可以直接hook绑定模型，绑定栏目fid，也可以到well_website_template()函数里绑定 well_model = 0新闻 直接hook比较暴力，根据需要自行区分各端
    /*if ($forum['well_model'] && $fid == 1) {
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/list.htm');
    exit;
    }*/
    $list_default == 1 AND include _include(well_website_template(2, $forum));
}

?>