<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
// hook model_website_data_start.php

// ------------> 原生CURD，无关联其他数据。
function well_data__create($arr = array(), $d = NULL)
{
    // hook model_website_data__create_start.php
    $r = db_insert('website_data', $arr, $d);
    // hook model_website_data__create_end.php
    return $r;
}

function well_data__update($tid, $update = array(), $d = NULL)
{
    // hook model_website_data__update_start.php
    $r = db_update('website_data', array('tid' => $tid), $update, $d);
    // hook model_website_data__update_end.php
    return $r;
}

function well_data__read($cond = array(), $orderby = array(), $col = array(), $d = NULL)
{
    // hook model_website_data__read_start.php
    $r = db_find_one('website_data', $cond, $orderby, $col, $d);
    // hook model_website_data__read_end.php
    return $r;
}

function well_data__find($cond = array(), $orderby = array(), $page = 1, $pagesize = 20, $key = 'tid', $col = array(), $d = NULL)
{
    // hook model_website_data__find_start.php
    $arr = db_find('website_data', $cond, $orderby, $page, $pagesize, $key, $col, $d);
    // hook model_website_data__find_end.php
    return $arr;
}

function well_data__delete($tid, $d = NULL)
{
    // hook model_website_data__delete_start.php
    $r = db_delete('website_data', array('tid' => $tid), $d);
    // hook model_website_data__delete_end.php
    return $r;
}

//--------------------------强相关--------------------------
// $arr = array('tid' => $tid, 'gid' => $gid, 'message' => $arr['message'], 'doctype' => $doctype);
function well_data_create($arr)
{
    // hook model_website_data_create_start.php

    if (empty($arr)) return FALSE;

    // hook model_website_data_create_before.php

    well_data_message_format($arr);

    // hook model_website_data_create_after.php

    $r = well_data__create($arr);

    // hook model_website_data_create_end.php

    return $r;
}

// 更新 $update = array('tid' => $tid, 'gid' => $gid, 'message' => $arr['message'], 'doctype' => $doctype);
function well_data_update($tid, $update)
{
    // hook model_website_data_update_start.php

    if (!$tid || empty($update)) return FALSE;
    $conf = GLOBALS('conf');

    // hook model_website_data_update_before.php

    well_data_message_format($update);

    // hook model_website_data_update_center.php

    $r = well_data__update($tid, $update);
    if ($r === FALSE) return FALSE;

    // hook model_website_data_update_after.php

    $conf['cache']['type'] != 'mysql' AND cache_delete('website_data_' . $tid);
    // hook model_website_data_update_end.php

    return $r;
}

// 单次查询 tid
function well_data_read($tid)
{
    if (!$tid) return NULL;

    // hook model_website_data_read_by_tid_start.php

    $r = well_data__read(array('tid' => $tid));
    $r AND well_data_format($r);

    // hook model_website_data_read_by_tid_end.php

    return $r;
}

function well_data_find($tidarr, $pagesize = 20)
{
    if (!$tidarr) return NULL;

    // hook model_website_data_find_start.php

    $arrlist = well_data__find(array('tid' => $tidarr), array('tid' => -1), 1, $pagesize);

    // hook model_website_data_find_end.php

    return $arrlist;
}

// 主键删除
function well_data_delete($tid)
{
    // hook model_website_data_delete_start.php

    $conf = GLOBALS('conf');
    if (!$tid) return FALSE;

    // hook model_website_data_delete_before.php

    $r = well_data__delete($tid);
    if ($r === FALSE) return FALSE;

    if ($conf['cache']['type'] != 'mysql') cache_delete('website_data_' . $tid);

    // hook model_website_data_delete_end.php

    return $r;
}

function well_data_format(&$val)
{
    // hook model_website_data_format_start.php
    if (empty($val)) return;
    //$r['message'] = stripslashes(htmlspecialchars_decode($r['message']));
    // hook model_website_data_format_end.php
}

// 写入时格式化
function well_data_message_format(&$post)
{
    // hook model_website_data_message_format_start.php

    // 超长内容截取
    $post['message'] = xn_substr($post['message'], 0, 2028000);

    // hook model_website_data_message_format_beofre.php

    // 格式转换: 类型，0: html, 1: txt; 2: markdown; 3: ubb
    //$post['message'] = htmlspecialchars($post['message']);

    // 入库的时候进行转换
    $post['doctype'] == 0 && $post['message'] = ($post['gid'] == 1 ? $post['message'] : xn_html_safe($post['message']));
    $post['doctype'] == 1 && $post['message'] = xn_txt_to_html($post['message']);

    // hook model_website_data_message_format_after.php

    unset($post['gid']);

    // 对引用进行处理
    !empty($post['quotepid']) && $post['quotepid'] > 0 && $post['message'] = well_post_quote($post['quotepid']) . $post['message'];

    // hook model_website_data_message_format_end.php
}

// 公用的附件模板，采用函数，效率比 include 高。
function well_data_file_list_html($filelist, $include_delete = FALSE, $path = FALSE, $access = FALSE)
{
    if (empty($filelist)) return '';

    // hook model_website_data_file_list_html_start.php
    $path = $path === TRUE ? '../' : '';
    $s = '<fieldset class="fieldset">' . "\r\n";
    $s .= '<legend>上传的附件：</legend>' . "\r\n";
    $s .= '<ul class="attachlist">' . "\r\n";
    foreach ($filelist as &$attach) {
        $s .= '<li aid="' . $attach['aid'] . '">' . "\r\n";
        $s .= '		<a href="' . $path . ($access === TRUE ? $attach['url'] : url('attach-website_download-' . $attach['aid'])) . '" target="_blank">' . "\r\n";
        $s .= '			<i class="icon filetype ' . $attach['filetype'] . '"></i>' . "\r\n";
        $s .= '			' . $attach['orgfilename'] . "\r\n";
        $s .= '		</a>' . "\r\n";
        // hook model_website_post_file_list_html_delete_before.php
        $include_delete AND $s .= '		<a href="javascript:void(0)" class="delete ml-3"><i class="icon-remove"></i> ' . lang('delete') . '</a>' . "\r\n";
        // hook model_website_post_file_list_html_delete_after.php
        $s .= '</li>' . "\r\n";
    };
    $s .= '</ul>' . "\r\n";
    $s .= '</fieldset>' . "\r\n";

    // hook model_website_data_file_list_html_end.php

    return $s;
}

//--------------------------cache--------------------------
// 从缓存中读取，避免重复从数据库取数据
function well_data_read_cache($tid)
{
    $conf = GLOBALS('conf');
    // hook model_website_data_read_cache_start.php
    $key = 'website_data_' . $tid;
    static $cache = array(); // 用静态变量只能在当前 request 生命周期缓存，要跨进程，可以再加一层缓存： memcached/xcache/apc/
    if (isset($cache[$key])) return $cache[$key];
    if ($conf['cache']['type'] != 'mysql') {
        $r = cache_get($key);
        if ($r === NULL) {
            $r = well_data_read($tid);
            $r AND cache_set($key, $r, 3600); // 60分钟
        }
    } else {
        $r = well_data_read($tid);
    }
    $cache[$key] = $r ? $r : NULL;
    // hook model_website_data_read_cache_end.php
    return $cache[$key];
}

// hook model_website_data_end.php
?>