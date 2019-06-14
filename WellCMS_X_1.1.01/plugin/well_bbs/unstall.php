<?php
/*
 * Copyright (C) 燃烧的冰 81340116@qq.com
 */

!defined('DEBUG') AND exit('Forbidden');

$tablepre = $db->tablepre;

$sql = "DROP TABLE IF EXISTS {$tablepre}thread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}thread_top;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}post;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}attach;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}mythread;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}mypost;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}modlog;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}queue;";
$r = db_exec($sql);

$sql = "DROP TABLE IF EXISTS {$tablepre}table_day;";
$r = db_exec($sql);

// 清空所有附件
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'attach/', 1);
rmdir_recusive(APP_PATH . $conf['upload_path'] . 'tmp/', 1);

?>