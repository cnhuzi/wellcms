<?php
/*
	WellCMS 1.0 配置文件
	支持多台 DB，主从配置好以后，程序自动根据 SQL 读写分离。
	支持各种 cache，本机 apc/xcache, 网络: redis/memcached/mysql
	支持 CDN，如果前端开启了 CDN 请设置 cdn_on=>1, 否则获取 IP 会不准确 
	支持临时目录设置，独立 Linux 主机，可以设置为 /dev/shm 通过内存加速
*/
return array (
	'db' => array (
		'type' => 'mysql',	
		'mysql' => array (
			'master' => array (
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'root',
				'name' => 'test',
				'tablepre' => 'well_',
				'charset' => 'utf8',
				'engine' => 'innodb',
			),
			'slaves' => array (),
		),
		'pdo_mysql' => array (
			'master' => array (
				'host' => 'localhost',
				'user' => 'root',
				'password' => 'root',
				'name' => 'test',
				'tablepre' => 'well_',
				'charset' => 'utf8',
				'engine' => 'innodb',
			),
			'slaves' => array (),
		),
	),
	'cache' => array (
		'enable' => true,
		'type' => 'mysql',
		'memcached' => array (
			'host' => 'localhost',
			'port' => '11211',
			'cachepre' => 'well_',
		),
		'redis' => array (
			'host' => 'localhost',
			'port' => '6379',
			'cachepre' => 'well_',
		),
		'xcache' => array (
			'cachepre' => 'well_',
		),
		'yac' => array (
			'cachepre' => 'well_',
		),
		'apc' => array (
			'cachepre' => 'well_',
		),
		'mysql' => array (
			'cachepre' => 'well_',
		),
	),
	'tmp_path' => './tmp/',		// 可以配置为 linux 下的 /dev/shm ，通过内存缓存临时文件
	'log_path' => './log/',		// 日志目录
	
	// -------------------> 配置

	'view_url' => 'view/',		// 可以配置单独的 CDN 域名：比如：http://static.domain.com/view/
	'upload_url' => 'upload/',	// 可以配置单独的 CDN 域名：比如：http://upload.domain.com/upload/
	'upload_path' => './upload/',	// 物理路径，可以用 NFS 存入到单独的文件服务器
	
	'logo_mobile_url' => 'view/img/logo.png',   // 手机的 LOGO URL
	'logo_pc_url' => 'view/img/logo.png',   // PC 的 LOGO URL
	'logo_water_url' => 'view/img/water-small.png', // 水印的 LOGO URL
	
	'sitename' => 'WellCMS',
	'sitebrief' => 'Site Brief',
	'timezone' => 'Asia/Shanghai',  // 时区，默认中国
	'lang' => 'zh-cn',
	'runlevel' => 5,    // 0: 站点关闭; 1: 管理员可读写; 2: 会员可读;  3: 会员可读写; 4：所有人只读; 5: 所有人可读写
	'runlevel_reason' => 'The site is under maintenance, please visit later.',

	'cookie_pre' => 'well_', // cookie 前缀
	'cookie_domain' => '',
	'cookie_path' => '',
	'auth_key' => 'efdkjfjiiiwurjdmclsldow753jsdj438',
	
	'pagesize' => 20,
	'postlist_pagesize' => 100,
	'cache_thread_list_pages' => 10,
	'online_update_span' => 120,    // 在线更新频度，大站设置的长一些
	'online_hold_time' => 3600, // 在线的时间
	'session_delay_update' => 0,
	'upload_image_width' => 927,	// 上传图片自动缩略的最大宽度
	'order_default' => 'lastpid',   // BBS帖子排序
	'attach_dir_save_rule' => 'Ym',	// 附件存放规则，附件多用：Ymd，附件少：Ym
	
	'update_views_on' => 1,
	'user_create_email_on' => 0,
	'user_create_on' => 1,
	'user_resetpw_on' => 0,
	
	'admin_bind_ip' => 0,		// 后台是否绑定 IP
	
	'cdn_on' => 0,
	
	/* 支持多种 URL 格式：
		0: ?thread-create-1.htm
		1: thread-create-1.htm
		2: ?/thread/create/1  不支持
		3: /thread/create/1   不支持
	*/
	'url_rewrite_on' => 0,
	
	// 禁止插件
	'disabled_plugin' => 0, 
	  
	'version' => '1.0.2',
	'static_version' => '?1.0',
	'installed' => 0,
	'compress' => 1, // 代码压缩 0关闭 1仅压缩php、html代码(不压缩js代码) 2压缩全部代码 如果启用压缩出现错误，请关闭，删除html中的所有注释，并且js代码按照英文分号结束的地方加上分号;
);
?>