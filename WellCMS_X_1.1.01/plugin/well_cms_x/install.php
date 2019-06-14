<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Forbidden');
include _include(APP_PATH . 'plugin/well_cms_x/model/well_db_check.func.php');

$tablepre = $db->tablepre;

if (well_db_find_table($db->tablepre . 'forum')) {

    # 栏目增加well_nav_display栏目是否显示在导航
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_nav_display` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # well_display 首页是否显示内容 栏目内容 首页是否显示内容 1显示
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_display` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 栏目内容 首页显示数量
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_news` int(11) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 设置为列表 列表最新数量
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_list_news` int(11) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 频道最新数量
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_channel_news` int(11) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 栏目增加well_comment评论开启 0关闭 1开启
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_comment` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_fup` int(11) NOT NULL DEFAULT '0' COMMENT '频道ID'";
    $r = db_exec($sql);

    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_son` int(11) NOT NULL DEFAULT '0' COMMENT '子栏目数'";
    $r = db_exec($sql);

    # 分类 0论坛 1cms
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_type` tinyint(1) NOT NULL DEFAULT '1'";
    $r = db_exec($sql);

    # 模型 0新闻
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_model` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 栏目属性 (0列表 1频道)
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_forum_type` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 模板
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_tpl` tinyint(1) NOT NULL DEFAULT '0' COMMENT '模板0默认 1自建'";
    $r = db_exec($sql);

    # 分类页模板
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_tpl_cate` char(40) NOT NULL DEFAULT ''";
    $r = db_exec($sql);

    # 内容页模板
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_tpl_read` char(40) NOT NULL DEFAULT ''";
    $r = db_exec($sql);

    # 列表分页数量
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_pagesize` int(11) NOT NULL DEFAULT '20'";
    $r = db_exec($sql);

    # 栏目下需要显示在列表和内页的属性
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_flag` char(40) NOT NULL DEFAULT ''";
    $r = db_exec($sql);

    # 上传图片像素
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_picture_size` char(32) NOT NULL DEFAULT ''";
    $r = db_exec($sql);

    # 模板0默认 1自建
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_tpl` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

    # 自定义排序主题
    $sql = "ALTER TABLE `{$tablepre}forum` ADD COLUMN `well_thread_rank` tinyint(1) NOT NULL DEFAULT '0'";
    $r = db_exec($sql);

} else {

# 板块表，一级, runtime 中存放 forumlist 格式化以后的数据。
    $sql = "CREATE TABLE `{$tablepre}forum` (
  `fid` int(11) unsigned NOT NULL auto_increment, # fid
  `name` char(16) NOT NULL default '',			# 栏目名称
  `rank` tinyint(3) NOT NULL default '0',	# 显示，倒序，数字越大越靠前
  `threads` int(11) NOT NULL default '0',	# 主题数
  `todayposts` int(11) NOT NULL default '0',# 今日发帖，计划任务每日凌晨0点清空为0
  `todaythreads` int(11) NOT NULL default '0',# 今日发主题，计划任务每日凌晨０点清空为０
  `brief` text NOT NULL,					# 栏目简介 允许HTML
  `announcement` text NOT NULL,				# 栏目公告 允许HTML
  `accesson` int(11) NOT NULL default '0',	# 是否开启权限控制
  `orderby` tinyint(1) NOT NULL default '0',  # 默认列表排序，0: 顶贴时间 last_date， 1: 发帖时间 tid
  `create_date` int(11) NOT NULL default '0',	# 板块创建时间
  `icon` int(11) NOT NULL default '0',		# 板块是否有 icon，存放最后更新时间
  `moduids` char(120) NOT NULL default '',		# 每个栏目有多个版主，最多10个： 10*12 = 120，删除用户的时候，如果是版主，则调整后再删除。逗号分隔
  `seo_title` char(64) NOT NULL default '',		# SEO 标题，如果设置会代替栏目名称
  `seo_keywords` char(64) NOT NULL default '',  # SEO keyword
  `well_nav_display` tinyint(1) NOT NULL DEFAULT '0', # 栏目是否显示在导航 1显示
  `well_display` tinyint(1) NOT NULL DEFAULT '0', # 首页是否显示内容 栏目内容 首页是否显示内容 1显示
  `well_news` int(11) NOT NULL DEFAULT '0', # 栏目内容 首页显示数量
  `well_list_news` int(11) NOT NULL DEFAULT '0', # 设置为列表 列表最新数量
  `well_channel_news` int(11) NOT NULL DEFAULT '0', # 频道最新数量
  `well_comment` tinyint(1) NOT NULL DEFAULT '0', # 评论开启 0关闭 1开启
  `well_fup` int(11) NOT NULL DEFAULT '0', # 上级栏目
  `well_son` int(11) NOT NULL DEFAULT '0', # 子栏目数量
  `well_type` tinyint(1) NOT NULL DEFAULT '1', # 分类 0论坛 1cms
  `well_model` tinyint(1) NOT NULL DEFAULT '0', # 模型 0新闻
  `well_forum_type` tinyint(1) NOT NULL DEFAULT '0', # 栏目属性 (0列表 1频道)
  `well_tpl` tinyint(1) NOT NULL DEFAULT '0', # 模板0默认 1自建
  `well_tpl_cate` char(40) NOT NULL DEFAULT '', # 分类页模板
  `well_tpl_read` char(40) NOT NULL DEFAULT '', # 内容页模板
  `well_pagesize` int(11) NOT NULL DEFAULT '20', # 列表分页数量
  `well_flag` char(40) NOT NULL DEFAULT '', # 显示在列表和内页的属性
  `well_picture_size` char(32) NOT NULL DEFAULT '', # 上传图片像素
  `well_thread_rank` tinyint(1) NOT NULL DEFAULT '0', # 自定义排序主题
  PRIMARY KEY (`fid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);

    $sql = "INSERT INTO `{$tablepre}forum` (`name`, `rank`, `threads`, `todayposts`, `todaythreads`, `brief`, `announcement`, `accesson`, `orderby`, `create_date`, `icon`, `moduids`, `seo_title`, `seo_keywords`, `seo_description`, `well_nav_display`, `well_display`, `well_news`, `well_list_news`, `well_channel_news`, `well_comment`, `well_fup`, `well_son`, `well_type`, `well_model`, `well_forum_type`, `well_tpl`, `well_tpl_cate`, `well_tpl_read`, `well_pagesize`, `well_flag`, `well_picture_size`, `well_thread_rank`) VALUES
    ('默认栏目', 1, 0, 0, 0, '默认栏目介绍', '', 0, 0, 0, 0, '', '', '', '', 1, 1, 10, 0, 0, 0, 0, 0, 1, 0, 0, 0, '', '', 20, '', '{\"width\":170,\"height\":113}', 0)";
    $r = db_exec($sql);

    # 栏目访问规则, forum.accesson 开启时生效, 记录行数： fid * gid
    $sql = "CREATE TABLE `{$tablepre}forum_access` (
  `fid` int(11) unsigned NOT NULL default '0',		# fid
  `gid` int(11) unsigned NOT NULL default '0',		# fid
  `allowread` tinyint(1) unsigned NOT NULL default '0', # 允许查看
  `allowthread` tinyint(1) unsigned NOT NULL default '0',	# 允许发主题
  `allowpost` tinyint(1) unsigned NOT NULL default '0',	# 允许回复
  `allowattach` tinyint(1) unsigned NOT NULL default '0',	# 允许上传附件
  `allowdown` tinyint(1) unsigned NOT NULL default '0',	# 允许下载附件
  PRIMARY KEY (`fid`,`gid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
    $r = db_exec($sql);

}

# 主题大表
$sql = "CREATE TABLE `{$tablepre}website_thread` (
  `tid` int(11) NOT NULL auto_increment,  # 主题id
  `fid` int(11) NOT NULL DEFAULT '0',     # 栏目
  `subject` char(80) NOT NULL DEFAULT '', # 主题
  `type` tinyint(2) NOT NULL DEFAULT '0', # 主题类型:0文章
  `top` tinyint(1) NOT NULL DEFAULT '0',  # 置顶: 0:普通,1-3置顶顺序
  `link` tinyint(1) NOT NULL DEFAULT '0', # 站外链接
  `uid` int(11) NOT NULL DEFAULT '0',     # 用户uid
  `icon` int(11) NOT NULL DEFAULT '0',    # 主图 图片名tid
  #`author_id` int(11) NOT NULL DEFAULT '0', # 作者ID
  #`source_id` int(11) NOT NULL DEFAULT '0', # 来源ID
  `tag` char(94) NOT NULL DEFAULT '',      # 标签 json {tgaid:name}
  `userip` int(11) NOT NULL DEFAULT '0',  # 发表ip ip2long()用来清理
  `create_date` int(11) NOT NULL DEFAULT '0', # 发表时间
  `views` int(11) NOT NULL DEFAULT '0',		# 查看次数 可剥离出去做单独的服务，避免 cache 失效
  `posts` int(11) NOT NULL DEFAULT '0',		# 回复数
  `images` tinyint(3) NOT NULL DEFAULT '0', # 附件中包含的图片数
  `files` tinyint(3) NOT NULL DEFAULT '0',  # 附件中包含的文件数
  `modes` tinyint(3) NOT NULL DEFAULT '0',  # 操作次数，如果 > 0 则查询 modelog，显示评分
  `status` tinyint(2) NOT NULL DEFAULT '0', # 0:通过 1~9审核:1待审核 10~19:10退稿 11逻辑删除
  `closed` tinyint(1) NOT NULL DEFAULT '0',	# 1关闭回复 2关闭主题不能回复、编辑
  `lastuid` int(11) NOT NULL DEFAULT '0',	# 最近参与的 uid
  `last_date` int(11) NOT NULL DEFAULT '0', # 最后回复时间
  `brief` char(120) NOT NULL DEFAULT '',  # 简介
  `keyword` char(64) NOT NULL DEFAULT '', # SEO keyword
  `description` char(120) NOT NULL DEFAULT '',  # SEO description 外链接写这里 直接跳出去了
  PRIMARY KEY (`tid`)  # 主键
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 主题数据 大表
$sql = "CREATE TABLE `{$tablepre}website_data` (
  `tid` int(11) NOT NULL auto_increment,  # 主题id
  `doctype` tinyint(1) NOT NULL DEFAULT '0',  # 类型，0:html, 1:txt 2:markdown 3:ubb
  `message` longtext NOT NULL,  # 内容，用户的原始数据
  PRIMARY KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 小表优化主题 未通过审核和逻辑删除的主题 不加入此表
$sql = "CREATE TABLE `{$tablepre}website_thread_tid` (
  `tid` int(11) NOT NULL auto_increment,  # 主题id
  `rank` smallint(6) NOT NULL DEFAULT '0',  # 排序最大值排在最前面
  `fid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  `verify_date` int(11) NOT NULL DEFAULT '0', # 审核通过时间
  PRIMARY KEY (`tid`),  # 编辑更新 删除 使用
  KEY `fid_tid` (`fid`,`tid`),  # 栏目主题排序
  KEY `fid_rank` (`fid`,`rank`),  # 栏目主题自定义排序
  KEY `uid_tid` (`uid`,`tid`)  # 用户主题 排序
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 主题回复数据 评论回复多的话是大表
$sql = "CREATE TABLE `{$tablepre}website_post` (
  `pid` int(11) NOT NULL auto_increment, # 回复id
  `fid` int(11) NOT NULL DEFAULT '0',    # 栏目
  `tid` int(11) NOT NULL DEFAULT '0',    # 主题id
  `uid` int(11) NOT NULL DEFAULT '0',    # 用户id
  `status` tinyint(2) NOT NULL DEFAULT '0', # 0:通过 1~9审核:1待审核 10~19:10退稿 11逻辑删除
  `create_date` int(11) NOT NULL DEFAULT '0', # 回复时间
  `userip` int(11) NOT NULL DEFAULT '0',      # 发帖ip ip2long()
  `doctype` tinyint(3) NOT NULL DEFAULT '0',  # 类型，0: html, 1: txt 2: markdown 3: ubb
  #`replys` longtext NOT NULL,   # 楼中楼回复json存放
  `quotepid` int(11) NOT NULL default '0',  # 引用哪个 pid，可能不存在
  `message` longtext NOT NULL,  # 内容，用户的原始数据
  PRIMARY KEY (`pid`),
  KEY (`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 小表优化评论 未通过审核回复 不加入此表
$sql = "CREATE TABLE `{$tablepre}website_post_pid` (
  `pid` int(11) NOT NULL auto_increment, # 回复id
  `fid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `uid` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`tid`,`pid`),  # 主题回复 主题 排序
  KEY (`uid`,`pid`), # 我的回复，清理数据需要
  KEY (`fid`,`pid`) # 栏目下的回复
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# CMS附件表 绑定BBS附件表aid
$sql = "CREATE TABLE `{$tablepre}website_attach` (
  `aid` int(11) NOT NULL auto_increment,  # 附件id
  `tid` int(11) NOT NULL DEFAULT '0',     # 主题id
  `uid` int(11) NOT NULL DEFAULT '0',     # 用户id
  `filesize` int(8) NOT NULL DEFAULT '0', # 文件尺寸，单位字节
  `width` mediumint(8) NOT NULL DEFAULT '0', # width > 0 则为图片
  `height` mediumint(8) NOT NULL DEFAULT '0',# height
  `filename` char(120) NOT NULL DEFAULT '',		# 文件名称，会过滤，并且截断，保存后的文件名，不包含URL前缀 upload_url
  `orgfilename` char(120) NOT NULL DEFAULT '',  # 上传的原文件名
  `filetype` char(7) NOT NULL DEFAULT '',       # 文件类型: image/txt/zip，小图标显示 <\i class=\"icon filetype image\"><\/i>
  `create_date` int(11) NOT NULL DEFAULT '0',	# 文件上传时间 UNIX 时间戳
  `comment` char(100) NOT NULL DEFAULT '',  # 文件注释 方便于搜索
  `downloads` int(11) NOT NULL DEFAULT '0', # 下载次数，预留
  `credits` int(11) NOT NULL DEFAULT '0',   # 需要的积分，预留
  `golds` int(11) NOT NULL DEFAULT '0',     # 需要的金币，预留
  `rmbs` int(11) NOT NULL DEFAULT '0',      # 需要的人民币，预留
  `isimage` tinyint(1) NOT NULL DEFAULT '0',# 是否为图片
  PRIMARY KEY (`aid`),  # aid
  KEY `tid` (`tid`),    # 主题内容下多个附件
  KEY `uid` (`uid`)     # 我的附件，清理数据需要
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 全站所有置顶 1栏目 2频道 3全局
$sql = "CREATE TABLE `{$tablepre}website_thread_top` (
  `fid` int(11) NOT NULL DEFAULT '0', # 查找栏目置顶
  `top` int(11) NOT NULL DEFAULT '0', # top:0 普通最新，> 0 置顶
  `tid` int(11) NOT NULL DEFAULT '0', # 主题id
  PRIMARY KEY (`tid`),
  KEY (`top`,`tid`),  # 最新top=0 order by tid desc / 全局置顶top=3
  KEY (`fid`,`top`)  # 栏目置顶 fid=1 and top=1
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# TODO 砍掉forum表需要显示的数量，统一在flag查询显示数量，只增加flag字串
# 自定义主题属性 在板块内增加需要显示的属性字符串,切割flagid,flagid
$sql = "CREATE TABLE `{$tablepre}website_flag` (
  `flagid` int(11) NOT NULL auto_increment,
  `name` char(24) NOT NULL DEFAULT '',
  `fid` int(11) NOT NULL DEFAULT '0',     # 0全站
  `rank` smallint(6) NOT NULL DEFAULT '0',  # 排序最大值排在最前面
  `number` int(11) NOT NULL DEFAULT '0',  # 需要显示的数量
  `count` int(11) NOT NULL DEFAULT '0',   # 属性下有多少主题
  `icon` int(11) NOT NULL DEFAULT '0',   # 图标
  `display` tinyint(1) NOT NULL DEFAULT '0', # 1显示
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`flagid`),
  KEY (`name`,`fid`),
  KEY (`fid`,`display`,`flagid`) # 栏目显示的属性 fid=0 AND display=1为首页
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 如果有父栏目如频道，在发布内容页面显示父栏目需要显示的属性
$sql = "CREATE TABLE `{$tablepre}website_flag_thread` (
  `id` int(11) NOT NULL auto_increment,
  `flagid` int(11) NOT NULL DEFAULT '0',
  `fid` int(11) NOT NULL DEFAULT '0',
  `tid` int(11) NOT NULL DEFAULT '0',
  `type` tinyint(1) NOT NULL DEFAULT '0', # 1首页 2频道 3栏目
  `create_date` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY (`tid`),
  KEY (`flagid`,`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

# 操作日志
$sql = "CREATE TABLE `{$tablepre}website_modelog` (
  `logid` int(11) NOT NULL auto_increment,	# logid
  `type` tinyint(2) NOT NULL DEFAULT '0',   # 1删除 2移动 3置顶 4取消置顶 5禁止回复 6关闭 7打开 8操作人民币 9操作金币 10操作积分 
  `uid` int(11) NOT NULL default '0',		# 版主 uid
  `tid` int(11) NOT NULL default '0',		# 主题id
  `pid` int(11) NOT NULL default '0',		# 回复id
  `subject` char(32) NOT NULL default '',	# 主题
  `comment` char(64) NOT NULL default '',	# 管理评价
  `total` int(11) NOT NULL default '0',		# 加减人民币 金币 积分
  `create_date` int(11) NOT NULL default '0', # 时间
  PRIMARY KEY (`logid`),
  KEY (`uid`, `logid`),
  KEY (`tid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8";
$r = db_exec($sql);

$sql = "CREATE TABLE `{$tablepre}website_tag` (
  `tagid` int(11) NOT NULL AUTO_INCREMENT,
  `name` char(12) NOT NULL DEFAULT '',
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

// 写入初始配置
$arr_conf = array(
    'name' => 'WellCMS X', // 程序名
    'version' => '1.0.0', // 版本
    'installed' => 0, // 安装演示数据
    'setting' => array(
        'website_mode' => 2, // 网站模式 0自定义 1门户 2扁平
        'tpl_mode' => 0, // 模板模式 0自适应 1PC和手机 2PC、平板和手机
        'map' => '', // 网站地图
    ),
    'picture_size' => array(
        'width' => 170,
        'height' => 113,
    ),
);
setting_set('well_website_conf', $arr_conf);

/*xn_copy('../view/img/logo.png', '../view/img/backup_logo.png');
xn_copy('../view/img/avatar.png', '../view/img/backup_avatar.png');
xn_copy('../view/img/water-small.png', '../view/img/backup_water-small.png');
xn_copy('../plugin/well_cms_x/view/image/logo.png', '../view/img/logo.png');
xn_copy('../plugin/well_cms_x/view/image/avatar.png', '../view/img/avatar.png');*/
xn_copy('../plugin/well_cms_x/view/image/nopic.png', '../view/img/nopic.png');
//xn_copy('../plugin/well_cms_x/view/image/water-small.png', '../view/img/water-small.png');
clearstatcache();
forum_list_cache_delete();

// 初始化
$r === false AND message(-1, '创建数据表结构失败');

?>