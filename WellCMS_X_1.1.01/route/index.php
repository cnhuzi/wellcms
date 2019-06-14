<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 扩展时可hook也可overwrite
*/
!defined('DEBUG') AND exit('Access Denied.');

// hook index_start.php

$page = param(1, 1);
$active = 'default';

// hook index_before.php

// 从默认的地方读取主题列表
$default_thread_list = 1;


// hook index_after.php

// SEO
$header['title'] = $conf['sitename'];
$header['keywords'] = '';
$header['description'] = $conf['sitebrief'];
$_SESSION['fid'] = 0;

// hook index_end.php

include _include(APP_PATH . 'view/htm/index.htm');

?>