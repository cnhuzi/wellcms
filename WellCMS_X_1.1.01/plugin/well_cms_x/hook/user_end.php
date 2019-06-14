<?php exit;
elseif($action == 'thread') {

        // hook user_thread_start.php

    $_uid = param(2, 0);
    empty($_uid) AND $_uid = $uid;
    $_user = user_read($_uid);

    empty($_user) AND message(-1, lang('user_not_exists'));
    $header['title'] = $_user['username'];
    $header['mobile_title'] = $_user['username'];

    $page = param(3, 1);
    $pagesize = 20;
    $totalnum = $_user['threads'];
    $pagination = pagination(url("user-thread-$_uid-{page}"), $totalnum, $page, $pagesize);
    $threadlist = mythread_find_by_uid($_uid, $page, $pagesize);
    thread_list_access_filter($threadlist, $gid);

    // hook user_thread_end.php

    include _include(APP_PATH.'plugin/well_bbs/view/htm/user_thread.htm');

    }
?>