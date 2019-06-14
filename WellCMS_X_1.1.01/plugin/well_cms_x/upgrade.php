<?php

!defined('DEBUG') AND exit('Forbidden');

include APP_PATH . 'plugin/well_cms_x/model/well_check_db.func.php';

if (well_db_find_field($db->tablepre . 'forum', 'seo_description')) {
    $sql = "ALTER TABLE `{$tablepre}forum` DROP `seo_description`;";
    $r = db_exec($sql);
}

$sql = "ALTER TABLE  `{$tablepre}forum` CHANGE  `threads`  `threads` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0';";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}forum` CHANGE  `todayposts`  `todayposts` INT( 11 ) NOT NULL DEFAULT  '0';";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}forum` CHANGE  `todaythreads`  `todaythreads` INT( 11 ) NOT NULL DEFAULT  '0';";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}forum` CHANGE  `orderby`  `orderby` TINYINT( 1 ) NOT NULL DEFAULT  '0';";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}website_flag_thread` DROP INDEX  `flagid` ,ADD INDEX  `flagid` (  `flagid` ,  `id` );";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}website_flag` ADD  `icon` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `count`;";
$r = db_exec($sql);

$sql = "ALTER TABLE  `{$tablepre}website_thread` ADD  `tag` CHAR( 94 ) NOT NULL DEFAULT  '' AFTER  `icon`;";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}website_tag` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(10) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}website_tag_thread` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

?>