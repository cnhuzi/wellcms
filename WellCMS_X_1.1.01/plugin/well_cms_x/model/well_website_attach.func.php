<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// hook model_website_attach_start.php

// ------------> 最原生的 CURD，无关联其他数据

function well_attach__create($arr, $d = NULL)
{
    // hook model_website_attach__create_start.php
    $r = db_insert('website_attach', $arr, $d);
    // hook model_website_attach__create_end.php
    return $r;
}

function well_attach__update($aid, $arr, $d = NULL)
{
    // hook model_website_attach__update_start.php
    $r = db_update('website_attach', array('aid' => $aid), $arr, $d);
    // hook model_website_attach__update_end.php
    return $r;
}

function well_attach__read($aid, $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_attach__read_start.php
    $attach = db_find_one('website_attach', array('aid' => $aid), $orderby, $col, $d);
    // hook model_website_attach__read_end.php
    return $attach;
}

function well_attach__delete($aid, $d = NULL)
{
    // hook model_website_attach__delete_start.php
    $r = db_delete('website_attach', array('aid' => $aid), $d);
    // hook model_website_attach__delete_end.php
    return $r;
}

function well_attach__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = '', $col = array(), $d = NULL)
{
    // hook model_website_attach__find_start.php
    $attachlist = db_find('website_attach', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_attach__find_end.php
    return $attachlist;
}

function well_attach_count($cond = array(), $d = NULL)
{
    // hook model_website_attach_count_start.php
    $n = db_count('website_attach', $cond, $d);
    // hook model_website_attach_count_end.php
    return $n;
}

// ------------> 关联 CURD，主要是强相关的数据，比如缓存。弱相关的大量数据需要另外处理
function well_attach_create($arr)
{
    // hook model_website_attach_create_start.php
    $r = well_attach__create($arr);
    // hook model_website_attach_create_end.php
    return $r;
}

function well_attach_update($aid, $arr)
{
    // hook model_website_attach_update_start.php
    $r = well_attach__update($aid, $arr);
    // hook model_website_attach_update_end.php
    return $r;
}

function well_attach_read($aid)
{
    if (!$aid) return NULL;
    // hook model_website_attach_read_start.php

    $attach = well_attach__read($aid);

    $attach AND well_attach_format($attach);

    // hook model_website_attach_read_end.php

    return $attach;
}

function well_attach_delete($aid)
{
    // hook model_website_attach_delete_start.php

    $conf = GLOBALS('conf');
    $attach = well_attach_read($aid);

    $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
    file_exists($path) AND unlink($path);

    // hook model_website_attach_delete_after.php

    $r = well_attach__delete($aid);

    // hook model_website_attach_delete_end.php
    return $r;
}

function well_attach_find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20)
{
    // hook model_website_attach_find_start.php

    $attachlist = well_attach__find($cond, $orderby, $page, $pagesize);
    if (!$attachlist) return NULL;

    // hook model_website_attach_find_before.php

    foreach ($attachlist as &$attach) well_attach_format($attach);
    // hook model_website_attach_find_end.php

    return $attachlist;
}

// 获取 $filelist $imagelist
function well_attach_find_by_tid($tid)
{
    if (!$tid) return NULL;

    $imagelist = $filelist = array();

    // hook model_website_attach_find_by_tid_start.php

    $attachlist = well_attach__find(array('tid' => $tid), array(), 1, 1000);

    // hook model_website_attach_find_by_tid_before.php

    if ($attachlist) {
        foreach ($attachlist as $attach) {
            well_attach_format($attach);
            $attach['isimage'] ? ($imagelist[] = $attach) : ($filelist[] = $attach);
        }
    }

    // hook model_website_attach_find_by_tid_end.php

    return array($attachlist, $imagelist, $filelist);
}

function well_attach_delete_by_tid($tid)
{
    // hook model_website_attach_delete_by_tid_start.php

    $conf = GLOBALS('conf');

    list($attachlist, $imagelist, $filelist) = well_attach_find_by_tid($tid);

    // hook model_website_attach_delete_by_tid_before.php

    if (!empty($attachlist)) {
        foreach ($attachlist as $attach) {
            $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
            file_exists($path) AND unlink($path);
            well_attach__delete($attach['aid']);
        }
    }

    // hook model_website_attach_delete_by_tid_end.php

    return count($attachlist);
}

