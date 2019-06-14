<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 扩展时可hook也可overwrite
*/
!defined('DEBUG') AND exit('Access Denied.');

// hook website_index_start.php

$arrlist = $extra = array(); // 插件预留

// 加载默认的数据和模板 直接hook比较暴力，但是需要自行区分各端，走默认well_website_template()调用也可以，已经区分好，根据需要自行决定
$index_default = 1;

// hook website_index_default_before.php

if ($index_default == 1) {

    $website_setting = $website_conf['setting'];
    // website_mode
    $website_mode = $website_setting['website_mode'];
    // tpl_mode
    $tpl_mode = $website_setting['tpl_mode'];

    // hook website_index_before.php

    // 从默认的地方读取主题列表
    $thread_list_from_default = 1;

    // hook website_index_mode_before.php

    if ($website_mode == 0) {
        // 自定义模式 custom 仅调用首页属性主题和置顶

        // hook website_index_custom_start.php

        /*
         * flagid对应首页显示的flagid 实例时填写对应数字
         * 属性名 $flaglist[flagid]['name'];
         * 属性链接 $flaglist[flagid]['url'];
         * 属性所有主题数组 $flaglist[flagid]['list'];
         * */
        $flaglist = well_website_flag_index_cache();

        // hook website_index_custom_before.php

        // 查找置顶
        $toplist = well_thread_top_find_cache();

        // hook website_index_custom_after.php

        $arrlist = array('flaglist' => $flaglist, 'toplist' => $toplist);

        // hook website_index_custom_end.php

    } elseif ($website_mode == 1) {
        // 门户模式 portal

        // hook website_index_portal_start.php

        /*
         * $arrlist['list']对应每个需要显示的栏目;
         * // 栏目按照rank排序，调用时单独栏目可直接$arrlist['list'][fid值]
         * $arrlist['list'][fid值]['name']栏目名;
         * $arrlist['list'][fid值]['url']栏目路径;
         * $arrlist['list'][fid值]['news']栏目下显示的主题二维数组;
         * $arrlist['flag']首页需要显示的全部主题二维数组;
         * $arrlist['flagarr'][flagid]对应flagid显示的主题二维数组;
         * $arrlist['top']首页置顶需要显示的主题二维数组;
         * */
        $arrlist = well_website_index_cate_thread_cache($forumlist);

        // 轮播凑整 双列排版 防止错版 单一列注释该代码
        $slide = array_value($arrlist, 'top');
        if ($slide) {
            if (count($arrlist['top']) % 2 != 0) {
                $i = 0;
                foreach ($arrlist['top'] as $key => &$_thread) {
                    $i++;
                    if ($i == 1) {
                        $slide[] = $_thread;
                    }
                }
            }
        }

        $first_flag = isset($arrlist['flagarr']) ? reset($arrlist['flagarr']) : array();

        // hook website_index_portal_end.php

    } elseif ($website_mode == 2) {
        // 扁平模式

        // hook website_index_flat_start.php

        $page = param(1, 1);
        $pagesize = 20;

        // hook website_index_flat_after.php

        if ($thread_list_from_default) {

            // hook website_index_flat_thread_find_start.php

            if (empty($forumlist_show)) {
                $threadlist = array();
                $threads = 0;
                // hook website_index_flat_thread_find_before.php
            } else {
                $fids = arrlist_values($forumlist_show, 'fid');
                $threads = arrlist_sum($forumlist_show, 'threads');

                // hook website_index_flat_thread_find_center.php

                $threadlist = well_thread_find_by_fids($fids, $page, $pagesize, $threads);

                // hook website_index_flat_thread_find_after.php
            }

            // hook website_index_flat_thread_find_end.php

            $pagination = pagination(url($route . '-{page}', $extra), $threads, $page, $pagesize);
        }

        // hook website_index_flat_center.php

        // 置顶
        if ($page == 1) {
            $toplist = well_thread_top_find_cache();
            $threadlist = isset($threadlist) ? ((array)$toplist + $threadlist) : array();
        }

        // hook website_index_flat_after.php

        // 过滤没有权限访问的主题 / filter no permission thread
        well_thread_list_access_filter($threadlist, $gid);

        // hook website_index_flat_access_filter_after.php

        $flaglist = well_website_flag_index_cache();

        // hook website_index_flat_website_flag_after.php

        $arrlist = array('threadlist' => $threadlist, 'flaglist' => $flaglist);

        // hook website_index_flat_end.php
    }

    // hook website_index_mode_after.php
}

// hook website_index_after.php

// SEO
$header['title'] = $conf['sitename'];
$header['keywords'] = $conf['sitename'];
$header['description'] = strip_tags($conf['sitebrief']);
$_SESSION['fid'] = 0;
$active = 'default';

// hook website_index_end.php

if ($ajax) {
    //message(0, $arrlist);
} else {
    // hook website_index_template_htm.php
    // 加载默认的数据和模板
    $index_default == 1 AND include _include(well_website_template(1));
}

?>