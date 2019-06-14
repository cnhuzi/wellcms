<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');
// 频道页 通过判断频道类型加载不同内容和模板

// hook website_category_start.php

$fid = param(1, 0);
$extra = array(); // 插件预留

!$fid AND message(-1, lang('data_malformation'));
$forum = array_value($forumlist_show, $fid);
!$forum AND message(-1, lang('forum_not_exists'));

// 管理时使用
$extra['fup'] = $fid;

// hook website_category_before.php

// 不是频道
!$forum['well_forum_type'] AND message(-1, lang('data_malformation'));

$website_setting = $website_conf['setting'];
// website_mode
$website_mode = $website_setting['website_mode'];
// tpl_mode
$tpl_mode = $website_setting['tpl_mode'];

// hook website_category_after.php

// 根据栏目类型加载不同代码和模板
switch ($forum['well_model']) {
    // hook website_category_case_start.php
    case '0':
        // 文章
        include _include(APP_PATH . 'plugin/well_cms_x/route/category_article.php');
        break;
    // hook website_category_case_end.php
    default:
        http_location('./');
        break;
}

// hook website_category_end.php

?>