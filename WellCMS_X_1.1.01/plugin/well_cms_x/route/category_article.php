<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 扩展时可hook也可overwrite
*/
!defined('DEBUG') AND exit('Access Denied.');
// 频道下显示的属性主体 栏目和主题 频道置顶
// hook website_category_article_start.php

// 加载默认的数据和模板
$category_article_default = 1;

// hook website_category_article_default_before.php

if ($category_article_default == 1) {

    // 从默认的地方读取主题列表
    $thread_list_from_default = 1;

    // hook website_category_article_mode_before.php

    if ($website_mode == 0 ||$website_mode == 1) {
        // 门户模式 portal

        // hook website_category_article_portal_start.php

        /*
         *
         * $arrlist[list]对应每个需要删除的栏目;
         * // 栏目按照rank被重新排序所以键已经不是fid
         * $arrlist[list][0]['name']栏目名;
         * $arrlist[list][0]['url']栏目路径;
         * $arrlist[list][0]['news']栏目下显示的主题二维数组;
         * $arrlist[flag]首页需要显示的主题二维数组;
         * $arrlist[top]首页置顶需要显示的主题二维数组;
         * */
        $arrlist = well_website_channel_thread($fid);

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

        $first_flag = isset($arrlist['flagarr']) ? reset($arrlist['flagarr']) : '';

        // hook website_category_article_portal_end.php

    } elseif ($website_mode == 2) {
        // 扁平模式

        // hook website_category_article_flat_start.php

        $page = param(2, 1);
        $pagesize = 20;

        // hook website_category_article_flat_after.php

        $threadlist = array();
        if ($thread_list_from_default) {
            $category_forumlist = well_category_show($fid);
            $fids = arrlist_values($category_forumlist, 'fid');
            $threads = arrlist_sum($category_forumlist, 'threads');
            $pagination = pagination(well_nav_url($fid, TRUE, $extra), $threads, $page, $pagesize);

            // hook index_flat_thread_find_by_fids_before.php

            $threadlist = well_thread_find_by_fids($fids, $page, $pagesize, $threads);

        }

        // hook website_category_article_flat_center.php

        // 查找置顶
        if ($page == 1) {
            $toplist1 = well_thread_top_find(0);
            $toplist2 = well_thread_top_find($fid, 2);
            $threadlist = (array)$toplist1 + (array)$toplist2 + (array)$threadlist;
        }

        // hook website_category_article_flat_after.php

        // 过滤没有权限访问的主题 / filter no permission thread
        well_thread_list_access_filter($threadlist, $gid);

        // hook website_category_article_flat_access_filter_after.php

        $flaglist = well_website_flag_category_thread_cache($fid);

        // hook website_category_article_flat_website_flag_after.php

        $arrlist = array('threadlist' => $threadlist, 'flaglist' => $flaglist);

        // hook website_category_article_flat_end.php
    }

    // hook website_category_article_mode_after.php
}

// hook website_category_article_after.php

// SEO
$seo_title = $forum['seo_title'] ? $forum['seo_title'] : $forum['name'].'-'.$conf['sitename'];
$header['title'] = strip_tags($seo_title);
$header['mobile_title'] = strip_tags($forum['name']);
$header['mobile_link'] = well_nav_format($forum, $extra);
$seo_keywords = $forum['seo_keywords'] ? $forum['seo_keywords'] : $forum['name'];
$header['keywords'] = strip_tags($seo_keywords);
$header['description'] = strip_tags($forum['brief']);
$_SESSION['fid'] = $fid;
$active = 'default';

// hook website_category_article_end.php

if ($ajax) {
    //message(0, $arrlist);
} else {
    // hook website_category_template_htm.php
    // 这里可以直接,绑定栏目fid 直接hook比较暴力，根据需要自行区分各端
    /*if ($fid == 1) {
        include _include(APP_PATH . 'plugin/well_cms_x/view/htm/category_article.htm');
    exit;
    }*/
    $category_article_default == 1 AND include _include(well_website_template(2, $forum));
}

?>