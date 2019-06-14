# 表结构

### 用户表 ###
DROP TABLE IF EXISTS `wellcms_user`;
CREATE TABLE `wellcms_user` (
  `uid` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '用户编号',
  `gid` smallint(6) unsigned NOT NULL DEFAULT '0' COMMENT '用户组编号',	# 如果要屏蔽，调整用户组即可
  `email` char(40) NOT NULL DEFAULT '' COMMENT '邮箱',
  `username` char(32) NOT NULL DEFAULT '' COMMENT '用户名',	# 不可以重复
  `realname` char(16) NOT NULL DEFAULT '' COMMENT '用户名',	# 真实姓名，天朝预留
  `idnumber` char(19) NOT NULL DEFAULT '' COMMENT '用户名',	# 真实身份证号码，天朝预留
  `password` char(32) NOT NULL DEFAULT '' COMMENT '密码',
  `password_sms` char(16) NOT NULL DEFAULT '' COMMENT '密码',	# 预留，手机发送的 sms 验证码
  salt char(16) NOT NULL DEFAULT '' COMMENT '密码混杂',
  `mobile` char(11) NOT NULL DEFAULT '' COMMENT '手机号',		# 预留，供二次开发扩展
  `qq` char(15) NOT NULL DEFAULT '' COMMENT 'QQ',			# 预留，供二次开发扩展，可以弹出QQ直接聊天
  `threads` int(11) NOT NULL DEFAULT '0' COMMENT '发帖数',		#
  `posts` int(11) NOT NULL DEFAULT '0' COMMENT '回帖数',		#
  `credits` int(11) NOT NULL DEFAULT '0' COMMENT '积分',		# 预留，供二次开发扩展
  `golds` int(11) NOT NULL DEFAULT '0' COMMENT '金币',		# 预留，虚拟币
  `rmbs` int(11) NOT NULL DEFAULT '0' COMMENT '人民币',		# 预留，人民币
  `create_ip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时IP',
  `create_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '创建时间',
  `login_ip` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时IP',
  `login_date` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录时间',
  `logins` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '登录次数',
  `avatar` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '用户最后更新图像时间',
  PRIMARY KEY (`uid`),
  UNIQUE KEY (`username`),
  UNIQUE KEY (`email`), # 升级的时候可能为空
  KEY gid (gid)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;
INSERT INTO `wellcms_user` SET uid=1, gid=1, email='admin@admin.com', username='admin',`password`='d98bb50e808918dd45a8d92feafc4fa3',salt='123456';

# 用户组
DROP TABLE IF EXISTS `wellcms_group`;
CREATE TABLE `wellcms_group` (
  `gid` smallint(6) unsigned NOT NULL,			#
  `name` char(20) NOT NULL default '',			# 用户组名称
  `creditsfrom` int(11) NOT NULL default '0',	# 积分从
  `creditsto` int(11) NOT NULL default '0',		# 积分到
  `allowread` tinyint(1) NOT NULL default '0',		# 允许访问
  `allowthread` tinyint(1) NOT NULL default '0',  # 允许发主题
  `allowpost` tinyint(1) NOT NULL default '0',		# 允许回帖
  `allowattach` tinyint(1) NOT NULL default '0',  # 允许上传文件
  `allowdown` tinyint(1) NOT NULL default '0',		# 允许下载文件
  `allowtop` tinyint(1) NOT NULL default '0',     # 允许置顶
  `allowupdate` tinyint(1) NOT NULL default '0',  # 允许编辑
  `allowdelete` tinyint(1) NOT NULL default '0',  # 允许删除
  `allowmove` tinyint(1) NOT NULL default '0',		# 允许移动
  `allowbanuser` tinyint(1) NOT NULL default '0',		# 允许禁止用户
  `allowdeleteuser` tinyint(1) NOT NULL default '0',# 允许删除用户
  `allowviewip` tinyint(1) NOT NULL default '0',	# 允许查看用户敏感信息
  # hook 增加数据
  #`allowintoadmin` tinyint(1) NOT NULL default '0',    # 允许进后台
  #`allowmanageforum` tinyint(1) NOT NULL default '0',	# 允许管理版块
  #`allowmanagesetting` tinyint(1) NOT NULL default '0',# 允许管理设置
  #`allowmanageplugin` tinyint(1) NOT NULL default '0',	# 允许管理插件
  #`allowmanageorder` tinyint(1) NOT NULL default '0',	# 允许管理订单
  #`allowagent` tinyint(1) NOT NULL default '0',  # 允许代理
  #`allowsell` tinyint(1) NOT NULL default '0',   # 允许分销
  #`allowmerchant` tinyint(1) NOT NULL default '0',	# 允许成为商户
  #`allowdelivery` tinyint(1) NOT NULL default '0',	# 允许发货
  #`allowverify` tinyint(1) NOT NULL default '0',	# 允许审核验证
  #`allowcontribute` tinyint(1) NOT NULL default '0',	# 允许投稿
  # 信息message 通知notice 活动activity
  PRIMARY KEY (gid)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `wellcms_group` SET gid='0', name="游客组", creditsfrom='0', creditsto='0', allowread='1', allowthread='0', allowpost='1', allowattach='0', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';

INSERT INTO `wellcms_group` SET gid='1', name="管理员组", creditsfrom='0', creditsto='0', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='1', allowupdate='1', allowdelete='1', allowmove='1', allowbanuser='1', allowdeleteuser='1', allowviewip='1';
INSERT INTO `wellcms_group` SET gid='2', name="超级版主组", creditsfrom='0', creditsto='0', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='1', allowupdate='1', allowdelete='1', allowmove='1', allowbanuser='1', allowdeleteuser='1', allowviewip='1';
INSERT INTO `wellcms_group` SET gid='4', name="版主组", creditsfrom='0', creditsto='0', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='1', allowupdate='1', allowdelete='1', allowmove='1', allowbanuser='1', allowdeleteuser='0', allowviewip='1';
INSERT INTO `wellcms_group` SET gid='5', name="实习版主组", creditsfrom='0', creditsto='0', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='1', allowupdate='1', allowdelete='0', allowmove='1', allowbanuser='0', allowdeleteuser='0', allowviewip='0';

INSERT INTO `wellcms_group` SET gid='6', name="待验证用户组", creditsfrom='0', creditsto='0', allowread='1', allowthread='0', allowpost='1', allowattach='0', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';
INSERT INTO `wellcms_group` SET gid='7', name="禁止用户组", creditsfrom='0', creditsto='0', allowread='0', allowthread='0', allowpost='0', allowattach='0', allowdown='0', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';

INSERT INTO `wellcms_group` SET gid='101', name="一级用户组", creditsfrom='0', creditsto='50', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';
INSERT INTO `wellcms_group` SET gid='102', name="二级用户组", creditsfrom='50', creditsto='200', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';
INSERT INTO `wellcms_group` SET gid='103', name="三级用户组", creditsfrom='200', creditsto='1000', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';
INSERT INTO `wellcms_group` SET gid='104', name="四级用户组", creditsfrom='1000', creditsto='10000', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';
INSERT INTO `wellcms_group` SET gid='105', name="五级用户组", creditsfrom='10000', creditsto='10000000', allowread='1', allowthread='1', allowpost='1', allowattach='1', allowdown='1', allowtop='0', allowupdate='0', allowdelete='0', allowmove='0', allowbanuser='0', allowdeleteuser='0', allowviewip='0';

# session 表
# 缓存到 runtime 表。 online_0 全局 online_fid 版块。提高遍历效率。
DROP TABLE IF EXISTS `wellcms_session`;
CREATE TABLE `wellcms_session` (
  `sid` char(32) NOT NULL default '0',  # 随机生成 id 不能重复 uniqueid() 13 位
  `uid` int(11) unsigned NOT NULL default '0',  # 用户id 未登录为 0，可以重复
  `fid` tinyint(3) unsigned NOT NULL default '0', # 所在的版块
  `url` char(32) NOT NULL default '', # 当前访问 url
  `ip` int(11) unsigned NOT NULL default '0',		# 用户ip
  `useragent` char(128) NOT NULL default '',		# 用户浏览器信息
  `data` char(255) NOT NULL default '', # session 数据，超大数据存入大表。
  `bigdata` tinyint(1) NOT NULL default '0',  # 是否有大数据。
  `last_date` int(11) unsigned NOT NULL default '0',  # 上次活动时间
  PRIMARY KEY (`sid`),
  KEY ip (`ip`),
  KEY fid (`fid`),
  KEY uid_last_date (`uid`, `last_date`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_session_data`;
CREATE TABLE `wellcms_session_data` (
  `sid` char(32) NOT NULL default '0',			#
  `last_date` int(11) unsigned NOT NULL default '0',	# 上次活动时间
  `data` text NOT NULL,					# 存超大数据
  PRIMARY KEY (`sid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

# 持久的 key value 数据存储, ttserver, mysql
DROP TABLE IF EXISTS `wellcms_kv`;
CREATE TABLE `wellcms_kv` (
  `k` char(32) NOT NULL default '',
  `v` mediumtext NOT NULL,
  `expiry` int(11) unsigned NOT NULL default '0',		# 过期时间
  PRIMARY KEY(`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `wellcms_kv` (`k`, `v`, `expiry`) VALUES
('setting', '{\n    "well_website_conf": {\n        "name": "WellCMS X",\n        "version": "1.0.0",\n        "installed": 0,\n        "setting": {\n            "website_mode": 2,\n            "tpl_mode": 0,\n            "map": ""\n        },\n        "picture_size": {\n            "width": 170,\n            "height": 113\n        }\n    }\n}', 0);

# 缓存表，用来保存临时数据。
DROP TABLE IF EXISTS `wellcms_cache`;
CREATE TABLE `wellcms_cache` (
  `k` char(32) NOT NULL default '',
  `v` mediumtext NOT NULL,
  `expiry` int(11) unsigned NOT NULL default '0',		# 过期时间
  PRIMARY KEY(`k`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_forum`;
CREATE TABLE `wellcms_forum` (
  `fid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(16) NOT NULL DEFAULT '',
  `rank` tinyint(3) NOT NULL DEFAULT '0',
  `threads` int(11) NOT NULL DEFAULT '0',
  `todayposts` int(11) NOT NULL DEFAULT '0',
  `todaythreads` int(11) NOT NULL DEFAULT '0',
  `brief` text NOT NULL,
  `announcement` text NOT NULL,
  `accesson` int(11) NOT NULL DEFAULT '0',
  `orderby` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  `moduids` char(120) NOT NULL DEFAULT '',
  `seo_title` char(64) NOT NULL DEFAULT '',
  `seo_keywords` char(64) NOT NULL DEFAULT '',
  `seo_description` char(120) NOT NULL DEFAULT '',
  `well_nav_display` tinyint(1) NOT NULL DEFAULT '0',
  `well_display` tinyint(1) NOT NULL DEFAULT '0',
  `well_news` int(11) NOT NULL DEFAULT '0',
  `well_list_news` int(11) NOT NULL DEFAULT '0',
  `well_channel_news` int(11) NOT NULL DEFAULT '0',
  `well_comment` tinyint(1) NOT NULL DEFAULT '0',
  `well_fup` int(11) NOT NULL DEFAULT '0',
  `well_son` int(11) NOT NULL DEFAULT '0',
  `well_type` tinyint(1) NOT NULL DEFAULT '1',
  `well_model` tinyint(1) NOT NULL DEFAULT '0',
  `well_forum_type` tinyint(1) NOT NULL DEFAULT '0',
  `well_tpl` tinyint(1) NOT NULL DEFAULT '0',
  `well_tpl_cate` char(40) NOT NULL DEFAULT '',
  `well_tpl_read` char(40) NOT NULL DEFAULT '',
  `well_pagesize` int(11) NOT NULL DEFAULT '20',
  `well_flag` char(40) NOT NULL DEFAULT '',
  `well_picture_size` char(32) NOT NULL DEFAULT '',
  `well_thread_rank` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
INSERT INTO `wellcms_forum` (`fid`, `name`, `rank`, `threads`, `todayposts`, `todaythreads`, `brief`, `announcement`, `accesson`, `orderby`, `create_date`, `icon`, `moduids`, `seo_title`, `seo_keywords`, `seo_description`, `well_nav_display`, `well_display`, `well_news`, `well_list_news`, `well_channel_news`, `well_comment`, `well_fup`, `well_son`, `well_type`, `well_model`, `well_forum_type`, `well_tpl`, `well_tpl_cate`, `well_tpl_read`, `well_pagesize`, `well_flag`, `well_picture_size`, `well_thread_rank`) VALUES
(1, '默认版块', 0, 0, 0, 0, '默认版块介绍', '', 0, 0, 0, 0, '', '', '', '', 1, 1, 10, 0, 0, 0, 0, 0, 1, 0, 0, 0, '', '', 20, '', '{"width":170,"height":113}', 0);

DROP TABLE IF EXISTS `wellcms_forum_access`;
CREATE TABLE `wellcms_forum_access` (
  `fid` int(11) unsigned NOT NULL DEFAULT '0',
  `gid` int(11) unsigned NOT NULL DEFAULT '0',
  `allowread` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowthread` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowpost` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowattach` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `allowdown` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_attach`;
CREATE TABLE `wellcms_website_attach` (
  `aid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `filesize` int(8) NOT NULL DEFAULT '0',
  `width` mediumint(8) NOT NULL DEFAULT '0',
  `height` mediumint(8) NOT NULL DEFAULT '0',
  `filename` char(120) NOT NULL DEFAULT '',
  `orgfilename` char(120) NOT NULL DEFAULT '',
  `filetype` char(7) NOT NULL DEFAULT '',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `comment` char(100) NOT NULL DEFAULT '',
  `downloads` int(11) NOT NULL DEFAULT '0',
  `credits` int(11) NOT NULL DEFAULT '0',
  `golds` int(11) NOT NULL DEFAULT '0',
  `rmbs` int(11) NOT NULL DEFAULT '0',
  `isimage` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`aid`),
  KEY `tid` (`tid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_data`;
CREATE TABLE `wellcms_website_data` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `doctype` tinyint(1) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_flag`;
CREATE TABLE `wellcms_website_flag` (
  `flagid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(24) NOT NULL DEFAULT '',
  `fid` int(11) NOT NULL DEFAULT '0',
  `rank` smallint(6) NOT NULL DEFAULT '0',
  `number` int(11) NOT NULL DEFAULT '0',
  `count` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  `display` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`flagid`),
  KEY `name` (`name`,`fid`),
  KEY `fid` (`fid`,`display`,`flagid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_flag_thread`;
CREATE TABLE `wellcms_website_flag_thread` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `flagid` int(11) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `tid` (`tid`),
  KEY `flagid` (`flagid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_modelog`;
CREATE TABLE `wellcms_website_modelog` (
  `logid` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `pid` int(11) NOT NULL DEFAULT '0',
  `subject` char(32) NOT NULL DEFAULT '',
  `comment` char(64) NOT NULL DEFAULT '',
  `total` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`logid`),
  KEY `uid` (`uid`,`logid`),
  KEY `tid` (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_post`;
CREATE TABLE `wellcms_website_post` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `userip` int(11) NOT NULL DEFAULT '0',
  `doctype` tinyint(3) NOT NULL DEFAULT '0',
  `quotepid` int(11) NOT NULL DEFAULT '0',
  `message` longtext NOT NULL,
  PRIMARY KEY (`pid`),
  KEY `uid` (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_post_pid`;
CREATE TABLE `wellcms_website_post_pid` (
  `pid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`,`pid`),
  KEY `uid` (`uid`,`pid`),
  KEY `fid` (`fid`,`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_thread`;
CREATE TABLE `wellcms_website_thread` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `fid` int(11) NOT NULL DEFAULT '0',
  `subject` varchar(128) NOT NULL DEFAULT '',
  `type` tinyint(2) NOT NULL DEFAULT '0',
  `top` tinyint(1) NOT NULL DEFAULT '0',
  `link` tinyint(1) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  `tag` varchar(94) NOT NULL DEFAULT '',
  `userip` int(11) NOT NULL DEFAULT '0',
  `create_date` int(11) NOT NULL DEFAULT '0',
  `views` int(11) NOT NULL DEFAULT '0',
  `posts` int(11) NOT NULL DEFAULT '0',
  `images` tinyint(3) NOT NULL DEFAULT '0',
  `files` tinyint(3) NOT NULL DEFAULT '0',
  `modes` tinyint(3) NOT NULL DEFAULT '0',
  `status` tinyint(2) NOT NULL DEFAULT '0',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `lastuid` int(11) NOT NULL DEFAULT '0',
  `last_date` int(11) NOT NULL DEFAULT '0',
  `brief` varchar(120) NOT NULL DEFAULT '',
  `keyword` varchar(64) NOT NULL DEFAULT '',
  `description` varchar(120) NOT NULL DEFAULT '',
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_thread_tid`;
CREATE TABLE `wellcms_website_thread_tid` (
  `tid` int(11) NOT NULL AUTO_INCREMENT,
  `rank` smallint(6) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `verify_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `fid_tid` (`fid`,`tid`),
  KEY `fid_rank` (`fid`,`rank`),
  KEY `uid_tid` (`uid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_thread_top`;
CREATE TABLE `wellcms_website_thread_top` (
  `fid` int(11) NOT NULL DEFAULT '0',
  `top` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`),
  KEY `top` (`top`,`tid`),
  KEY `fid` (`fid`,`top`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_tag`;
CREATE TABLE `wellcms_website_tag` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(12) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT '0',
  `icon` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

DROP TABLE IF EXISTS `wellcms_website_tag_thread`;
CREATE TABLE `wellcms_website_tag_thread` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `tid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tagid`,`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;