<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
 
// hook model_website_template_start.php

// 加载模板 $type 1首页 2频道或列表 3内容页
function well_website_template($type, $forum = array())
{
    $website_conf = GLOBALS('website_conf');
    // 网站模式
    $website_mode = $website_conf['setting']['website_mode'];
    // 0自适应 1PC和手机 2PC、平板和手机
    $tpl_mode = $website_conf['setting']['tpl_mode'];

    // hook model_website_template_start.php

    !isset($tpl_mode) AND $tpl_mode = 0;

    // 用户自行开发模板路径 此处非hook形式，需上传模板到以下目录
    $template_path = APP_PATH . 'plugin/well_cms_x/view/template/';

    // 可hook模板路径
    // hook model_website_template_before.php

    // 0电脑 1微信 2手机浏览器 3pad
    $detect = well_detect_device();

    $template_page = $pre = '';
    if ($tpl_mode && $detect) {
        if ($tpl_mode == 2 && $detect == 3) {
            $pre = 'pad.';
        } else {
            $pre = 'm.';
        }
    }

    // hook model_website_template_after.php

    // 默认模板路径
    $default_template_path = APP_PATH . 'plugin/well_cms_x/view/htm/';

    if ($type == 1) {

        // hook model_website_template_index_start.php

        if ($website_mode == 0) {
            // 自由模式首页

            // hook model_website_template_index_freedom_before.php
            $index = $template_path . $pre . 'index.htm';
            $template_page = file_exists($index) ? $index : $default_template_path . 'index.htm';

        } elseif ($website_mode == 1) {
            // 门户模式首页

            // hook model_website_template_index_portal_before.php
            $index = $template_path . $pre . 'portal.htm';
            $template_page = file_exists($index) ? $index : $default_template_path . 'portal.htm';

        } elseif ($website_mode == 2) {
            // 扁平模式首页

            // hook model_website_template_index_flat_before.php
            $index = $template_path . $pre . 'index.htm';
            $template_page = file_exists($index) ? $index : $default_template_path . 'index.flat.htm';
        }

        // hook model_website_template_index_end.php

    } elseif ($type == 2) {
        // 频道&列表

        // hook model_website_template_category_start.php

        // 自定义的模板路径和文件
        $well_cate_tpl = $template_path . $pre . $forum['well_tpl_cate'];

        // hook model_website_template_category_before.php

        if ($website_mode == 0 || $website_mode == 1) {
            // 门户模式首页

            // hook model_website_template_category_portal_before.php
            if ($forum['well_forum_type']) {
                if ($forum['well_tpl_cate'] AND file_exists($well_cate_tpl)) {
                    $template_page = $well_cate_tpl;
                } else {
                    $template_page = $default_template_path . 'portal_category.htm';
                }
            } else {
                $template_page = $default_template_path . 'list.htm';
            }

            // hook model_website_template_category_portal_after.php

        } elseif ($website_mode == 2) {
            // 扁平模式首页

            // hook model_website_template_index_flat_before.php
            if ($forum['well_tpl_cate'] AND file_exists($well_cate_tpl)) {
                $template_page = $well_cate_tpl;
            } else {
                $template_page = $default_template_path . 'list.htm';
            }
            // hook model_website_template_index_flat_after.php
        }

        //$template_page = ($forum['well_tpl_cate'] AND file_exists($well_cate_tpl)) ? $well_cate_tpl : ($forum['well_forum_type'] ? (file_exists($default_template_path . 'portal_category.htm') ? $default_template_path . 'portal_category.htm' : $default_template_path . 'list.htm') : $default_template_path . 'list.htm');

        // hook model_website_template_category_end.php

    } elseif ($type == 3) {

        // hook model_website_template_read_start.php

        // 内容页
        $well_show_tpl = $template_path . $pre . $forum['well_tpl_read'];

        // hook model_website_template_read_before.php

        //$template_page = ($forum['well_tpl_read'] AND file_exists($well_show_tpl)) ? $well_show_tpl : $default_template_path . 'read.htm';
        $template_page = ($forum['well_tpl'] AND file_exists($well_show_tpl)) ? $well_show_tpl : $default_template_path . 'read.htm';

        // hook model_website_template_read_end.php
    }

    // hook model_website_template_end.php

    return $template_page;
}

// flag模板
function well_website_flag_template($read)
{
    $website_conf = GLOBALS('website_conf');
    // 0自适应 1PC和手机 2PC、平板和手机
    $tpl_mode = $website_conf['setting']['tpl_mode'];

    // hook model_website_flag_template_start.php

    !isset($tpl_mode) AND $tpl_mode = 0;

    // 0电脑 1微信 2手机浏览器 3pad
    $detect = well_detect_device();

    $pre = '';
    if ($tpl_mode && $detect) {
        if ($tpl_mode == 2 && $detect == 3) {
            $pre = 'pad.';
        } else {
            $pre = 'm.';
        }
    }

    // hook model_website_flag_template_before.php

    // 自定义的模板路径和文件
    $template_page = '';

    // 可hook模板路径
    // hook model_website_flag_template_after.php

    // 默认模板路径
    !file_exists($template_page) AND $template_page = APP_PATH . 'plugin/well_cms_x/view/htm/flag_list.htm';

    // hook model_website_flag_template_end.php

    return $template_page;
}

// tag模板
function well_website_tag_template($read)
{
    $website_conf = GLOBALS('website_conf');
    // 0自适应 1PC和手机 2PC、平板和手机
    $tpl_mode = $website_conf['setting']['tpl_mode'];

    // hook model_website_tag_template_start.php

    !isset($tpl_mode) AND $tpl_mode = 0;

    // 0电脑 1微信 2手机浏览器 3pad
    $detect = well_detect_device();

    $pre = '';
    if ($tpl_mode && $detect) {
        if ($tpl_mode == 2 && $detect == 3) {
            $pre = 'pad.';
        } else {
            $pre = 'm.';
        }
    }

    // hook model_website_tag_template_before.php

    // 自定义的模板路径和文件
    $template_page = '';

    // 可hook模板路径
    // hook model_website_tag_template_after.php

    // 默认模板路径
    !file_exists($template_page) AND $template_page = APP_PATH . 'plugin/well_cms_x/view/htm/tag.htm';

    // hook model_website_tag_template_end.php

    return $template_page;
}

// hook model_website_template_end.php

?>