function well_attach_delete_by_uid($uid)
{
    // hook model_website_attach_delete_by_uid_start.php

    $conf = GLOBALS('conf');

    // hook model_website_attach_delete_by_uid_before.php

    $attachlist = well_attach__find(array('uid' => $uid), array(), 1, 9000);

    if (empty($attachlist)) return;

    // hook model_website_attach_delete_by_uid_after.php

    foreach ($attachlist as $attach) {
        $path = $conf['upload_path'] . 'website_attach/' . $attach['filename'];
        file_exists($path) AND unlink($path);
        well_attach__delete($attach['aid']);
    }

    // hook model_website_attach_delete_by_uid_end.php
}

// ------------> 其他方法
function well_attach_format(&$attach)
{
    // hook model_website_attach_format_start.php
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    if (empty($attach)) return;
    // hook model_website_attach_format_before.php
    $attach['create_date_fmt'] = date('Y-n-j', $attach['create_date']);
    $attach['url'] = $conf['upload_url'] . 'website_attach/' . $attach['filename'];
    // hook model_website_attach_format_end.php
}

function well_attach_type($name, $types)
{
    // hook model_website_attach_type_start.php
    $ext = file_ext($name);
    foreach ($types as $type => $exts) {
        if ($type == 'all') continue;
        if (in_array($ext, $exts)) {
            return $type;
        }
    }
    // hook model_website_attach_type_end.php
    return 'other';
}

// 扫描垃圾的附件，每日清理一次
function well_attach_gc()
{
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    // hook model_website_attach_gc_start.php
    $tmpfiles = glob($conf['upload_path'] . 'tmp/*.*');
    if (is_array($tmpfiles)) {
        foreach ($tmpfiles as $file) {
            // 清理超过一天还没处理的临时文件
            if ($time - filemtime($file) > 86400) {
                unlink($file);
            }
        }
    }
    // hook model_website_attach_gc_end.php
}

// 关联 session 中的临时文件，并不会重新统计 images, files
// type 0内容主图 1:内容图片或附件 8:节点主图 9:节点tag主图 教练套课主图
function well_attach_assoc_data(&$arr)
{
    // hook model_website_attach_assoc_start.php

    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    $uid = array_value($arr, 'uid');
    $tid = array_value($arr, 'tid');
    $type = array_value($arr, 'type', 0);

    // hook model_website_attach_assoc_before.php

    $sess_tmp_files = well_attach_assoc_type($type);
    //if (empty($sess_tmp_files)) return; // 内容中去掉图片时不删除旧图

    // hook model_website_attach_assoc_center.php

    $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');

    // hook model_website_attach_assoc_after.php

    if ($type == 0) {

        if (!empty($sess_tmp_files)) {
            // 主题主图
            $website_path = $conf['upload_path'] . 'website_mainpic';
            !is_dir($website_path) AND mkdir($website_path, 0777, TRUE);

            //$filename = file_name($sess_tmp_files['url']);
            // 获取文件后缀
            $ext = file_ext($sess_tmp_files['url']);
            $day = date($attach_dir_save_rule, $time);
            $path = $conf['upload_path'] . 'website_mainpic/' . $day;
            !is_dir($path) AND mkdir($path, 0777, TRUE);

            //$destfile = $path . '/' . $filename;
            // 主题ID.后缀
            $destfile = $path . '/' . $tid . '.' . $ext;
            $r = xn_copy($sess_tmp_files['path'], $destfile);
            !$r AND xn_log("xn_copy($sess_tmp_files[path]), $destfile) failed, tid:$tid", 'php_error');

            if (is_file($destfile) && filesize($destfile) == filesize($sess_tmp_files['path'])) {
                @unlink($sess_tmp_files['path']);
            }

            // 清空 session
            $_SESSION['tmp_mainpic'] = array();
            clearstatcache();
        }

    } elseif ($type == 1) {

        if (!empty($sess_tmp_files)) {
            $website_path = $conf['upload_path'] . 'website_attach';
            !is_dir($website_path) AND mkdir($website_path, 0777, TRUE);

            foreach ($sess_tmp_files as $file) {

                // hook model_website_attach_assoc_file_url_before.php

                // 内容附件 将文件移动到 upload/website_attach 目录
                $file_url = $file['admin'] ? str_replace('../upload/', 'upload/', $file['url']) : $file['url'];
                $filename = file_name($file_url);
                $day = date($attach_dir_save_rule, $time);
                $path = $conf['upload_path'] . 'website_attach/' . $day;
                $url = $conf['upload_url'] . 'website_attach/' . $day;
                !is_dir($path) AND mkdir($path, 0777, TRUE);

                // hook model_website_attach_assoc_file_url_after.php

                $destfile = $path . '/' . $filename;
                $desturl = $url . '/' . $filename;
                $r = xn_copy($file['path'], $destfile);
                !$r AND xn_log("xn_copy($file[path]), $destfile) failed, tid:$tid", 'php_error');
                if (is_file($destfile) && filesize($destfile) == filesize($file['path'])) {
                    @unlink($file['path']);
                }

                // hook model_website_attach_assoc_attach_arr_before.php

                $attach_arr = array(
                    'tid' => $tid,
                    'uid' => $uid,
                    'filesize' => $file['filesize'],
                    'width' => $file['width'],
                    'height' => $file['height'],
                    'filename' => "$day/$filename",
                    'orgfilename' => $file['orgfilename'],
                    'filetype' => $file['filetype'],
                    'create_date' => $time,
                    'comment' => '',
                    'downloads' => 0,
                    'isimage' => $file['isimage']
                );

                // hook model_website_attach_assoc_attach_create_before.php

                // 关联后内容再入库
                $aid = well_attach_create($attach_arr);
                $arr['message'] = str_replace($file['url'], $desturl, $arr['message']);

                // hook model_website_attach_assoc_replace_after.php

                // md5秒传还需要一个表对应aid插入md5值
            }

            // 清空 session
            $_SESSION['tmp_website_files'] = array();
        }

        // 处理不在 message 中的图片，删除掉没有插入的图片附件
        if ($arr['message']) {
            list($attachlist, $imagelist, $filelist) = well_attach_find_by_tid($tid);

            foreach ($imagelist as $key => $attach) {
                $url = $conf['upload_url'] . 'website_attach/' . $attach['filename'];
                // 搜索出现的位置 返回false表示无
                if (strpos($arr['message'], $url) === FALSE) {
                    unset($imagelist[$key]);
                    well_attach_delete($attach['aid']);
                }
            }

            $images = count($imagelist);
            $files = count($filelist);
            // 更新附件数
            if (array_value($arr, 'images') != $images || array_value($arr, 'files') != $files) {
                well_thread_update($tid, array('images' => $images, 'files' => $files));
                // hook model_website_attach_assoc_post_update_thread.php
            }
        }
    }

    // hook model_website_attach_assoc_end.php
}

