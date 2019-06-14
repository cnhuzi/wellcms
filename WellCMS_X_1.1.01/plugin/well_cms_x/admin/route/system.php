<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

!defined('DEBUG') AND exit('Access Denied.');

// hook website_admin_system_start.php

$action = param(1, 'setting');

if ($action == 'setting') {

    // hook website_admin_system_setting_start.php

    $website_conf = GLOBALS('website_conf');
    $setting = array_value($website_conf, 'setting');

    if ($method == 'GET') {

        $header['title'] = lang('setting');
        $header['mobile_title'] = lang('setting');
        $header['mobile_link'] = url('system-setting');

        // hook website_admin_system_setting_get_start.php

        $input = array();
        $website_modearr = array('0' => lang('well_custom'), '1' => lang('well_portal'), '2' => lang('well_flat'));
        // hook website_admin_system_setting_get_website_mode_before.php
        $input['website_mode'] = form_radio('website_mode', $website_modearr, array_value($setting, 'website_mode', 0));

        $tpl_modearr = array('0' => lang('well_website_mode_0'), '1' => lang('well_website_mode_1'), '2' => lang('well_website_mode_2'));
        // hook website_admin_system_setting_get_tpl_mode_before.php
        $input['tpl_mode'] = form_radio('tpl_mode', $tpl_modearr, array_value($setting, 'tpl_mode', 0));

        // hook website_admin_system_setting_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/system_setting.htm');

    } elseif ($method == 'POST') {

        // hook website_admin_system_setting_post_start.php

        $website_mode = param('website_mode', 0);
        $tpl_mode = param('tpl_mode', 0);

        // hook website_admin_system_setting_post_before.php

        $setting['website_mode'] = $website_mode;
        $setting['tpl_mode'] = $tpl_mode;

        // 模式更改删除缓存
        array_value($setting, 'website_mode', 0) != $website_mode AND cache_delete('website_index_list');

        // hook website_admin_system_setting_post_center.php

        $website_conf['setting'] = $setting;

        // hook website_admin_system_setting_post_after.php

        setting_set('well_website_conf', $website_conf);

        // hook website_admin_system_setting_post_end.php

        message(0, lang('modify_successfully'));
    }

} elseif ($action == 'map') {

    // hook website_admin_system_map_start.php

    $website_conf = GLOBALS('website_conf');
    $setting = array_value($website_conf, 'setting');

    if ($method == 'GET') {

        $header['title'] = lang('well_map');
        $header['mobile_title'] = lang('well_map');
        $header['mobile_link'] = url('system-map');

        // hook website_admin_system_map_get_start.php

        // 1生成地图
        $type = param('type', 0);

        if ($type == 1) {

            // hook website_admin_system_map_xml_start.php

            !file_exists(array_value($setting, 'map', 'sitemap')) AND xn_mkdir(APP_PATH . array_value($setting, 'map', 'sitemap'), 0777);

            $page = param('page', 0); // 当前页数
            $n = param('n', 0); // 总页数
            $pagesize = 40000;
            $fids = param('fids');
            $fid = param('fid', 0);

            $forum_xml = $xml = '';
            $lastmod = date('Y-m-d');

            // hook website_admin_system_map_xml_before.php

            $dir = array_value($setting, 'map', 'sitemap') . '/';

            // hook website_admin_system_map_xml_middle.php

            $forumlist_show = well_return_column($forumlist);

            // hook website_admin_system_map_xml_after.php

            if (!empty($forumlist_show) && !$fids) {
                // 生成栏目索引

                $fids = '';
                foreach ($forumlist_show as $_forum) {

                    if (!$_forum['threads']) continue;

                    $fids .= $_forum['fid'] . '|';

                    $n = ceil($_forum['threads'] / $pagesize);

                    //--------------生成栏目索引---------------
                    for ($i = 0; $i < $n; ++$i) {
                        $forum_xml .= '    <loc>' . well_return_domain() . '/' . $dir . 'forum-' . $_forum['fid'] . '-' . $i . '.xml</loc>
    <lastmod>' . $lastmod . '</lastmod>
    <changefreq>daily</changefreq>
    <priority>0.8</priority>' . "\r\n";
                    }

                    if ($forum_xml) {
                        $forum_xml = trim($forum_xml, "\r\n");
                        $forum_map = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<sitemapindex>
    <sitemap>
{$forum_xml}
    </sitemap>
</sitemapindex>
</urlset>
EOT;

                        file_put_contents_try(APP_PATH . $setting['map'] . '/index.xml', $forum_map);
                    }
                }

                $thread = rtrim($fids, '|');

                message(0, jump('Create a section index', url('system-map', array('type' => 1, 'fids' => rtrim($fids, '|'))), 1));

            } else {

                $fidarr = explode('|', $fids);
                !$fid AND $fid = $fidarr[0];

                // 按照栏目生成内容索引 可以创建已生成标识，不用重复生成旧数据，VIP版再添加吧

                $forum = $forumlist_show[$fid];
                !$n AND $n = ceil($forum['threads'] / $pagesize);

                $arrlist = well_thread_tid_find_by_fid($fid, $page, $pagesize, FALSE);

                foreach ($arrlist as $_thread) {
                    $xml .= '    <url>
        <loc>' . well_return_domain() . '/' . well_url_format($fid, $_thread['tid']) . '</loc>
        <mobile:mobile type="pc,mobile"/>
        <lastmod>' . $lastmod . '</lastmod>
        <changefreq>daily</changefreq>
        <priority>0.8</priority>
    </url>' . "\r\n";
                }

                if ($xml) {
                    $xml = trim($xml, "\r\n");
                    $map = <<<EOT
<?xml version="1.0" encoding="utf-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
{$xml}
</urlset>
EOT;

                    file_put_contents_try(APP_PATH . $setting['map'] . '/forum-' . $fid . '-' . $page . '.xml', $map);
                }

                $page += 1;
                if ($page == $n) {
                    $page = 0;
                    $fid = array_value($fidarr, 1, 0);
                    $n = 0;
                    unset($fidarr[0]);

                    $fidarr = array_filter($fidarr);
                    empty($fidarr) AND message(0, jump('Complete', url('system-map'), 2));
                }
                $fids = implode('|', $fidarr);

                message(0, jump('Forum : ' . $forum['name'] . ' Number : ' . ($page + 1), url('system-map', array('type' => 1, 'fid' => $fid, 'fids' => $fids, 'n' => $n, 'page' => $page)), 1));

            }

            // hook website_admin_system_map_xml_end.php

        } else {

            $input = array();
            $input['map'] = form_text('map', array_value($setting, 'map', 'sitemap'), FALSE, lang('well_website_map_tips'));

            // hook website_admin_system_map_get_before.php

            $arr = array();
            if (array_value($setting, 'map')) {
                foreach (glob('../' . array_value($setting, 'map') . '/' . '*.*') as $file) {
                    $arr[] = well_return_domain() . '/' . str_replace('../', '', $file);
                }
            }
        }

        // hook website_admin_system_map_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/system_map.htm');

    } elseif ($method == 'POST') {

        // hook website_admin_system_map_post_start.php

        $map = param('map', '', FALSE);
        // 英文 数字 下划线及三种组合 不支持其他字符
        !preg_match('/^[a-z\d_]*$/i', $map) AND message(1, lang('well_english_number_tips'));

        // hook website_admin_system_map_post_before.php

        $setting['map'] = $map;

        // hook website_admin_system_map_post_middle.php

        $website_conf['setting'] = $setting;

        // hook website_admin_system_map_post_after.php

        setting_set('well_website_conf', $website_conf);

        // hook website_admin_system_map_post_end.php

        message(0, lang('well_operate_successfully'));
    }

} elseif ($action == 'increase') {

    $website_conf = GLOBALS('website_conf');
    $installed = array_value($website_conf, 'installed');

    $type = param('type', 0);

    if ($method == 'GET') {

        $page = param('page', 0);
        $page += 1;
        $n = param('n', 0);
        $fid = param('fid', 0);
        $tid = param('tid', 0);

        if ($n == 1) {
            $count = 250;
        } elseif ($n == 2) {
            $count = 500;
        } elseif ($n == 3) {
            $count = 2500;
        } elseif ($n == 4) {
            $count = 5000;
        } else {
            $count = 50;
        }

        if ($type == 1 && $page <= $count) {

            !$fid AND message(0, jump('No section', url('system-increase'), 1));

            // 投入正常运营的站点不能灌水
            $runtime['website_threads'] AND $installed == 0 AND message(0, lang('create_failed'));

            if (!$tid) {
                $tid = well_thread_maxid();
                $tid += 1;
            }

            $number = $tid + 20000;
            $subject = 'WellCMS X 性能测试';
            $message = 'WellCMS X 性能测试';
            $thread = $thread_tid = $data = '';
            for ($tid; $tid < $number; ++$tid) {
                $thread .= '(' . $tid . ',' . $fid . ',"' . $subject . '",' . $uid . ',' . $time . '),';
                $thread_tid .= '(' . $tid . ',' . $fid . ',' . $uid . '),';
                $data .= '(' . $tid . ',"' . $message . '"),';
            }

            $thread = rtrim($thread, ',');
            $r = db_exec("REPLACE INTO `{$db->tablepre}website_thread` (`tid`,`fid`,`subject`,`uid`,`create_date`) VALUES $thread");
            $r === FALSE AND message(-1, 'Create thread error');

            $thread_tid = rtrim($thread_tid, ',');
            $r = db_exec("REPLACE INTO `{$db->tablepre}website_thread_tid` (`tid`,`fid`,`uid`) VALUES $thread_tid");
            $r === FALSE AND message(-1, 'Create thread tid error');

            $data = rtrim($data, ',');
            $r = db_exec("REPLACE INTO `{$db->tablepre}website_data` (`tid`,`message`) VALUES $data");
            $r === FALSE AND message(-1, 'Create data error');

            forum_update($fid, array('threads+' => 20000));

            // 灌水标识
            if ($installed == 0) {
                $website_conf['installed'] = 1;
                setting_set('well_website_conf', $website_conf);
            }

            message(0, jump('Number : ' . $page . '<br><br>threads : ' . ($page * 20000), url('system-increase', array('type' => 1, 'fid' => $fid, 'n' => $n, 'tid' => $tid, 'page' => $page)), 1));

        } else {
            $website_forumlist = well_return_column($forumlist);
        }

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/system_increase.htm');

    } elseif ($method == 'POST') {

        if ($type == 1) {

            db_exec("TRUNCATE  `{$db->tablepre}website_thread`");
            db_exec("TRUNCATE  `{$db->tablepre}website_thread_tid`");
            db_exec("TRUNCATE  `{$db->tablepre}website_data`");
            db_exec("TRUNCATE  `{$db->tablepre}website_attach`");
            db_exec("TRUNCATE  `{$db->tablepre}website_flag`");
            db_exec("TRUNCATE  `{$db->tablepre}website_flag_thread`");
            db_exec("TRUNCATE  `{$db->tablepre}website_modelog`");
            db_exec("TRUNCATE  `{$db->tablepre}website_post`");
            db_exec("TRUNCATE  `{$db->tablepre}website_post_pid`");
            db_exec("TRUNCATE  `{$db->tablepre}website_thread_top`");

            cache_truncate();
            //$runtime = NULL;

            $fids = array();
            foreach ($forumlist as $val) {
                $fids[] = $val['fid'];
            }

            forum_update($fids, array('threads' => 0));

            // 灌水标识
            if ($installed == 1) {
                $website_conf['installed'] = 0;
                setting_set('well_website_conf', $website_conf);
            }
        }

        message(0, lang('well_operate_successfully'));
    }
} elseif ($action == 'optimize') {

    if($method == 'GET') {

        // hook website_admin_system_optimize_get_start.php

        $input = array();
        $input['optimize'] = form_checkbox('optimize', 1);

        // hook website_admin_system_optimize_get_end.php

        include _include(APP_PATH . 'plugin/well_cms_x/admin/view/htm/system_optimize.htm');

    } else {

        // hook website_admin_system_optimize_post_start.php

        $optimize = param('optimize');

        // hook website_admin_system_optimize_post_before.php

        $optimize AND db_sql_find_one("OPTIMIZE TABLE `{$db->tablepre}cache`, `{$db->tablepre}forum`, `{$db->tablepre}forum_access`, `{$db->tablepre}group`, `{$db->tablepre}kv`, `{$db->tablepre}session`, `{$db->tablepre}session_data`, `{$db->tablepre}user`, `{$db->tablepre}website_attach`, `{$db->tablepre}website_data`, `{$db->tablepre}website_flag`, `{$db->tablepre}website_flag_thread`, `{$db->tablepre}website_modelog`, `{$db->tablepre}website_post`, `{$db->tablepre}website_post_pid`, `{$db->tablepre}website_thread`, `{$db->tablepre}website_thread_tid`, `{$db->tablepre}website_thread_top`, `{$db->tablepre}website_tag`, `{$db->tablepre}website_tag_thread`");

        // hook website_admin_system_optimize_post_end.php

        message(0, lang('admin_clear_successfully'));
    }
}

// hook website_admin_system_end.php

?>