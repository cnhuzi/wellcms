<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

$action = param(1, 'list');

$website_forumlist = well_all_category($forumlist);
// hook website_admin_flag_start.php

if ($action == 'list') {

    // hook website_admin_flag_list_start.php

    if ($method == 'GET') {

        // hook website_admin_flag_list_get_start.php

        $type = param('type', 0); // 0首页 1栏目或频道
        $display = param('display', 0);
        $fid = param(2, 0);
        $page = param(3, 1);
        $orderby = param(4, 0); // 0降序 1自定义排序降序
        $pagesize = 25;
        // 插件预留
        $extra = array('type' => $type, 'display' => $display);

        // hook website_admin_flag_list_get_forum_before.php

        $forum = forum_read($fid);

        // hook website_admin_flag_list_get_forum_after.php

        if ($type) {
            // $type = 1 栏目下属性显示和不显示的属性
            $n = well_website_flag_count_by_fid_display($fid, $display);

            // 启用了自定义排序
            $orderby && $n AND $pagesize = $n;

            // 各栏目所有显示的属性
            $n AND $arrlist = well_website_flag_find_by_fid_display($fid, $display, $page, $pagesize);

        } else {

            $n = well_website_flag_count($fid);

            // 启用了自定义排序
            $orderby AND $n AND $pagesize = $n;

            // 所有属性
            $n AND $arrlist = well_website_flag_find($fid, $page, $pagesize);
        }

        // hook website_admin_flag_list_get_before.php

        if ($orderby && $n) {
            $arrlist = well_array_multisort_key($arrlist, 'rank', FALSE, 'flagid');
            $arrlist = well_array_pagination($arrlist, $page, 25);
        }

        // hook website_admin_flag_list_get_after.php

        $pagination = pagination(url('flag-list-' . $fid . '-{page}', $extra), $n, $page, $pagesize);

        $header['title'] = lang('well_flag');
        $header['mobile_title'] = lang('well_flag');

        // hook website_admin_flag_list_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/flag_list.htm');

    } elseif ($method == 'POST') {

        $type = param('type', 0);

        // hook website_admin_flag_list_post_start.php

        if ($type == 1) {

            $arr = _POST('data');

            // hook website_admin_flag_list_rank_post_start.php

            if (!empty($arr)) {
                // hook website_admin_flag_list_rank_post_before.php
                foreach ($arr as &$val) {
                    $rank = intval($val['rank']);
                    $flagid = intval($val['flagid']);
                    intval($val['oldrank']) != $rank && $flagid AND $r = well_website_flag_update($flagid, array('rank' => $rank));
                    // hook website_admin_flag_list_rank_post_foreach.php
                }
                // hook website_admin_flag_list_rank_post_after.php
            }

            // hook website_admin_flag_list_rank_post_end.php

            message(0, lang('update_successfully'));

        } else {

            // 删除
            $fid = param(2, 0);
            $flagid = param(3, 0);
            !$flagid AND message(1, lang('data_malformation'));

            // hook website_admin_flag_list_post_before.php

            if ($fid) {
                $forum = forum_read($fid);

                $flagarr = $forum['well_flag'] ? explode(',', $forum['well_flag']) : array();
                $key = array_search($flagid, $flagarr);
                unset($flagarr[$key]);
                $flagstr = implode(',', $flagarr);
                $flagstr = trim($flagstr, ',');
                forum_update($fid, $arr = array('well_flag' => $flagstr));
            }

            // hook website_admin_flag_list_post_after.php

            // 清空主题 大数据量有可能超时 此处暂时这样处理，以后优化再改成遍历主键删除
            $r = well_website_flag_thread_delete_by_flagid($flagid);
            $r === FALSE AND message(-1, lang('delete_failed'));

            $r = well_website_flag_delete($flagid);
            $r === FALSE AND message(-1, lang('delete_failed'));

            $iconfile = '../upload/website_flag/' . $flagid . '.png';
            file_exists($iconfile) AND unlink($iconfile);

            // hook website_admin_flag_list_post_end.php

            message(0, lang('delete_successfully'));
        }
    }
} elseif ($action == 'create') {

    // hook website_admin_flag_create_start.php

    if ($method == 'GET') {

        // hook website_admin_flag_create_get_start.php

        $fid = param(2, 0);
        $forum = forum_read($fid);

        // hook website_admin_flag_create_get_before.php

        $input = array();
        $input['name'] = form_text('name', '', FALSE, lang('well_flag'));
        $input['display'] = form_radio_yes_no('display');
        $input['number'] = form_text('number', '', FALSE, lang('well_display_number'));

        $mainpic = '../plugin/well_cms_x/view/image/nopic.png';

        // hook website_admin_flag_create_get_middle.php

        $breadcrumb_flag = lang('well_add_flag');
        $disabled = '';
        $form_action = url('flag-create-' . $fid);

        // hook website_admin_flag_create_get_after.php

        $header['title'] = lang('well_add_flag') . '-' . ($fid ? $forum['name'] : lang('well_flag'));
        $header['mobile_title'] = lang('well_add_flag') . '-' . ($fid ? $forum['name'] : lang('well_flag'));

        // hook website_admin_flag_create_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/flag_post.htm');

    } elseif ($method == 'POST') {

        // hook website_admin_flag_create_post_start.php

        $fid = param('fid', 0);
        $name = param('name', '', FALSE);

        $name = trim($name);
        $name = strip_tags($name);
        !$name AND message('name', lang('well_flag_empty'));

        $name = htmlspecialchars($name);
        // 查询该属性是否存在
        $read = well_website_flag_read_by_name_and_fid_cache($name, $fid);
        $read AND message('name', lang('well_flag_existed'));

        // hook website_admin_flag_create_post_before.php

        $display = param('display', 0);
        $number = param('number', 10);

        $flagarr = array();
        if ($display && $fid) {
            $forum = array_value($forumlist, $fid);
            $flagarr = explode(',', $forum['well_flag']);
            $n = count($flagarr);
            $n >= 10 AND message(1, lang('well_display limit_n'));
        }

        $delete = param('delete', 0);
        $icon = param('icon');


        // hook website_admin_flag_create_post_middle.php

        $number = $display ? $number : 0;
        $arr = array('name' => $name, 'fid' => $fid, 'display' => $display, 'number' => $number, 'create_date' => $time);

        $delete == 0 AND $icon AND $arr['icon'] = $time;

        // hook website_admin_flag_create_post_array.php

        $flagid = well_website_flag_create($arr);
        $flagid === FALSE AND message(-1, lang('well_create_failed'));

        if ($delete == 0 && $icon) {
            $data = substr($icon, strpos($icon, ',') + 1);
            $data = base64_decode($data);
            $path = '../upload/website_flag/';
            !is_dir($path) AND mkdir($path, 0777, TRUE);
            $iconfile = $path . $flagid . '.png';
            file_put_contents($iconfile, $data);
        }

        // hook website_admin_flag_create_post_after.php

        if ($display && $fid) {
            $flagarr[] = $flagid;
            $flagstr = implode(',', $flagarr);
            $flagstr = trim($flagstr, ',');
            forum_update($fid, $arr = array('well_flag' => $flagstr));
        }

        // hook website_admin_flag_create_post_end.php

        message(0, lang('well_create_sucessfully'));
    }

} elseif ($action == 'update') {

    // hook website_admin_flag_update_start.php

    $fid = param(2, 0);
    $forum = array_value($forumlist, $fid);

    // hook website_admin_flag_update_before.php

    $flagid = param(3, 0);

    $read = well_website_flag_read($flagid);
    !$read AND message(-1, lang('well_flag_empty'));

    // hook website_admin_flag_update_end.php

    if ($method == 'GET') {

        // hook website_admin_flag_update_get_start.php

        $input = array();
        $input['name'] = form_text('name', $read['name'], FALSE, lang('well_flag'));
        $input['display'] = form_radio_yes_no('display', $read['display']);
        $input['number'] = form_text('number', $read['number'], FALSE, lang('well_display_number'));

        $mainpic = $read['icon'] ? '../upload/website_flag/' . $flagid . '.png' : '../plugin/well_cms_x/view/image/nopic.png';

        // hook website_admin_flag_update_get_before.php

        $breadcrumb_flag = lang('well_edit') . lang('well_flag');
        $disabled = 'disabled="disabled"';
        $form_action = url('flag-update-' . $fid . '-' . $flagid);

        // hook website_admin_flag_update_get_after.php

        $header['title'] = lang('well_edit') . lang('well_flag');
        $header['mobile_title'] = lang('well_edit') . lang('well_flag');

        // hook website_admin_flag_update_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/flag_post.htm');

    } elseif ($method == 'POST') {

        // hook website_admin_flag_update_post_start.php

        $update = array();
        $name = param('name', '', FALSE);
        $name = strip_tags($name);
        $name = htmlspecialchars($name);
        if ($name && $name != $read['name']) {
            // 查询该属性是否存在
            $r = well_website_flag_read_by_name_and_fid_cache($name, $read['fid']);
            $r AND message('name', lang('well_flag_existed'));

            $update['name'] = $name;
        }

        // hook website_admin_flag_update_post_before.php

        $display = param('display', 0);
        // 原来显示 现在不显示 清理
        if ($display != $read['display']) {
            // 非首页 配置了栏目
            if ($read['fid']) {
                $forum = array_value($forumlist, $read['fid']);
                $flagarr = $forum['well_flag'] ? explode(',', $forum['well_flag']) : array();

                if ($read['display']) {
                    $key = array_search($read['flagid'], $flagarr);
                    unset($flagarr[$key]);
                } else {
                    // 改为显示 追加
                    $n = count($flagarr);
                    $n >= 10 AND message(1, lang('well_display limit_n'));
                    $flagarr[] = $read['flagid'];
                }

                $flagstr = implode(',', $flagarr);
                $flagstr = trim($flagstr, ',');
                forum_update($fid, $arr = array('well_flag' => $flagstr));
            }

            $update['display'] = $display;
        }

        // hook website_admin_flag_update_post_middle.php

        $number = param('number', 10);
        $update['number'] = $display ? $number : 0;

        $delete = param('delete', 0);
        if ($delete) {
            $update['icon'] = 0;
            $iconfile = '../upload/website_flag/' . $flagid . '.png';
            file_exists($iconfile) AND unlink($iconfile);
        }

        $icon = param('icon');
        if ($delete == 0 && $icon) {
            $update['icon'] = $time;
            $data = substr($icon, strpos($icon, ',') + 1);
            $data = base64_decode($data);
            $path = '../upload/website_flag/';
            !is_dir($path) AND mkdir($path, 0777, TRUE);
            $iconfile = $path . $flagid . '.png';
            file_put_contents($iconfile, $data);
        }

        // hook website_admin_flag_update_post_after.php

        !empty($update) AND well_website_flag_update($read['flagid'], $update) === FALSE AND message(-1, lang('well_update_failed'));

        // hook website_admin_flag_update_post_end.php

        message(0, lang('well_update_sucessfully'));
    }

} elseif ($action == 'read') {

    // hook website_admin_flag_read_start.php

    $flagid = param(2, 0);
    $read = well_website_flag_read($flagid);
    !$read AND message(-1, lang('well_flag_empty'));

    // hook website_admin_flag_read_end.php

    if ($method == 'GET') {

        // hook website_admin_flag_read_get_start.php

        $page = param(3, 1);
        $pagesize = 25;
        // 插件预留
        $extra = array();

        // hook website_admin_flag_read_get_before.php

        //$n = well_website_flag_thread_count_by_flagid($flagid);
        if ($read['count']) {

            $arrlist = well_website_flag_thread_find_by_flagid($flagid, $page, $pagesize);

            // hook website_admin_flag_read_get_flag_after.php

            $idarr = arrlist_key_values($arrlist, 'tid', 'id');
            $tidarr = arrlist_values($arrlist, 'tid');
            // 遍历flag所有主题

            // hook website_admin_flag_read_get_thread_before.php

            $threadlist = well_thread_find($tidarr, $pagesize);

            // hook website_admin_flag_read_get_thread_before.php
        }

        // hook website_admin_flag_read_get_before.php

        $pagination = pagination(url('flag-read-' . $flagid . '-{page}', $extra), $read['count'], $page, $pagesize);

        // hook website_admin_flag_read_get_after.php

        $header['title'] = lang('well_flag_list');
        $header['mobile_title'] = lang('well_flag_list');

        // hook website_admin_flag_read_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/flag_read_list.htm');

    } elseif ($method == 'POST') {

        // hook website_admin_flag_read_post_start.php

        $type = param('type', 0);
        $type AND $id = param('id', array());
        !$type AND $id = param(3, 0);
        !$id AND message(1, lang('data_malformation'));

        // hook website_admin_flag_read_post_before.php

        $n = ($type && !empty($id)) ? count($id) : 1;

        // hook website_admin_flag_read_post_middle.php

        // 删除的是同一个flag下的主题
        well_website_flag_update($flagid, array('count-' => $n));

        // hook website_admin_flag_read_post_after.php

        $r = well_website_flag_thread_delete($id);
        $r === FALSE AND message(-1, lang('delete_failed'));

        // hook website_admin_flag_read_post_end.php

        message(0, lang('delete_successfully'));
    }
}

// hook website_admin_flag_end.php

?>