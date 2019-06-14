<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');
// 属性

// hook website_flag_start.php

$flagid = param(1, 0);
$page = param(3, 1);
$pagesize = 20;
$extra = array(); // 插件预留

// hook website_flag_list_start.php

!$flagid AND message(1, lang('data_malformation'));

$read = well_website_flag_read($flagid);
!$read AND message(1, lang('thread_not_exists'));

// hook website_flag_list_before.php

$n = well_website_flag_thread_count_by_flagid($flagid);
$arrlist = $n ? well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize) : NULL;

// hook website_flag_list_center.php

if ($arrlist) {
    $tidarr = arrlist_values($arrlist, 'tid');
    $threadlist = well_thread_find($tidarr, $pagesize);
}

$pagination = pagination(well_flag_url($flagid, TRUE, $extra), $n, $page, $pagesize);

// hook website_flag_list_after.php

$header['title'] = empty($read['title']) ? $read['name'] . '-' . $conf['sitename'] : $read['title'];
$header['mobile_title'] = $read['name'];
$header['mobile_link'] = well_flag_url($flagid, FALSE, $extra);
$header['keywords'] = empty($read['keywords']) ? $read['name'] : $read['keywords'];
$header['description'] = empty($read['description']) ? $read['name'] : $read['description'];
$_SESSION['fid'] = 0;

// hook website_flag_list_end.php

//include _include(APP_PATH . 'plugin/well_cms_x/view/htm/flag_list.htm');
include _include(well_website_flag_template($read));

/*if ($action == 'list') {

    $flagid = param(2, 0);
    $page = param(3, 1);
    $pagesize = 20;
    $extra = array(); // 插件预留

    // hook website_flag_list_start.php

    !$flagid AND message(1, lang('data_malformation'));

    $read = well_website_flag_read($flagid);
    !$read AND message(1, lang('thread_not_exists'));

    // hook website_flag_list_before.php

    $n = well_website_flag_thread_count_by_flagid($flagid);
    $arrlist = $n ? well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize) : NULL;

    // hook website_flag_list_center.php

    if ($arrlist) {
        $tidarr = arrlist_values($arrlist, 'tid');
        $threadlist = well_thread_find($tidarr, $pagesize);
    }

    $pagination = pagination(well_flag_url($flagid, TRUE, $extra), $n, $page, $pagesize);

    // hook website_flag_list_after.php

    $header['title'] = $read['name'] . '-' . $conf['sitename'];
    $header['mobile_title'] = $read['name'];
    $header['mobile_link'] = well_flag_url($flagid, FALSE, $extra);
    $header['keywords'] = $read['name'];
    $header['description'] = $read['name'];
    $active = 'default';
    $_SESSION['fid'] = 0;

    // hook website_flag_list_end.php

    include _include(APP_PATH . 'plugin/well_cms_x/view/htm/flag_list.htm');

} else {

    // flag-page.htm
    $page = param(1, 1);
    $pagesize = 20;
    $extra = array(); // 插件预留

    // hook website_flag_before.php

    $n = 0;
    if ($forumlist_show) {
        $forumarr = well_all_category($forumlist_show);
        $fids = arrlist_values($forumarr, 'fid');

        $n = well_website_flag_count($fids);
        // 所有属性
        $arrlist = $n ? well_website_flag_find($fids, $page, $pagesize) : NULL;
    }

    // hook website_flag_center.php

    $pagination = pagination(url('flag-{page}'), $n, $page, $pagesize);

    // hook website_flag_after.php

    $header['title'] = lang('well_flag') . '-' . $conf['sitename'];
    $header['mobile_title'] = lang('well_flag');
    $header['mobile_link'] = url('flag');
    $header['keywords'] = lang('well_flag');
    $header['description'] = lang('well_flag');

    // hook website_flag_end.php

    if ($ajax) {
        //message(0, $arrlist);
    } else {
        // hook website_flag_template_htm.php
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/flag_default.htm');
    }
}*/

?>