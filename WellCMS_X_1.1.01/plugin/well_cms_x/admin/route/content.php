<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1, 'list');

// hook website_admin_content_start.php

$website_forumlist = well_return_column($forumlist);

// hook website_admin_content_before.php

if ($action == 'list') {
    // content-list-fid-page

    // hook website_admin_content_list_start.php

    if ($method == 'GET') {

        // hook website_admin_content_list_get_start.php

        $fid = param(2, 0);
        $page = param(3, 1);
        $orderby = param(4, 0); // 主题排序
        $pagesize = 20;
        // 插件预留
        $extra = array('model' => 0, 'fid' => $fid, 'path' => '../');

        // hook website_admin_content_list_get_before.php

        // 所有审核过的内容
        if ($fid) {

            // hook website_admin_content_list_get_forum_before.php

            $forum = array_value($forumlist, $fid);
            !$forum AND message(1, lang('forum_not_exists'));

            // hook website_admin_content_list_get_forum_after.php

            //$n = well_thread_fid_count($fid);
            $n = $forum['threads'];

            // hook website_admin_content_list_get_forum_thread_before.php

            // 栏目下的主题
            if ($orderby) {
                $threadlist = $n ? well_thread_find_rank_desc($fid, $page, $pagesize) : NULL;
            } else {
                $threadlist = $n ? well_thread_find_by_fid($fid, $page, $pagesize) : NULL;
            }

            // hook website_admin_content_list_get_forum_thread_after.php

        } else {

            // hook website_admin_content_list_get_count_before.php

            $n = well_thread_tid_count();

            // hook website_admin_content_list_get_count_after.php

            if ($n) {
                // 遍历所有tid
                $tidlist = well_thread_tid_find($page, $pagesize);
                // hook website_admin_content_list_get_tid_before.php
                $tidarr = arrlist_values($tidlist, 'tid');
                // hook website_admin_content_list_get_tid_after.php

                // hook website_admin_content_list_get_thread_before.php
                // 遍历所有主题
                $threadlist = well_thread_find($tidarr, $pagesize);
                // 合并
                $threadlist = well_array2_merge($threadlist, $tidlist, 'tid');
                // hook website_admin_content_list_get_thread_after.php
            }

            // hook website_admin_content_list_get_page_before.php

            // 查找置顶帖
            if ($page == 1) {
                // hook website_admin_content_list_get_top_before.php
                $toplist = well_thread_top_find_cache($fid, 3);
                $threadlist = (array)$toplist + (array)(isset($threadlist) ? $threadlist : array());
                // hook website_admin_content_list_get_top_after.php
            }

            // hook website_admin_content_list_get_page_after.php
        }

        $pagination = pagination(url('content-list-' . $fid . '-{page}', $extra), $n, $page, $pagesize);

        // hook website_admin_content_list_get_after.php

        $header['title'] = lang('well_content');
        $header['mobile_title'] = lang('well_content');

        // hook website_admin_content_list_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_list.htm');

    } elseif ($method == 'POST') {

        $arr = _POST('data');

        // hook website_admin_content_list_post_start.php

        if (!empty($arr)) {
            // hook website_admin_content_list_post_before.php
            foreach ($arr as &$val) {
                $rank = intval($val['rank']);
                $tid = intval($val['tid']);
                intval($val['oldrank']) != $rank && $tid AND $r = well_thread_tid_update_rank($tid, $rank);
                // hook website_admin_content_list_post_foreach.php
            }
            // hook website_admin_content_list_post_after.php
        }

        // hook website_admin_content_list_post_end.php

        message(0, lang('update_successfully'));
    }

} elseif ($action == 'create') {

    // hook website_admin_content_create_start.php

    $fid = param(2, 0);

    // hook website_admin_content_create_before.php

    if ($method == 'GET') {

        // hook website_admin_content_create_get_start.php

        $forum = $fid ? array_value($forumlist, $fid) : array();
        $model = array_value($forum, 'well_model', 0);

        // hook website_admin_content_create_get_before.php

        $forum_flagids = array();
        $category_flagids = array();
        $index_flagids = array();
        $index_flag = well_website_flag_find_by_fid_display_cache(0);
        // hook website_admin_content_create_get_flag_before.php
        if ($index_flag) {
            foreach ($index_flag as $key => &$val) {
                unset($index_flag[$key]['fid']);
                unset($index_flag[$key]['number']);
                unset($index_flag[$key]['count']);
                unset($index_flag[$key]['display']);
                unset($index_flag[$key]['create_date']);
                unset($index_flag[$key]['display_text']);
                unset($index_flag[$key]['column_name']);
                unset($val);
                // hook website_admin_content_create_get_flag_center.php
            }
        }

        // hook website_admin_content_create_get_flag_after.php

        // 后台无需再过滤权限 后续增加鉴权
        //$forumlist_allowthread = forum_list_access_filter($forumlist, $gid, 'allowthread');
        //empty($forumlist_allowthread) AND message(-1, lang('user_group_insufficient_privilege'));

        // hook website_admin_content_create_get_mainpic_before.php

        // 获取主图
        $filepic = _SESSION('tmp_mainpic');
        $mainpic = !empty($filepic) ? '../' . $filepic['url'] : '../plugin/well_cms_x/view/image/nopic.png';

        // hook website_admin_content_create_get_mainpic_middle.php

        $website_conf = GLOBALS('website_conf');
        $picture = $website_conf['picture_size'];
        $pic_width = $picture['width'];
        $pic_height = $picture['height'];

        // hook website_admin_content_create_get_mainpic_after.php

        $input = array();
        $form_title = lang('well_add_content');
        $form_action = url('content-create-' . $fid);
        $form_submit_txt = lang('submit');
        $form_subject = '';
        $form_message = '';
        $form_brief = '';
        $form_doctype = 0;
        $form_closed = '';
        $form_link = '';
        $form_keyword = '';
        $form_description = '';
        $quotepid = 0;
        $filelist = array();
        $filelist += (array)_SESSION('tmp_website_files');

        $tagstr = '';

        // hook website_admin_content_create_get_form_after.php

        $breadcrumb_flag = lang('well_add_content');

        // hook website_admin_content_create_get_after.php

        $header['title'] = lang('well_add_content');
        $header['mobile_title'] = lang('well_add_content');

        // hook website_admin_content_create_get_template.php

        // 可以根据自己设计的添加内容界面绑定栏目，绑定模型，显示不同的界面
        /*if ($model == 0) {
            // 加载
            include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_post.htm');
            exit;
        }*/

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_post.htm');

        // hook website_admin_content_create_get_end.php

    } elseif ($method == 'POST') {

        // hook website_admin_content_create_post_start.php

        $fid = param('fid', 0);
        $forum = array_value($forumlist, $fid);
        !$forum AND message('fid', lang('forum_not_exists'));

        // hook website_admin_content_create_post_forum_after.php

        $r = forum_access_user($fid, $gid, 'allowthread');
        !$r AND message(-1, lang('user_group_insufficient_privilege'));

        // hook website_admin_content_create_post_access_after.php

        $subject = param('subject');
        empty($subject) AND message('subject', lang('please_input_subject'));
        $subject = well_filter_all_html($subject);
        xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
        // 过滤标题 关键词

        // hook website_admin_content_create_post_subject_after.php

        $link = param('link', 0);
        $closed = param('closed', 0);
        $mainpic = param('mainpic', 0);
        $delete_pic = param('delete_pic', 0);
        $save_image = param('save_image', 0);
        $brief_auto = param('brief_auto', 0);
        $doctype = param('doctype', 0);
        $doctype > 10 AND message(-1, lang('doc_type_not_supported'));

        // hook website_admin_content_create_post_before.php

        $message = $_message = '';
        if ($link == 0) {
            $message = param('message', '', FALSE);
            empty($message) AND message('message', lang('please_input_message'));
            xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));
            // 过滤所有html标签
            $_message = well_filter_all_html($message);
            // 过滤内容 关键词

            // hook website_admin_content_create_post_message_after.php
        }

        // hook website_admin_content_create_post_brief_start.php

        $brief = param('brief');
        if ($brief) {

            $brief = well_filter_all_html($brief);

            // 过滤简介 关键词
            // hook website_admin_content_create_post_brief_before.php

            xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
        } else {
            $brief = ($brief_auto AND $_message) ? xn_substr($_message, 0, 120) : '';
        }

        // hook website_admin_content_create_post_brief_end.php

        $keyword = param('keyword');
        $keyword = well_filter_all_html($keyword);
        // 过滤内容 关键词
        // hook website_admin_content_create_post_keyword_before.php
        // 超出则截取
        xn_strlen($keyword) > 64 AND $keyword = xn_substr($keyword, 0, 64);

        // hook website_admin_content_create_post_description_before.php

        $description = param('description');
        $description = well_filter_all_html($description);
        // 过滤内容 关键词
        // hook website_admin_content_create_post_description_center.php
        // 超出则截取
        xn_strlen($description) > 64 AND $description = xn_substr($description, 0, 120);

        // hook website_admin_content_create_post_description_after.php

        $tags = param('tags', '', FALSE);
        $tags = trim($tags, ',');
        $tags = well_filter_all_html($tags);
        // 过滤标签 关键词
        // hook website_admin_content_create_post_tag_center.php

        // hook website_admin_content_create_post_tag_after.php

        $thread = array('fid' => $fid, 'type' => 0, 'link' => $link, 'doctype' => $doctype, 'subject' => $subject, 'brief' => $brief, 'keyword' => $keyword, 'description' => $description, 'closed' => $closed, 'mainpic' => $mainpic, 'save_image' => $save_image, 'delete_pic' => $delete_pic, 'message' => $message);

        // hook website_admin_content_create_post_middle.php

        $tid = well_thread_create($thread);
        $tid === FALSE AND message(-1, lang('create_thread_failed'));
        unset($thread);

        // hook website_admin_content_create_post_after.php

        $tag_json = well_tag_post($tid, $fid, $tags);
        well_thread_update($tid, array('tag' => $tag_json)) === FALSE AND message(-1, lang('update_thread_failed'));

        // 首页flag
        $flag_index_arr = array_filter(param('index', array()));
        !empty($flag_index_arr) AND well_website_flagidarr_db_create(0, 1, $tid, $flag_index_arr) === FALSE AND message(-1, lang('well_create_index_flag_failed'));

        // 频道flag
        $flag_cate_arr = array_filter(param('category', array()));
        $forum['well_fup'] AND !empty($flag_cate_arr) AND well_website_flagidarr_db_create($forum['well_fup'], 2, $tid, $flag_cate_arr) === FALSE AND message(-1, lang('well_create_category_flag_failed'));

        // 栏目flag
        $flag_forum_arr = array_filter(param('forum', array()));
        !empty($flag_forum_arr) AND well_website_flagidarr_db_create($fid, 3, $tid, $flag_forum_arr) === FALSE AND message(-1, lang('well_create_forum_flag_failed'));

        // hook website_admin_content_create_post_end.php

        message(0, lang('create_thread_sucessfully'));
    }

} elseif ($action == 'update') {

    // hook website_admin_content_update_start.php

    $tid = param(2, 0);
    !$tid AND message(-1, lang('data_malformation'));

    $thread = well_thread_read($tid);
    !$thread AND message(-1, lang('thread_not_exists'));
    $fid = $thread['fid'];

    // hook website_admin_content_update_before.php

    $thread_data = well_data_read($tid);

    // hook website_admin_content_update_after.php

    list($index_flagids, $category_flagids, $forum_flagids, $flagarr) = well_website_flag_by_tid_return($tid);

    // hook website_admin_content_update_end.php

    if ($method == 'GET') {

        // hook website_admin_content_update_get_start.php

        $forum = array_value($forumlist, $fid);
        $model = array_value($forum, 'well_model', 0);

        // hook website_admin_content_update_get_forum_after.php

        $index_flag = well_website_flag_find_by_fid_display_cache(0);
        // hook website_admin_content_update_get_flag_before.php
        if ($index_flag) {
            foreach ($index_flag as $key => &$val) {
                unset($index_flag[$key]['fid']);
                unset($index_flag[$key]['number']);
                unset($index_flag[$key]['count']);
                unset($index_flag[$key]['display']);
                unset($index_flag[$key]['create_date']);
                unset($index_flag[$key]['display_text']);
                unset($index_flag[$key]['column_name']);
                unset($val);
                // hook website_admin_content_update_get_flag_center.php
            }
        }

        // hook website_admin_content_update_get_flag_after.php

        // 获取主图
        $thread['icon'] = $thread['icon'] ? ('../' . $thread['icon_text']) : '../plugin/well_cms_x/view/image/nopic.png';

        // hook website_admin_content_update_get_icon_after.php

        $website_conf = GLOBALS('website_conf');
        $picture = $website_conf['picture_size'];
        $pic_width = $picture['width'];
        $pic_height = $picture['height'];

        // hook website_admin_content_update_get_files_before.php

        $attachlist = $imagelist = $filelist = array();
        if ($thread['files']) {
            list($attachlist, $imagelist, $filelist) = well_attach_find_by_tid($tid);
        }

        $tagstr = $thread['tag_text'] ? implode(',', $thread['tag_text']) . ',' : '';

        // hook website_admin_content_update_get_files_after.php

        $input = array();
        $form_title = lang('well_edit');
        $form_action = url('content-update-' . $tid);
        $form_submit_txt = lang('submit');
        $form_subject = $thread['subject'];
        $form_message = str_replace('="upload/', '="../upload/', $thread_data['message']);
        $form_brief = $thread['brief'];
        $form_doctype = $thread_data['doctype'];
        $form_link = $thread['link'] ? 'checked="checked"' : '';
        $quotepid = 0;
        $form_closed = $thread['closed'] >= 1 ? 'checked="checked"' : '';
        $form_keyword = $thread['keyword'];
        $form_description = $thread['description'];
        $filelist = array();
        $filelist += (array)_SESSION('tmp_website_files');
        $mainpic = $thread['icon'];

        // hook website_admin_content_update_get_form_after.php

        $breadcrumb_flag = lang('well_edit');

        // hook website_admin_content_update_get_after.php

        $header['title'] = lang('well_edit');
        $header['mobile_title'] = lang('well_edit');

        // hook website_admin_content_update_get_template.php

        // 可以根据自己设计的添加内容界面绑定栏目，绑定模型，显示不同的界面
        /*if ($model == 0) {
            // 加载
            include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_post.htm');
            exit;
        }*/

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_post.htm');

        // hook website_admin_content_update_get_end.php

    } elseif ($method == 'POST') {

        // hook website_admin_content_update_post_start.php

        $arr = array();

        $subject = param('subject');
        empty($subject) AND message('subject', lang('please_input_subject'));
        $subject = strip_tags($subject);
        xn_strlen($subject) > 128 AND message('subject', lang('subject_length_over_limit', array('maxlength' => 128)));
        // 过滤标题 关键词

        // hook website_admin_content_update_post_subject_before.php

        if ($subject != $thread['subject']) {
            mb_strlen($subject, 'UTF-8') > 80 AND message('subject', lang('subject_max_length', array('max' => 80)));
            $arr['subject'] = $subject;

            $thread['top'] > 0 AND well_thread_top_cache_delete($fid);
        }

        // hook website_admin_content_update_post_subject_after.php

        $link = param('link', 0);
        $link != $thread['link'] AND $arr['link'] = $link;

        // hook website_admin_content_update_post_link_after.php

        $closed = param('closed', 0);
        $closed != $thread['closed'] AND $arr['closed'] = $closed;

        // hook website_admin_content_update_post_closed_after.php

        $doctype = param('doctype', 0);
        $doctype > 10 AND message(-1, lang('doc_type_not_supported'));

        // hook website_admin_content_update_post_message_before.php

        $message = $_message = '';
        if ($link == 0) {
            $message = param('message', '', FALSE);
            empty($message) AND message('message', lang('please_input_message'));

            // 超出提示
            xn_strlen($message) > 2028000 AND message('message', lang('message_too_long'));

            $_message = well_filter_all_html($message);
            // 过滤内容 关键词

            // hook website_admin_content_update_post_message_center.php
        }

        // hook website_admin_content_update_post_message_after.php

        $brief_auto = param('brief_auto', 0);
        $brief = param('brief');
        if ($brief) {

            $brief = strip_tags($brief);

            // 过滤简介 关键词
            // hook website_admin_content_update_post_brief_before.php

            xn_strlen($brief) > 120 AND $brief = xn_substr($brief, 0, 120);
        } else {
            $brief = ($brief_auto AND $_message) ? xn_substr($_message, 0, 120) : '';
        }

        // hook website_admin_content_update_post_brief_after.php

        if ($brief != $thread['brief']) {
            xn_strlen($brief) > 128 AND $brief = xn_substr($brief, 0, 128);
            $arr['brief'] = $brief;
        }

        // hook website_admin_content_update_post_keyword_before.php

        $keyword = param('keyword');
        $keyword = strip_tags($keyword);
        // 过滤内容 关键词
        // hook website_admin_content_update_post_keyword_center.php
        // 超出则截取
        xn_strlen($keyword) > 64 AND $keyword = xn_substr($keyword, 0, 64);

        $keyword != $thread['keyword'] AND $arr['keyword'] = $keyword;

        // hook website_admin_content_update_post_keyword_after.php

        $description = param('description');
        $description = strip_tags($description);
        // 过滤内容 关键词
        // hook website_admin_content_update_post_description_before.php
        // 超出则截取
        xn_strlen($description) > 64 AND $description = xn_substr($description, 0, 120);
        $description != $thread['description'] AND $arr['description'] = $description;

        // hook website_admin_content_update_post_fid_before.php

        $newfid = param('fid', 0);
        $forum = array_value($forumlist, $fid);
        !$forum AND message('fid', lang('forum_not_exists:'));

        // hook website_admin_content_update_post_fid_center.php

        if ($fid != $newfid) {
            //!forum_access_user($fid, $gid, 'allowthread') AND message(-1, lang('user_group_insufficient_privilege'));

            // hook website_admin_content_update_post_fid_access.php

            $thread['uid'] != $uid AND !forum_access_mod($fid, $gid, 'allowupdate') AND message(-1, lang('user_group_insufficient_privilege'));

            // hook website_admin_content_update_post_fid_update.php

            forum__update($newfid, array('threads+' => 1));
            forum__update($thread['fid'], array('threads-' => 1));
            well_thread_top_update_by_tid($tid, $newfid);

            well_thread_tid_update($tid, $newfid);

            $arr['fid'] = $newfid;
        }

        // 1 删除主图
        $delete_pic = param('delete_pic', 0);
        // hook website_admin_content_update_post_fid_after.php
        //$upload_picture = well_attach_assoc_type(1);
        $upload_mainpic = well_attach_assoc_type(0);
        if (!empty($upload_mainpic) || $delete_pic) {
            // Ym变更删除旧图
            $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');
            $old_day = $thread['icon'] ? date($attach_dir_save_rule, $thread['icon']) : '';
            $day = date($attach_dir_save_rule, $time);
            if ($day != $old_day || $delete_pic) {
                $file = $conf['upload_path'] . 'website_mainpic/' . $old_day . '/' . $tid . '.jpeg';
                file_exists($file) AND @unlink($file);
            }

            // hook website_admin_content_update_post_unlink_after.php

            if ($delete_pic) {
                $arr['icon'] = 0;
            } else {
                $arr['icon'] = $time;
                // 关联主图 type 0或空内容图片或附件 1:内容主图 8:节点主图 9:节点tag主图 教练套课主图
                $assoc_mainpic = array('tid' => $tid, 'uid' => $uid, 'type' => 0);
                // hook website_admin_content_update_post_attach_before.php
                well_attach_assoc_data($assoc_mainpic);
                unset($assoc_mainpic);
            }

        }/* elseif($mainpic && (!empty($upload_picture))) {
            $arr['icon'] = $time;
            well_attach_create_mainpic($tid, $fid);
        }*/

        // hook website_admin_content_update_post_attach_after.php

        $tags = param('tags', '', FALSE);
        $tags = trim($tags, ',');
        $tags = well_filter_all_html($tags);
        // 过滤标签 关键词
        // hook website_admin_content_update_post_tag_center.php

        $tag_json = well_tag_post_update($tid, $fid, $tags, $thread['tag_text']);
        if ($tag_json) {
            $tag_json != $thread['tag_text'] AND $arr['tag'] = $tag_json;
        } else {
            $arr['tag'] = '';
        }

        // hook website_admin_content_update_post_tag_after.php

        !empty($arr) AND well_thread_update($tid, $arr) === FALSE AND message(-1, lang('update_thread_failed'));
        unset($arr);

        // hook website_admin_content_update_post_before.php

        // 站外链接 无需更新数据表
        if ($link == 0) {

            $save_image = param('save_image', 0);
            $save_image AND $message = well_save_remote_image(array('tid' => $tid, 'fid' => $fid, 'uid' => $uid, 'message' => $message));

            // 关联附件
            $assoc_attach = array('tid' => $tid, 'uid' => $uid, 'type' => 1, 'images' => $thread['images'], 'files' => $thread['files'], 'message' => $message);
            well_attach_assoc_data($assoc_attach);

            $update = array('tid' => $tid, 'gid' => $gid, 'doctype' => $doctype, 'message' => $assoc_attach['message']);
            well_data_update($tid, $update) === FALSE AND message(-1, lang('update_post_failed'));
            unset($update);
        }

        // hook website_admin_content_update_post_center.php

        // 首页flag
        $flag_index_arr = array_filter(param('index', array()));
        // 首页需要再创建的
        $new_index_flagids = !empty($flag_index_arr) ? array_diff($flag_index_arr, $index_flagids) : '';
        !empty($new_index_flagids) AND well_website_flagidarr_db_create(0, 1, $tid, $new_index_flagids) === FALSE AND message(-1, lang('well_create_index_flag_failed'));
        // 返回首页被取消的flagid
        $old_index_flagids = array_diff($index_flagids, $flag_index_arr);
        !empty($old_index_flagids) AND well_website_flag_thread_delete_by_ids($old_index_flagids, $flagarr);

        // 频道flag
        $flag_cate_arr = array_filter(param('category', array()));
        // 频道需要再创建的
        $new_cate_flagids = !empty($flag_cate_arr) ? array_diff($flag_cate_arr, $category_flagids) : '';
        $forum['well_fup'] AND !empty($new_cate_flagids) AND well_website_flagidarr_db_create($forum['well_fup'], 2, $tid, $new_cate_flagids) === FALSE AND message(-1, lang('well_create_category_flag_failed'));
        // 返回频道被取消的flagid
        $old_cate_flagids = array_diff($category_flagids, $flag_cate_arr);
        !empty($old_cate_flagids) AND well_website_flag_thread_delete_by_ids($old_cate_flagids, $flagarr);

        // 栏目flag
        $flag_forum_arr = array_filter(param('forum', array()));
        // 需要再创建的
        $new_forum_flagids = !empty($flag_forum_arr) ? array_diff($flag_forum_arr, $forum_flagids) : '';
        !empty($new_forum_flagids) AND well_website_flagidarr_db_create($fid, 3, $tid, $new_forum_flagids) === FALSE AND message(-1, lang('well_create_forum_flag_failed'));
        // 返回被取消的flagid
        $old_forum_flagids = array_diff($forum_flagids, $flag_forum_arr);
        !empty($old_forum_flagids) AND well_website_flag_thread_delete_by_ids($old_forum_flagids, $flagarr);

        // hook website_admin_content_update_post_end.php

        message(0, lang('update_successfully'));
    }

} elseif ($action == 'delete') {

    if ($method != 'POST') return;

    // hook website_admin_content_delete_start.php

    $tid = param(2, 0);
    !$tid AND message(-1, lang('data_malformation'));

    $thread = well_thread_read_cache($tid);
    !$thread AND message(-1, lang('thread_not_exists'));

    // hook website_admin_content_delete_before.php

    // 权限判断 仅限管理员和用户本人有权限
    $allowdelete = ($uid == $thread['uid']) || forum_access_mod($thread['fid'], $gid, 'allowdelete');

    (!$allowdelete OR $thread['closed']) AND message(-1, lang('thread_has_already_closed'));

    // hook website_admin_content_delete_center.php

    // 全部删除
    well_thread_delete_all($tid) === FALSE AND message(-1, lang('delete_failed'));

    include _include(APP_PATH . 'plugin/well_cms_x/model/well_website_modelog.func.php');

    $arr = array('type' => 1, 'uid' => $uid, 'tid' => $tid, 'subject' => $thread['subject'], 'comment' => '', 'create_date' => $time);
    // hook website_admin_content_delete_after.php
    // 创建日志
    well_modelog_create($arr);

    // hook website_admin_content_delete_end.php

    message(0, lang('delete_completely'));

} elseif ($action == 'license') {

    $header['title'] = lang('well_license');
    $header['mobile_title'] = lang('well_license');
    $header['mobile_link'] = url('content-license');

    include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/content_license.htm');
}

// hook website_admin_content_end.php

?>