<?php
/*
 * Copyright (C) 燃烧的冰 81340116@qq.com
 */

!defined('DEBUG') AND exit('Forbidden');
$tablepre = $db->tablepre;

# 版块访问规则, forum.accesson 开启时生效, 记录行数： fid * gid
$sql = "CREATE TABLE `{$tablepre}thread` (
  `fid` smallint(6) NOT NULL default '0',			# 版块 id
  `tid` int(11) unsigned NOT NULL auto_increment,		# 主题id
  `top` tinyint(1) NOT NULL default '0',			# 置顶级别: 0: 普通主题, 1-3 置顶的顺序
  `uid` int(11) unsigned NOT NULL default '0',		# 用户id
  `userip` int(11) unsigned NOT NULL default '0',		# 发帖时用户ip ip2long()，主要用来清理
  `subject` char(128) NOT NULL default '',		# 主题
  `create_date` int(11) unsigned NOT NULL default '0',	# 发帖时间
  `last_date` int(11) unsigned NOT NULL default '0',	# 最后回复时间
  `views` int(11) unsigned NOT NULL default '0',		# 查看次数, 剥离出去，单独的服务，避免 cache 失效
  `posts` int(11) unsigned NOT NULL default '0',		# 回帖数
  `images` tinyint(6) NOT NULL default '0',		# 附件中包含的图片数
  `files` tinyint(6) NOT NULL default '0',		# 附件中包含的文件数
  `mods` tinyint(6) NOT NULL default '0',			# 预留：版主操作次数，如果 > 0, 则查询 modlog，显示斑竹的评分
  `closed` tinyint(1) unsigned NOT NULL default '0',	# 预留：是否关闭，关闭以后不能再回帖、编辑。
  `firstpid` int(11) unsigned NOT NULL default '0',	# 首贴 pid
  `lastuid` int(11) unsigned NOT NULL default '0',	# 最近参与的 uid
  `lastpid` int(11) unsigned NOT NULL default '0',	# 最后回复的 pid
  PRIMARY KEY (`tid`),					# 主键
  KEY (`lastpid`),					# 最后回复排序
  KEY (`fid`, `tid`),					# 发帖时间排序，正序。数据量大时可以考虑建立小表，对小表进行分区优化，只有数据量达到千万级以上时才需要。
  KEY (`fid`, `lastpid`)					# 顶贴时间排序，倒序
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}thread_top` (
  `fid` smallint(6) NOT NULL default '0',			# 查找板块置顶
  `tid` int(11) unsigned NOT NULL default '0',		# tid
  `top` int(11) unsigned NOT NULL default '0',		# top: 0 是普通最新贴，> 0 置顶贴。
  PRIMARY KEY (`tid`),					#
  KEY (`top`, `tid`),					# 最新贴：top=0 order by tid desc / 全局置顶： top=3
  KEY (`fid`, `top`)					# 版块置顶的贴 fid=1 and top=1
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}post` (
  `tid` int(11) unsigned NOT NULL default '0',		# 主题id
  `pid` int(11) unsigned NOT NULL auto_increment,		# 帖子id
  `uid` int(11) unsigned NOT NULL default '0',		# 用户id
  `isfirst` int(11) unsigned NOT NULL default '0',	# 是否为首帖，与 thread.firstpid 呼应
  `create_date` int(11) unsigned NOT NULL default '0',	# 发贴时间
  `userip` int(11) unsigned NOT NULL default '0',		# 发帖时用户ip ip2long()
  `images` smallint(6) NOT NULL default '0',		# 附件中包含的图片数
  `files` smallint(6) NOT NULL default '0',		# 附件中包含的文件数
  `doctype` tinyint(3) NOT NULL default '0',		# 类型，0: html, 1: txt 2: markdown 3: ubb
  `quotepid` int(11) NOT NULL default '0',		# 引用哪个 pid，可能不存在
  `message` longtext NOT NULL,				# 内容，用户提示的原始数据
  `message_fmt` longtext NOT NULL,			# 内容，存放的过滤后的html内容，可以定期清理，减肥。
  PRIMARY KEY (`pid`),
  KEY (`tid`, `pid`),
  KEY (`uid`)						# 我的回帖，清理数据需要
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}attach` (
  `aid` int(11) unsigned NOT NULL auto_increment ,	# 附件id
  `tid` int(11) NOT NULL default '0',			# 主题id
  `pid` int(11) NOT NULL default '0',			# 帖子id
  `uid` int(11) NOT NULL default '0',			# 用户id
  `filesize` int(8) unsigned NOT NULL default '0',	# 文件尺寸，单位字节
  `width` mediumint(8) unsigned NOT NULL default '0',	# width > 0 则为图片
  `height` mediumint(8) unsigned NOT NULL default '0',	# height
  `filename` char(120) NOT NULL default '',		# 文件名称，会过滤，并且截断，保存后的文件名，不包含URL前缀 upload_url
  `orgfilename` char(120) NOT NULL default '',		# 上传的原文件名
  `filetype` char(7) NOT NULL default '',			# 文件类型: image/txt/zip，小图标显示
  `create_date` int(11) unsigned NOT NULL default '0',	# 文件上传时间 UNIX 时间戳
  `comment` char(100) NOT NULL default '',		# 文件注释 方便于搜索
  `downloads` int(11) NOT NULL default '0',		# 下载次数，预留
  `credits` int(11) NOT NULL default '0',			# 需要的积分，预留
  `golds` int(11) NOT NULL default '0',			# 需要的金币，预留
  `rmbs` int(11) NOT NULL default '0',			# 需要的人民币，预留
  `isimage` tinyint(1) NOT NULL default '0',		# 是否为图片
  PRIMARY KEY (`aid`),					# aid
  KEY pid (`pid`),					# 每个帖子下多个附件
  KEY uid (`uid`)						# 我的附件，清理数据需要。
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}mythread` (
  `uid` int(11) unsigned NOT NULL default '0',		# uid
  `tid` int(11) unsigned NOT NULL default '0',		# 用来清理，删除板块的时候需要
  PRIMARY KEY (`uid`, `tid`)				# 每一个帖子只能插入一次 unique
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}mypost` (
  `uid` int(11) unsigned NOT NULL default '0',		# uid
  `tid` int(11) unsigned NOT NULL default '0',		# 用来清理
  `pid` int(11) unsigned NOT NULL default '0',		#
  PRIMARY KEY (`uid`, `pid`),
  KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}modlog` (
  `logid` int(11) unsigned NOT NULL auto_increment,	# logid
  `uid` int(11) unsigned NOT NULL default '0',		# 版主 uid
  `tid` int(11) unsigned NOT NULL default '0',		# 主题id
  `pid` int(11) unsigned NOT NULL default '0',		# 帖子id
  `subject` char(32) NOT NULL default '',			# 主题
  `comment` char(64) NOT NULL default '',			# 版主评价
  `rmbs` int(11) NOT NULL default '0',			# 加减人民币, 预留
  `create_date` int(11) unsigned NOT NULL default '0',	# 时间
  `action` char(16) NOT NULL default '',			# top|delete|untop
  PRIMARY KEY (`logid`),
  KEY (`uid`, `logid`),
  KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}queue` (
  queueid int(11) unsigned NOT NULL default '0',		# 队列 id
  v int(11) NOT NULL default '0',			# 队列中存放的数据，只能为 int
  expiry int(11) unsigned NOT NULL default '0',		# 过期时间，默认 0，不过期
  UNIQUE KEY(queueid, v),
  KEY(expiry)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}table_day` (
  `year` smallint(11) unsigned NOT NULL DEFAULT '0' COMMENT '年',	#
  `month` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '月', 	#
  `day` tinyint(11) unsigned NOT NULL DEFAULT '0' COMMENT '日', 		#
  `create_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '时间戳', 	#
  `table` char(16) NOT NULL default '' COMMENT '表名',			#
  `maxid` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '最大ID', 	#
  `count` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '总数', 		#
  PRIMARY KEY (`year`, `month`, `day`, `table`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

// 初始化
$r === false AND message(-1, '创建数据表结构失败');

?>