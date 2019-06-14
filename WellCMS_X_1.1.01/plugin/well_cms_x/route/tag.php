<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');
// tag

$action = param(1);

// hook website_tag_start.php

if ($action == 'list') {

    // hook website_tag_list_start.php

    $page = param(2, 1);
    $pagesize = 30;
    $extra = array(); // 插件预留

    // hook website_tag_list_before.php

    $count = well_tag_count();
    $arrlist = $count ? well_tag_find($page, $pagesize) : NULL;

    // hook website_tag_list_middle.php

    $pagination = pagination(url('tag-list-{page}'), $count, $page, $pagesize);

    // hook website_tag_list_after.php

    $header['title'] = lang('well_tag') . '-' . $conf['sitename'];
    $header['mobile_title'] = lang('well_tag');
    $header['mobile_link'] = url('tag-list');
    $header['keywords'] = lang('well_tag') . '-' . $conf['sitename'];
    $header['description'] = lang('well_tag') . '-' . $conf['sitename'];
    $_SESSION['fid'] = 0;

    // hook website_tag_list_end.php

    if ($ajax) {
        //message(0, $arrlist);
    } else {
        // hook website_tag_list_template_htm.php
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/tag_list.htm');
    }

} else {

    // tag-tagid-page.htm
    $tagid = param(1, 0);
    !$tagid AND message(-1, lang('data_malformation'));

    $page = param(2, 1);
    $pagesize = 20;
    $extra = array(); // 插件预留

    // hook website_tag_before.php

    $read = well_tag_read_by_tagid_cache($tagid);
    !$read AND message(-1, lang('well_tag_not_existed'));

    // hook website_tag_center.php

    $arr = well_tag_thread_find($tagid, $page, $pagesize);
    if ($arr) {
        $tidarr = arrlist_values($arr, 'tid');
        $threadlist = well_thread_find($tidarr, $pagesize);
    } else {
        $threadlist = NULL;
    }

    // hook website_tag_middle.php

    $pagination = pagination(well_tag_url($tagid, FALSE, $extra), $read['count'], $page, $pagesize);

    // hook website_tag_after.php

    $header['title'] = empty($read['title']) ? $read['name'] . lang('well_tag') . '-' . $conf['sitename'] : $read['title'];
    $header['mobile_title'] = $read['name'];
    $header['mobile_link'] = url('tag-list', $extra);
    $header['keywords'] = empty($read['keywords']) ? $read['name'] : $read['keywords'];
    $header['description'] = empty($read['description']) ? $read['name'] : $read['description'];
    $_SESSION['fid'] = 0;

    // hook website_tag_end.php

    if ($ajax) {
        //message(0, array('tag' => $read, 'thread' => $threadlist));
    } else {
        // hook website_tag_template_htm.php
        include _include(well_website_tag_template($read));
    }
}

?>