// type 0内容主图 1:内容图片或附件 8:节点主图 9:节点tag主图 教练套课主图
function well_attach_assoc_type($type)
{
    // hook model_website_attach_assoc_type_start.php
    switch ($type) {
        case '0':
            $sess_tmp_files = _SESSION('tmp_mainpic');
            break;
        case '1':
            $sess_tmp_files = _SESSION('tmp_website_files');
            break;
        // hook model_website_attach_assoc_case_end.php
        default:
            $sess_tmp_files = _SESSION('tmp_mainpic');
            break;
    }
    // hook model_website_attach_assoc_type_end.php
    return $sess_tmp_files;
}

// Create main picture
function well_attach_create_mainpic($tid, $fid)
{
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    $forumlist = GLOBALS('forumlist');;
    $forum = array_value($forumlist, $fid);

    $website_conf = GLOBALS('website_conf');
    $picture = $website_conf['picture_size'];
    $picture = isset($forum['well_picture_size']) ? $forum['well_picture_size'] : $picture['picture_size'];
    $pic_width = $picture['width'];
    $pic_height = $picture['height'];

    $attachlist = well_attach_assoc_type(1);
    if (empty($attachlist)) return;

    $website_path = $conf['upload_path'] . 'website_mainpic';
    !is_dir($website_path) AND mkdir($website_path, 0777, TRUE);

    $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');

    $day = date($attach_dir_save_rule, $time);
    $path = $conf['upload_path'] . 'website_mainpic/' . $day;
    !is_dir($path) AND mkdir($path, 0777, TRUE);

    $tmp_file = APP_PATH . 'upload/tmp/' . $tid . '.jpeg';

    $i = 0;
    foreach ($attachlist as $val) {
        ++$i;
        if ($val['isimage'] == 1 && $i == 1) {
            well_image_clip_thumb($val['path'], $tmp_file, $pic_width, $pic_height);
            break;
        }
    }

    $ext = file_ext($tmp_file);

    $destfile = $path . '/' . $tid . '.' . $ext;

    $r = xn_copy($tmp_file, $destfile);

    !$r AND xn_log("xn_copy($tmp_file), $destfile) failed, tid:$tid", 'php_error');
}

