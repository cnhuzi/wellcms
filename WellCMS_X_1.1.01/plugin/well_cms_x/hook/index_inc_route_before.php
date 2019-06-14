<?php exit;
$forum_nav = well_filter_nav($forumlist_show);
// 二叉树导航 需要的时候自行启用
//$forum_nav = well_all_category_tree($forumlist_show);
// 配置数据
$website_conf = setting_get('well_website_conf');
?>