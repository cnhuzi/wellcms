<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
*/
!defined('DEBUG') AND exit('Access Denied.');

$action = param(1);

// hook website_attach_start.php

if ($action == 'website_create') {

    // hook website_attach_create_start.php

    $user = user_read($uid);
    user_login_check();

    // hook website_attach_create_check_after.php

    $admin_upload = param(2, 0);

    $width = param('width', 0);
    $height = param('height', 0);
    $name = param('name');
    $filetype = param('filetype');
    $data = param_base64('data');

    $convert = param('convert', 0); // 图片转换压缩 = 1
    $is_image = param('is_image', 0); // 图片
    $upload_file = param('upload_file', 0); // 1主图
    $n = param('n', 0); // 对应主图赋值
    $type = param('type', 0);// 根据模型定义

    // hook website_attach_create_before.php

    // 允许的文件后缀名
    //$types = include _include(APP_PATH.'conf/attach.conf.php');
    //$allowtypes = $types['all'];

    empty($group['allowattach']) AND $gid != 1 AND message(-1, '您无权上传');

    empty($data) AND message(-1, lang('data_is_empty'));
    //$data = base64_decode_file_data($data);
    $size = strlen($data);
    $size > 20480000 AND message(-1, lang('filesize_too_large', array('maxsize' => '20M', 'size' => $size)));

    // hook website_attach_create_file_ext_start.php

    // 获取文件后缀名 111.php.shtmll
    $ext = file_ext($name, 7);
    $filetypes = include APP_PATH . 'conf/attach.conf.php';

    // hook website_attach_create_file_ext_before.php

    // 主图必须为图片
    if ($is_image == 1 && $upload_file == 1 && !in_array($ext, $filetypes['image'])) {
        message(-1, lang('well_up_picture_error'));
    }

    // hook website_attach_create_file_ext_center.php

    // 如果文件后缀不在规定范围内 改变后缀名
    !in_array($ext, $filetypes['all']) AND $ext = '_' . $ext;
    $convert == 1 AND $is_image == 1 AND $ext = $filetype;

    // hook website_attach_create_file_ext_after.php

    $tmpanme = $uid . '_' . xn_rand(15) . '.' . $ext;

    // hook website_attach_create_tmpanme_after.php

    $tmpfile = $conf['upload_path'] . 'tmp/' . $tmpanme;

    // hook website_attach_create_tmpfile_after.php

    $tmpurl = $conf['upload_url'] . 'tmp/' . $tmpanme;

    // hook website_attach_create_tmpurl_after.php

    $filetype = well_attach_type($name, $filetypes);

    // hook website_attach_create_save_before.php

    file_put_contents($tmpfile, $data) OR message(-1, lang('write_to_file_failed'));

    // hook website_attach_create_save_after.php

    // 保存到 session，发帖成功以后，关联到帖子。
    // save attach information to session, associate to post after create thread.

    // 抛弃之前的 $_SESSION 数据，重新启动 session，降低 session 并发写入的问题
    // Discard the previous $_SESSION data, restart the session, reduce the problem of concurrent session write
    sess_restart();

    empty($_SESSION['tmp_website_files']) AND $_SESSION['tmp_website_files'] = array();

    // hook website_attach_create_after.php

    $type == 0 AND $n = count($_SESSION['tmp_website_files']);
    $filesize = filesize($tmpfile);
    $attach = array(
        'url' => ($admin_upload ? '../' : '') . $tmpurl,
        'path' => $tmpfile,
        'admin' => $admin_upload,
        'orgfilename' => $name,
        'filetype' => $filetype,
        'filesize' => $filesize,
        'width' => $width,
        'height' => $height,
        'isimage' => $is_image,
        'downloads' => 0,
        'aid' => '_' . $n
    );

    // hook website_attach_create_array_after.php

    if ($type == 1) {
        // hook website_attach_create_mainpic_beofre.php
        $_SESSION['tmp_mainpic'] = $attach;
    } else {
        // hook website_attach_create_website_files_beofre.php
        $_SESSION['tmp_website_files'][$n] = $attach;
    }

    // hook website_attach_create_session_after.php

    unset($attach['path']);

    // hook website_attach_create_end.php

    message(0, $attach);

} elseif ($action == 'website_delete') {

    // hook website_attach_delete_start.php

    $user = user_read($uid);
    user_login_check();

    $aid = param(2);

    // hook website_attach_delete_before.php

    // 临时的文件 id / temp attach id : _0 _1 _2 _3 ...
    if (substr($aid, 0, 1) == '_') {
        $key = intval(substr($aid, 1));
        $tmp_files = _SESSION('tmp_website_files');
        !isset($tmp_files[$key]) AND message(-1, lang('item_not_exists', array('item' => $key)));
        $attach = $tmp_files[$key];
        !is_file($attach['path']) AND message(-1, lang('file_not_exists'));
        unlink($attach['path']);
        unset($_SESSION['tmp_website_files'][$key]);
    } else {
        $aid = intval($aid);
        $attach = well_attach_read($aid);
        empty($attach) AND message(-1, lang('attach_not_exists'));

        $thread = well_thread_read($attach['tid']);
        empty($thread) AND message(-1, lang('thread_not_exists'));
        $fid = $thread['fid'];

        $allowdelete = forum_access_mod($fid, $gid, 'allowdelete');
        $attach['uid'] != $uid AND !$allowdelete AND message(0, lang('insufficient_privilege'));

        $r = well_attach_delete($aid);
        $r === FALSE AND message(-1, lang('delete_failed'));
    }

    // hook website_attach_delete_end.php

    message(0, 'delete_successfully');

} elseif ($action == 'website_download') {

    // hook website_attach_download_start.php

    // 判断权限
    $aid = param(2, 0);
    $attach = well_attach_read($aid);
    empty($attach) AND message(-1, lang('attach_not_exists'));
    $tid = $attach['tid'];
    $thread = well_thread_read($tid);
    $fid = $thread['fid'];
    $allowdown = forum_access_user($fid, $gid, 'allowdown');
    empty($allowdown) AND message(-1, lang('insufficient_privilege_to_download'));

    // hook website_attach_output_before.php

    $attachpath = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
    $attachurl = $conf['upload_url'] . 'website_attach/' . $attach['filename'];
    !is_file($attachpath) AND message(-1, lang('attach_not_exists'));

    $type = 'php';

    // hook website_attach_output_after.php

    // php 输出
    if ($type == 'php') {

        // hook website_attach_download_update_before.php

        well_attach_update($aid, array('downloads+' => 1));

        // hook website_attach_download_update_after.php

        $filesize = $attach['filesize'];
        if (stripos($_SERVER["HTTP_USER_AGENT"], 'MSIE') !== FALSE || stripos($_SERVER["HTTP_USER_AGENT"], 'Edge') !== FALSE || stripos($_SERVER["HTTP_USER_AGENT"], 'Trident') !== FALSE) {
            $attach['orgfilename'] = urlencode($attach['orgfilename']);
            $attach['orgfilename'] = str_replace("+", "%20", $attach['orgfilename']);
        }
        $timefmt = date('D, d M Y H:i:s', $time) . ' GMT';
        header('Date: ' . $timefmt);
        header('Last-Modified: ' . $timefmt);
        header('Expires: ' . $timefmt);
        // header('Cache-control: max-age=0, must-revalidate, post-check=0, pre-check=0');
        header('Cache-control: max-age=86400');
        header('Content-Transfer-Encoding: binary');
        header("Pragma: public");
        header('Content-Disposition: attachment; filename="' . $attach['orgfilename'] . '"');
        header('Content-Type: application/octet-stream');
        //header("Content-Type: application/force-download"); // 后面的会覆盖前面

        // hook website_attach_download_readfile_before.php

        readfile($attachpath);

        /*if($attach['filetype'] == 'image') {
            // ie6 下会解析图片内容！
            //header('Content-Disposition: inline; filename='.$attach['orgfilename']);
            //header('Content-Type: image/pjpeg');
        } else {
            header('Content-Disposition: attachment; filename='.$attach['orgfilename']);
            header('Content-Type: application/octet-stream');
        }*/
        exit;
    } else {

        // hook website_attach_download_location_before.php

        http_location($attachurl);
    }
}

// hook website_attach_end.php

?>