function well_save_remote_image($arr)
{
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');
    $forumlist = GLOBALS('forumlist');;

    // hook model_website_save_remote_image_start.php

    $message = array_value($arr, 'message');
    $tid = array_value($arr, 'tid');
    $fid = array_value($arr, 'fid');
    $uid = array_value($arr, 'uid');
    $mainpic = array_value($arr, 'mainpic');

    $attach_dir_save_rule = array_value($conf, 'well_attach_dir_save_rule', 'Ym');

    $website_path = $conf['upload_path'] . 'website_attach';
    !is_dir($website_path) AND mkdir($website_path, 0777, TRUE);

    $day = date($attach_dir_save_rule, $time);
    $attach_dir = $conf['upload_path'] . 'website_attach/' . $day;
    $attach_url = $conf['upload_url'] . 'website_attach/' . $day;
    !is_dir($attach_dir) AND mkdir($attach_dir, 0777, TRUE);

    if ($mainpic) {
        $website_conf = GLOBALS('website_conf');
        $picture = $website_conf['picture_size'];
        $forum = array_value($forumlist, $fid);
        $picture = isset($forum['well_picture_size']) ? $forum['well_picture_size'] : $picture['picture_size'];
        $pic_width = $picture['width'];
        $pic_height = $picture['height'];

        $website_mainpic = $conf['upload_path'] . 'website_mainpic';
        !is_dir($website_mainpic) AND mkdir($website_mainpic, 0777, TRUE);
        $website_mainpic = $website_mainpic . '/' . $day;
        !is_dir($website_mainpic) AND mkdir($website_mainpic, 0777, TRUE);
        $tmp_file = APP_PATH . 'upload/tmp/' . $tid . '.jpeg';
        //$tmp_file = APP_PATH . $website_mainpic . '/' . $tid . '.jpeg';
    }

    $localurlarr = array(
        'http://' . $_SERVER['SERVER_NAME'] . '/',
        'https://' . $_SERVER['SERVER_NAME'] . '/',
    );

    // hook model_website_save_remote_image_before.php

    preg_match_all('#<img[^>]+src="(http://.*?)"#i', $message, $m);

    if (!empty($m[1])) {
        $n = 0;
        $i = 0;
        foreach ($m[1] as $url) {

            foreach ($localurlarr as $localurl) {
                if ($localurl == substr($url, 0, strlen($localurl))) continue 2;
            }

            // hook model_website_save_remote_image_imageurl_before.php

            $imageurl = well_get_image_url($url);
            $ext = file_ext($imageurl);

            // hook model_website_save_remote_image_imageurl_after.php

            if (!in_array($ext, array('gif', 'jpg', 'jpeg', 'png', 'bmp'))) continue;

            // hook model_website_save_remote_image_center.php

            $filename = xn_rand(16) . '.' . $ext;
            $destpath = $attach_dir . '/' . $filename;
            $desturl = $attach_url . '/' . $filename;
            $_message = str_replace($url, $desturl, $message);
            if ($message != $_message) {

                $imgdata = file_get_contents($url);
                $filesize = strlen($imgdata);
                if ($filesize < 10) continue;

                // hook model_website_save_remote_image_put_before.php

                file_put_contents_try($destpath, $imgdata);

                if ($mainpic) {
                    ++$i;
                    if ($i == 1) {
                        well_image_clip_thumb($destpath, $tmp_file, $pic_width, $pic_height);
                    }
                    $ext = file_ext($tmp_file);
                    $destmain = $website_mainpic . '/' . $tid . '.' . $ext;
                    $r = xn_copy($tmp_file, $destmain);
                    $r AND unlink($tmp_file);
                    $r AND well_thread_update($tid, array('icon' => $time));
                }

                // hook model_website_save_remote_image_put_after.php

                list($width, $height) = getimagesize($destpath);

                // hook model_website_save_remote_image_middle.php

                $attach = array(
                    'tid' => $tid,
                    'uid' => $uid,
                    'filesize' => $filesize,
                    'width' => $width,
                    'height' => $height,
                    'filename' => "$day/$filename",
                    'orgfilename' => $filename,
                    'filetype' => 'image',
                    'create_date' => $time,
                    'comment' => '',
                    'downloads' => 0,
                    'isimage' => 1
                );
                $aid = well_attach_create($attach);
                $n++;
            }

            $message = $_message;
        }

        // hook model_website_save_remote_image_after.php

        $n AND well_thread_update($tid, array('images+' => $n));
    }

    // hook model_website_save_remote_image_end.php

    return $message;
}

function well_get_image_url($url)
{
    if ($n = strpos($url, '.jpg')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.jpeg')) {
        $_n = $n + 5;
    } elseif ($n = strpos($url, '.png')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.gif')) {
        $_n = $n + 4;
    } elseif ($n = strpos($url, '.bmp')) {
        $_n = $n + 4;
    }

    $url = $n ? mb_substr($url, 0, $_n, 'UTF-8') : '';

    return $url;
}

// hook model_website_attach_end.php

?>