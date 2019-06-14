<?php exit;
    // 后台栏目更新
    $well_type = array_value($_forum, 'well_type', 0);
    $well_forum_type = $_forum['well_forum_type'];

    // 有数据禁止修改
    if ($_forum['threads'] || $_forum['well_son']) {
        if ($well_forum_type) {
            $well_forum_name = lang('well_channel');
        } else {
            $well_forum_name = lang('well_top_column');
        }
    }

    $fid = $_forum['fid'];
    $well_fup = $_forum['well_fup'];
    $old_fup = $_forum['well_fup'];
    // 所属频道
    $fidarr = well_channel_key_values($forumlist);
    //$well_channel_name = $fidarr[$_forum['well_fup']];

    $well_model = $_forum['well_model'];
    // 栏目模型 加载不同模型
    $well_model_arr = array();
    $well_model_arr[] = lang('well_article');
    // 其他模型
    // hook website_forum_update_get_well_model_after.php

    $well_nav_display = $_forum['well_nav_display'];
    $well_comment = $_forum['well_comment'];
    $well_tpl = $_forum['well_tpl'];
    $well_tpl_cate = $_forum['well_tpl_cate'];
    $well_tpl_read = $_forum['well_tpl_read'];
    $well_display = $_forum['well_display'];
    $well_thread_rank = $_forum['well_thread_rank'];
    $well_news = $_forum['well_news'];
    $picture_size = $_forum['well_picture_size'];
    $width = array_value($picture_size, 'width', 170);
    $height = array_value($picture_size, 'height', 113);
    $well_channel_news = $_forum['well_channel_news'];
    $well_list_news = $_forum['well_list_news'];
    $well_pagesize = $_forum['well_pagesize'];

    // 栏目类型 BBS CMS
    $arr = array();
    function_exists('thread_read') AND $arr[0] = lang('well_bbs');
    $arr[1] = lang('well_website');
    // hook website_forum_update_get_well_type_before.php

    if ($_forum['threads'] || $_forum['well_son']) {
        // 所属频道
        $input['well_type'] = well_form_radio('well_type', $arr, $well_type, TRUE);
        $input['well_forum_type'] = well_form_radio('well_forum_type', lang('well_column_category'), $well_forum_type, TRUE);
        $input['well_fup'] = well_form_select('well_fup', $fidarr, $well_fup, TRUE, TRUE);
    } else {
        $input['well_type'] = form_radio('well_type', $arr, $well_type);
        // 栏目属性：频道&列表
        $input['well_forum_type'] = form_radio('well_forum_type', lang('well_column_category'), $well_forum_type);
        $input['well_fup'] = form_select('well_fup', $fidarr, $well_fup);
    }
    unset($arr);

    // 主图宽度
    $input['width'] = form_text('width', $width);
    // 主图高度
    $input['height'] = form_text('height', $height);

    // 栏目模型 加载不同模型
    $input['well_model'] = form_radio('well_model', $well_model_arr, $well_model);

    //--------选项--------
    // 栏目是否显示在导航
    $input['well_nav_display'] = form_radio_yes_no('well_nav_display', $well_nav_display);
    // 是开启评论
    $input['well_comment'] = form_radio_yes_no('well_comment', $well_comment);

    //--------模板--------
    // 0默认 1自建
    $input['well_tpl'] = form_radio('well_tpl', lang('well_tpl_template_radio'), $well_tpl);
    // 分类页模板
    $input['well_tpl_cate'] = form_text('well_tpl_cate', $well_tpl_cate);
    // 内容页模板
    $input['well_tpl_read'] = form_text('well_tpl_read', $well_tpl_read);

    //--------显示--------
    // 首页是否显示内容 1显示
    $input['well_display'] = form_radio_yes_no('well_display', $well_display);
    $input['well_thread_rank'] = form_radio_yes_no('well_thread_rank', $well_thread_rank);
    // 首页显示最新数量
    $input['well_news'] = form_text('well_news', $well_news);

    //--------上级频道显示该栏目--------
    // 频道最新数量
    $input['well_channel_news'] = form_text('well_channel_news', $well_channel_news);

    //--------列表页显示--------
    // 最新数量
    $input['well_list_news'] = form_text('well_list_news', $well_list_news);
    $input['well_pagesize'] = form_text('well_pagesize', $well_pagesize);
    $input['seo_keywords'] = form_text('seo_keywords', $_forum['seo_keywords']);

?>