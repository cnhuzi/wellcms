<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1, 'list');

$website_forumlist = well_return_column($forumlist);
// hook website_admin_top_start.php

if ($action == 'list') {

    // hook website_admin_top_list_start.php

    if ($method == 'GET'){

        // hook website_admin_top_list_get_start.php

        $header['title'] = lang('top');
        $header['mobile_title'] = lang('top');

        $fid = param(2, 0);
        $page = param(3, 1);
        $pagesize = 25;
        $extra = array('model' => 0, 'fid' => $fid, 'path' => '../'); // 插件预留

        // hook website_admin_top_list_get_before.php

        if ($fid) {
            $forum = array_value($forumlist, $fid);

            // hook website_admin_top_list_get_forum_after.php

            $well_fup = array_value($forum, 'well_fup');

            // hook website_admin_top_list_get_fup_after.php

            $fid = $forum['well_fup'] ? array($fid, $forum['well_fup']) : $fid;

            // hook website_admin_top_list_get_fid_after.php

            // 有父频道则全站 频道 栏目全部置顶 没有则全站和栏目全部置顶
            $top = $well_fup ? array(1, 2, 3) : array(1, 3);

            // hook website_admin_top_list_get_top_after.php

            $n = well_thread_top_count_by_fid($fid);
        } else {
            $top = 3;

            // hook website_admin_top_list_get_top_middle.php

            $n = well_thread_top_count_by_top($top);
        }

        // hook website_admin_top_list_get_find_before.php

        $threadlist = $n ? well_thread_top_find_cache($fid, $top) : '';

        // hook website_admin_top_list_get_after.php

        $pagination = pagination(url('top-' . $fid . '-{page}', $extra), $n, $page, $pagesize);

        // hook website_admin_top_list_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_list.htm');
    }

    // hook website_admin_top_list_end.php
}

// hook website_admin_top_end.php

?>