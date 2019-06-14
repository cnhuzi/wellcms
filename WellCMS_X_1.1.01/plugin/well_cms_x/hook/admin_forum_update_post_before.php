<?php exit;
    // 后台栏目更新
    // hook website_admin_forum_update_post_array_start.php

    if (!$_forum['threads'] && !$_forum['well_son']) {
        $well_type = param('well_type', 0);
        $well_forum_type = param('well_forum_type', 0);
        $well_fup = param('well_fup', 0);
    } else {
        $well_type = $_forum['well_type'];
        $well_forum_type = $_forum['well_forum_type'];
        $well_fup = $_forum['well_fup'];
    }
    $well_nav_display = param('well_nav_display', 0);
    $well_model = param('well_model', 0);
    $old_fup = param('old_fup', 0);
    $well_tpl = param('well_tpl', '', FALSE);
    $well_tpl_cate = param('well_tpl_cate', '', FALSE);
    $well_tpl_read = param('well_tpl_read', '', FALSE);
    $width = param('width', 170);
    $height = param('height', 113);
    $picture_size = array('width' => $width, 'height' => $height);
    $seo_keywords = param('seo_keywords');

    // 修改前配置上级频道
    if ($old_fup) {
        if ($old_fup != $well_fup) {
            // 旧频道-1
            forum_update($old_fup, array('well_son-' => 1));
            $well_fup AND forum_update($well_fup, array('well_son+' => 1));
        }
    } else {
        $well_fup AND forum_update($well_fup, array('well_son+' => 1));
    }

    // 频道不显示
    if ($well_forum_type == 1) {
        $well_display = 0;
        $well_news = 0;
        $well_thread_rank = 0;
        $well_list_news = 0;
        $well_channel_news = 0;
        $well_comment = 0;
        $well_pagesize = 0;
    } else {
        // 列表需要显示数据
        $well_comment = param('well_comment', 0);
        $well_type == 0 AND $well_comment = 0;

        $well_display = param('well_display', 0);
        $well_type == 0 AND $well_display = 0;

        $well_thread_rank = param('well_thread_rank', 0);
        $well_type == 0 AND $well_thread_rank = 0;

        $well_news = param('well_news', 0);
        $well_display AND !$well_news AND $well_news = 10;
        !$well_display AND $well_news = 0;

        $well_channel_news = param('well_channel_news', 0);
        $well_channel_news = $well_channel_news ? $well_channel_news : 10;
        $well_list_news = param('well_list_news', 0);
        $well_list_news = $well_list_news ? $well_list_news : 10;
        $well_pagesize = param('well_pagesize', 0);
        $well_pagesize = $well_pagesize ? $well_pagesize : ($well_type == 1 ? 20 : 0);
        $well_type == 0 AND $well_pagesize = 0;
    }

    // 网站 & 自建模板
    if ($well_type == 1 && $well_tpl == 1) {
        // 0列表 1频道

        // 用户自行开发的模板上传路径
        $path = APP_PATH . 'plugin/well_cms_x/view/template/';

        // hook website_admin_forum_update_post_template_after.php

        // 列表&频道模板
        if ($well_forum_type == 1 || $well_forum_type == 0) {

            $well_tpl_cate = trim($well_tpl_cate);

            if (!$well_tpl_cate) message('well_tpl_cate', lang('well_template_name_error'));
            // 为了安全计算长度后 截取后5位 如果不是htm或html后缀不通过
            $strlen = xn_strlen($well_tpl_cate);
            $cate_tplstr = xn_substr($well_tpl_cate, $strlen - 5, 5);
            $str_cate_tpl = strstr($cate_tplstr, '.htm');
            if ($str_cate_tpl === FALSE) message('well_tpl_cate', lang('well_template_name_error'));
            // 检查文件是否存在
            if (!file_exists($path . $well_tpl_cate)) message('well_tpl_cate', lang('well_templatetpl_not exist'));
        }

        // 内容页模板
        if ($well_forum_type == 0) {

            $well_tpl_read = trim($well_tpl_read);

            if (!$well_tpl_read) message('well_tpl_read', lang('well_template_error'));
            // 为了安全计算长度后 截取后5位 如果不是htm或html后缀不通过
            $strlen = xn_strlen($well_tpl_read);
            $show_tplstr = xn_substr($well_tpl_read, $strlen - 5, 5);
            $str_show_tpl = strstr($show_tplstr, '.htm');
            if ($str_show_tpl === FALSE) message('well_tpl_read', lang('well_template_error'));
            if (!file_exists($path . $well_tpl_read)) message('well_tpl_read', lang('well_templatetpl_not exist'));
        }
    }

    // hook website_admin_forum_update_post_array_before.php

    $arr['seo_keywords'] = $seo_keywords;
    $arr['well_type'] = $well_type;
    $arr['well_forum_type'] = $well_forum_type;
    $arr['well_fup'] = $well_fup;
    $arr['well_nav_display'] = $well_nav_display;
    $arr['well_model'] = $well_model;
    $arr['well_tpl'] = $well_tpl;
    $arr['well_tpl_cate'] = $well_tpl_cate;
    $arr['well_tpl_read'] = $well_tpl_read;
    $arr['well_comment'] = $well_comment;
    $arr['well_display'] = $well_display;
    $arr['well_thread_rank'] = $well_thread_rank;
    $arr['well_news'] = $well_news;
    $arr['well_channel_news'] = $well_channel_news;
    $arr['well_list_news'] = $well_list_news;
    $arr['well_pagesize'] = $well_pagesize;
    $arr['well_picture_size'] = json_encode($picture_size);
    // hook website_admin_forum_update_post_array_end.php
?>