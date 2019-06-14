<?php exit;
    // 过滤不在导航显示的栏目$forumlist_filter
    foreach ($forumlist as $fid => $forum) {
        if ($forum['well_nav_display'] == 0) {
            //unset($forumlist_filter[$fid]);
            unset($forumlist[$fid]);
        }
    }
?>