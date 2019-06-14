<?php exit;
elseif($action == 'thread') {

    // hook my_thread_start.php

$page = param(2, 1);
$pagesize = 20;
$totalnum = $user['threads'];

// hook my_profile_thread_list_before.php

$pagination = pagination(url('my-thread-{page}'), $totalnum, $page, $pagesize);
$threadlist = mythread_find_by_uid($uid, $page, $pagesize);

// hook my_thread_end.php

include _include(APP_PATH.'plugin/well_bbs/view/htm/my_thread.htm');
}
?>