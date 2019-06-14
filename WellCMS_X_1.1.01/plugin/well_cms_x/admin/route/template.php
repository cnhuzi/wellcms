<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1, 'code');

// 只给代码 用户自行创建文件上传
/*$path = APP_PATH . 'plugin/well_cms_x/view/template/';
        $filelist = $dirlist = array();
        foreach (glob($path . '*') as $val) {
            if (is_file($val)) {
                $filelist[] = well_code_to_utf8($val); //str_replace('../', '', $file);
            } else {
                // 目录
                $dirlist[] = well_code_to_utf8($val);
            }

        }
        echo '<pre>';
        print_r($dirlist);
        echo '</pre>';
        exit;*/
// hook website_admin_template_start.php

if ($action == 'code') {

    // hook website_admin_template_code_start.php

    $path = APP_PATH . 'plugin/well_cms_x/admin/view/code/';
    $path_default = APP_PATH . 'plugin/well_cms_x/view/htm/';

    if ($method == 'GET') {

        $model = param(2, 0); // 模式:0自定义 1门户 2扁平
        $fid = param(3, 0); // 0首页 有值则是频道和栏目
        $extra = array(); // 插件预留

        // hook website_admin_template_code_get_start.php

        $introduction = file_get_contents_try($path . 'code_html.htm');
        $introduction = htmlspecialchars($introduction);

        // hook website_admin_template_code_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/template_code.htm');

    } elseif ($method == 'POST') {

        $id = param('id');

        $code = '';
        switch ($id) {
            case 'complete-header':
                $code = file_get_contents_try($path . 'code_header.htm');
                break;
            case 'top-header':
                $code = file_get_contents_try($path . 'code_header_nav.inc.htm');
                break;
            case 'head':
                $code = file_get_contents_try($path . 'code_head.htm');
                break;
            case 'bottom-footer':
                $code = file_get_contents_try($path . 'code_footer.htm');
                break;
            case 'footer-inc':
                $code = file_get_contents_try($path . 'code_footer.inc.htm');
                break;
            case 'find-nav':
                $code = file_get_contents_try($path . 'code_header_nav.htm');
                break;
            case 'login':
                $code = file_get_contents_try($path . 'code_login.htm');
                break;
            case 'flat-complete-main':
                $code = file_get_contents_try($path . 'code_main_flat.htm');
                break;
            case 'flat-complete-list':
                $code = file_get_contents_try($path . 'code_list_flat.htm');
                break;
            case 'portal-complete-main':
                $code = file_get_contents_try($path . 'code_main_portal.htm');
                break;
            case 'portal-complete-category':
                $code = file_get_contents_try($path . 'code_category_portal.htm');
                break;
            case 'portal-complete-list':
                $code = file_get_contents_try($path . 'code_list_flat.htm');
                break;
            case 'read':
                $code = file_get_contents_try($path . 'code_read.htm');
                break;
            default:
                break;
        }

        message(0, $code);
    }

} elseif ($action == 'diy') {

    /*
     * 标题title 关键词keyword 描述description
     * 注册链接register_url 注册页面register_complete 注册代码register
     * 登录链接login_url 登录页面login_complete 登录代码login
     * 导航nav 导航链接nav_url 频道或栏目链接column_url
     * */

    if ($method == 'GET') {

        $all_category = well_all_category($forumlist);

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/template_diy.htm');

    } elseif ($method == 'POST') {

        // type fid mode
        $type = param('type');
        $fid = param('fid', 0);

        $path = APP_PATH . 'plugin/well_cms_x/admin/view/code/';

        $code = '';
        switch ($type) {
            case 'title':
                $code = file_get_contents_try($path . 'diy_title.htm');
                break;
            case 'keyword':
                $code = file_get_contents_try($path . 'diy_keyword.htm');
                break;
            case 'description':
                $code = file_get_contents_try($path . 'diy_description.htm');
                break;
            case 'register_url':
                $code = file_get_contents_try($path . 'diy_register_url.htm');
                break;
            case 'register_complete':
                $code = file_get_contents_try($path . 'diy_register_complete.htm');
                break;
            case 'register':
                $code = file_get_contents_try($path . 'diy_register.htm');
                break;
            case 'login_url':
                $code = file_get_contents_try($path . 'diy_login_url.htm');
                break;
            case 'login_complete':
                $code = file_get_contents_try($path . 'diy_login_complete.htm');
                break;
            case 'login':
                $code = file_get_contents_try($path . 'diy_login.htm');
                break;
            case 'navs':
                $code = file_get_contents_try($path . 'diy_nav.htm');
                break;
            case 'nav_url':
                // TODO 选择栏目 fid 对应栏目ID
                $forum = array_value($forumlist, $fid);
                !$forum AND message(1, lang('forum_not_exists'));

                $code = file_get_contents_try($path . 'diy_nav_url.htm');
                // 替换提交的栏目fid
                $code = str_replace('$fid', $fid, $code);
                break;
            case 'column_url':
                $code = file_get_contents_try($path . 'diy_column_url.htm');
                break;
            case 'column_name':
                $code = file_get_contents_try($path . 'diy_column_name.htm');
                break;
            case 'comment':
                $code = file_get_contents_try($path . 'diy_comment.htm');
                break;
            case 'list':
                $code = file_get_contents_try($path . 'diy_list.htm');
                break;
            case 'read':
                $code = file_get_contents_try($path . 'diy_read.htm');
                break;
        }

        message(0, $code);
    }

} elseif ($action == 'main') {

    /*
     * 置顶top、翻页pagination
     * 属性遍历flag、属性名flag_name
     * 属性链接flag_url、属性主题flag_thread
     * 轮播、双列轮播
     * 列表list
     * */

    if ($method == 'GET') {

        $all_category = well_all_category($forumlist);

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/template_main.htm');

    } elseif ($method == 'POST') {

        $type = param('type');
        $fid = param('fid', 0);
        $flagid = param('flagid', 0);
        $cate = param('category'); // 1首页 2频道 3列表 4内容页
        $mode = param('mode'); // 0自定义 1门户 2扁平

        $path = APP_PATH . 'plugin/well_cms_x/admin/view/code/';

        $code = '';
        switch ($type) {
            case 'top':
                // 置顶
                if (in_array($mode, array(0, 2))) {
                    $code = file_get_contents_try($path . 'main_top1.htm');
                } else {
                    $code = file_get_contents_try($path . 'main_top2.htm');
                }
                break;
            case 'pagination':
                // 分页
                $code = file_get_contents_try($path . 'main_pagination.htm');
                break;
            case 'slides-double':
                // 双列轮播
                $code = file_get_contents_try($path . 'main_slides_double.htm');
                break;
            case 'slides':
                // 轮播
                $code = file_get_contents_try($path . 'main_slides.htm');
                break;
            case 'list':
                // 列表
                switch ($cate) {
                    case '1':
                        //门户首页
                        $code = file_get_contents_try($path . ($mode == 1 ? 'main_portal_index.htm' : 'main_list.htm'));
                        break;
                    case '2':
                        //门户频道
                        $code = file_get_contents_try($path . ($mode == 1 ? 'main_portal_index.htm' : 'main_list.htm'));
                        break;
                    case '3':
                        $code = file_get_contents_try($path . 'main_list.htm');
                        break;
                    case '4':
                        $code = file_get_contents_try($path . 'main_comment_list.htm');
                        break;
                    case '5':
                        $code = file_get_contents_try($path . 'main_comment_list.htm');
                        break;
                }
                break;
            case 'flag-loop':
                // 循环属性
                $code = file_get_contents_try($path . 'main_flag_loop.htm');
                break;
            case 'flag-sole':
                // 调用单一属性和属性下主题
                $forum = array_value($forumlist, $fid);
                !$forum AND message(1, lang('forum_not_exists'));

                $code = file_get_contents_try($path . 'main_flag_sole.htm');
                // 替换提交的栏目fid
                $code = str_replace('$flagid', $flagid, $code);
                break;
            case 'portal-column':
                // 调用单一栏目和栏目下主题
                $forum = array_value($forumlist, $fid);
                !$forum AND message(1, lang('forum_not_exists'));

                $code = file_get_contents_try($path . 'main_portal_column.htm');
                // 替换提交的栏目fid
                $code = str_replace('$fid', $fid, $code);
                break;
        }

        message(0, $code);
    }

}

// hook website_admin_template_end.php

?>