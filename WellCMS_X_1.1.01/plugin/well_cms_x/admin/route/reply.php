<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

//$website_forumlist = well_return_column($forumlist);
$action = param(1, 'list');

// hook website_admin_reply_start.php

if ($action == 'list') {

    // hook website_admin_reply_list_start.php

    if ($method == 'GET') {

        // hook website_admin_reply_list_get_start.php
        // 0已验证 1待验证
        $verify = param('verify', 0);
        $page = param(2, 1);
        $pagesize = 25;
        // 插件预留
        $extra = array('verify' => 0, 'path' => '../');
        $threadlist = NULL;

        // hook website_admin_reply_list_get_before.php

        // 所有审核过的回复
        if ($verify == 0) {

            // hook website_admin_reply_list_get_pid_before.php

            $n = well_post_pid_count();

            // hook website_admin_reply_list_get_pid_after.php

            // 全站全部回复数据
            $n AND $postlist = well_post_find_all($page, $pagesize);

            // hook website_admin_reply_list_get_postlist_after.php
        } elseif ($verify == 1) {

            // hook website_admin_reply_list_get_verify_start.php

            // hook website_admin_reply_list_get_verify_end.php
        }

        // hook website_admin_reply_list_get_middle.php

        well_thread_list_access_filter($threadlist, $gid);

        $pagination = pagination(url('reply-list-{page}', $extra), $n, $page, $pagesize);

        // hook website_admin_reply_list_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/reply_list.htm');
    }

    // hook website_admin_reply_list_end.php
}

// hook website_admin_reply_end.php

?>