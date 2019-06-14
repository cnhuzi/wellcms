<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Forbidden');
include _include(APP_PATH . 'plugin/well_cms_x/model/well_db_check.func.php');

$tablepre = $db->tablepre;

$sql = "DROP TABLE IF EXISTS {$tablepre}website_thread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_data;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_thread_tid;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_post;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_post_pid;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_attach;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_thread_top;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_flag;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_flag_thread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_modelog;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_tag;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}website_tag_thread;";
$r = db_exec($sql);

if (well_db_find_table($db->tablepre . 'thread')) {
    $sql = "ALTER TABLE {$tablepre}forum DROP `well_nav_display`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_display`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_news`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_list_news`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_channel_news`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_comment`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_fup`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_son`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_type`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_model`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_forum_type`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl_cate`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_tpl_read`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_pagesize`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_flag`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_picture_size`;";
    $r = db_exec($sql);

    $sql = "ALTER TABLE {$tablepre}forum DROP `well_thread_rank`;";
    $r = db_exec($sql);
} else {

    $sql = "DROP TABLE IF EXISTS {$tablepre}forum;";
    $r = db_exec($sql);

    $sql = "DROP TABLE IF EXISTS {$tablepre}forum_access;";
    $r = db_exec($sql);
}

// 清空所有附件
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'website_attach/', 1);
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'website_mainpic/', 1);
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'website_flag/', 1);
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'tmp/', 1);
forum_list_cache_delete();

//xn_unlink('../view/img/logo.png');
//xn_unlink('../view/img/avatar.png');
xn_unlink('../view/img/nopic.png');
//xn_unlink('../view/img/water-small.png');
//xn_copy('../view/img/backup_logo.png', '../view/img/logo.png');
//xn_copy('../view/img/backup_avatar.png', '../view/img/avatar.png');
//xn_copy('../view/img/backup_water-small.png', '../view/img/water-small.png');
clearstatcache();

setting_delete('well_website_conf');

?>