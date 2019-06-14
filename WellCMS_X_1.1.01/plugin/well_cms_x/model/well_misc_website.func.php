<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */

// hook model_misc_website_start.php

/*array(
    //list栏目已经rank排序
    'list' => array(
        'fid' => fid,
        'name' => name,
        'rank' => rank,
        'well_type' => well_type,
        'url' => url,
        'well_news' => well_news,
        'well_news' => well_news,
        'news' => 栏目下主题二数组,
    ),
    'flag' => array(
        'tid' => thread
    ),
    'top' => array(
        'tid' => thread
    )
);*/
function well_website_index_cate_thread_cache($forumlist)
{
    // hook model_website_index_category_thread_cache_start.php

    $key = 'website_index_list';
    static $cache = array(); // 跨进程，需再加一层缓存： redis/memcached/xcache/apc/

    if (isset($cache[$key])) return $cache[$key];

    // hook model_website_index_category_thread_cache_before.php

    $arr = cache_get($key);
    if ($arr === NULL) {
        $arr = well_website_index_cate_thread($forumlist);
        !empty($arr) AND cache_set($key, $arr);
    }

    // hook model_website_index_category_thread_cache_after.php

    $cache[$key] = !empty($arr) ? $arr : NULL;

    // hook model_website_index_category_thread_cache_end.php

    return $cache[$key];
}

// 门户 获取需要在首页显示的栏目主题数据
function well_website_index_cate_thread($forumlist)
{
    if (!$forumlist) return NULL;

    // hook model_website_index_category_thread_start.php

    $orderby = array('tid' => -1);
    $page = 1;

    // hook model_website_index_category_thread_before.php

    // 遍历所有在首页显示内容的栏目
    $index_forumlist = well_index_category($forumlist);

    $arrlist = $threadarr = $forum_tids = array();

    // hook model_website_index_category_thread_forumlist_start.php

    if ($index_forumlist) {
        foreach ($index_forumlist as &$_forum) {

            // hook model_website_index_category_thread_forumlist_before.php

            // 首页显示数据
            $arrlist['list'][$_forum['fid']] = array(
                'fid' => $_forum['fid'],
                'name' => $_forum['name'],
                'rank' => $_forum['rank'],
                'well_type' => $_forum['well_type'],
                'url' => $_forum['url'],
                'well_news' => $_forum['well_news'],
                // hook model_well_cms_index_forum_foreach.php
            );

            // hook model_website_index_category_thread_forumlist_after.php

            $forum_thread = well_thread_tid__find(array('fid' => $_forum['fid']), $orderby, $page, $_forum['well_news'], 'tid', array('tid'));
            // 最新信息按栏目分组
            foreach ($forum_thread as $_thread) {
                $forum_tids[] = $_thread['tid'];
            }

            // hook model_website_index_category_thread_forumlist_after.php

            unset($forum_thread);
        }
    }

    // hook model_website_index_category_thread_forumlist_end.php

    unset($forumlist);

    // hook model_website_index_category_thread_center.php

    // 获取属性
    $flaglist = well_website_find_flag();
    // 获取属性对应的tid集合
    $flaglist = well_website_flag_return_thread_tids($flaglist);
    $flagtidarr = array_value($flaglist, 'arr');
    $flagtids = array_value($flaglist, 'tids');
    unset($flaglist);

    // hook model_website_index_category_thread_flag_after.php

    // 全站置顶
    $toplist = well_website_all_top(3);
    $toptids = $toplist ? arrlist_values($toplist, 'tid') : array();

    // 合并tid
    $tidarr = array_merge($forum_tids, (array)$flagtids, $toptids);

    // hook model_website_index_category_thread_merge_after.php

    if (empty($tidarr)) return array();

    // hook model_website_index_category_thread_unique_before.php

    $tidarr = array_unique($tidarr);

    // hook model_website_index_category_thread_unique_after.php

    $pagesize = count($tidarr);

    // hook model_website_index_category_thread_find_before.php

    // 遍历获取的所有tid主题
    $threadlist = well_thread_find_asc($tidarr, $pagesize);

    // hook model_website_index_category_thread_find_after.php

    // 遍历时为升序，翻转为降序
    $threadlist = array_reverse($threadlist);

    // hook model_website_index_category_array_reverse_after.php

    foreach ($threadlist as &$_thread) {

        // hook model_website_index_category_thread_cate_before.php

        // 各栏目最新内容
        in_array($_thread['tid'], $forum_tids) AND $arrlist['list'][$_thread['fid']]['news'][$_thread['tid']] = $_thread;

        // 全站置顶内容
        !empty($toptids) AND in_array($_thread['tid'], $toptids) AND $arrlist['top'][$_thread['tid']] = $_thread;

        // 首页属性主题
        if (!empty($flagtidarr)) {
            foreach ($flagtidarr as $key => $val) {
                if (in_array($_thread['tid'], $val['tids'])) {
                    $arrlist['flagarr'][$key][$_thread['tid']] = $_thread;
                    $arrlist['flag'][$_thread['tid']] = $_thread;
                }
            }
        }

        // hook model_website_index_category_thread_cate_after.php
    }

    unset($threadlist);

    if (isset($arrlist['top'])) {
        $i = 0;
        foreach ($arrlist['top'] as &$val) {
            ++$i;
            $val['i'] = $i;
        }
    }

    if (isset($arrlist['flag'])) {
        $i = 0;
        foreach ($arrlist['flag'] as &$val) {
            ++$i;
            $val['i'] = $i;
        }
    }

    if (isset($arrlist['flagarr'])) {
        foreach ($arrlist['flagarr'] as &$val) {
            $i = 0;
            foreach ($val as &$v) {
                ++$i;
                $v['i'] = $i;
            }
        }
    }

    // hook model_website_index_category_thread_after.php

    isset($arrlist['list']) AND $arrlist['list'] = well_array_multisort_key($arrlist['list'], 'rank', TRUE, 'fid');

    // hook model_website_index_category_thread_end.php

    return $arrlist;
}

//-------------------category--------------------

// 门户 获取需要在频道显示的栏目主题数据
function well_website_channel_thread($fid)
{
    if (!$fid) return NULL;
    $forumlist = GLOBALS('forumlist');

    // hook model_website_index_category_thread_start.php

    $orderby = array('tid' => 1);
    $page = 1;

    // hook model_website_index_category_thread_before.php

    // 遍历所有在频道显示内容的栏目
    $category_forumlist = well_category_show($fid);

    $arrlist = $threadarr = $forum_tids = $fids = array();

    // hook model_website_index_category_thread_forumlist_start.php

    if ($category_forumlist) {
        foreach ($category_forumlist as &$_forum) {

            // hook model_website_index_category_thread_forumlist_before.php

            $fids[] = $_forum['fid'];

            // 首页显示数据
            $arrlist['list'][$_forum['fid']] = array(
                'fid' => $_forum['fid'],
                'name' => $_forum['name'],
                'rank' => $_forum['rank'],
                'well_type' => $_forum['well_type'],
                'url' => $_forum['url'],
                'well_news' => $_forum['well_news'],
                // hook model_well_cms_index_forum_foreach.php
            );

            // hook model_website_index_category_thread_forumlist_after.php

            $forum_thread = well_thread_tid__find(array('fid' => $_forum['fid']), $orderby, $page, $_forum['well_news'], 'tid', array('tid'));
            // 最新信息按栏目分组
            foreach ($forum_thread as $_thread) {
                $forum_tids[] = $_thread['tid'];
            }

            // hook model_website_index_category_thread_forumlist_after.php

            unset($forum_thread);
        }
    }

    // hook model_website_index_category_thread_forumlist_end.php

    unset($category_forumlist);

    // hook model_website_index_category_thread_center.php

    // 获取频道下属性
    //$flaglist = well_website_find_flag($fid);
    $flagids = isset($forumlist[$fid]) ? explode(',', $forumlist[$fid]['well_flag']) : NULL;
    $flaglist = $flagids ? well_website_flag_find_by_flagid_cache($flagids, 1, count($flagids)) : NULL;

    // 获取属性对应的tid集合
    $flaglist = well_website_flag_return_thread_tids($flaglist);
    $flagtidarr = array_value($flaglist, 'arr');
    $flagtids = array_value($flaglist, 'tids');

    // hook model_website_index_category_thread_flag_after.php

    // 频道置顶
    $toplist = well_thread_top_find($fids, 2);
    $toptids = $toplist ? arrlist_values($toplist, 'tid') : array();

    // 合并tid
    $tidarr = array_merge($forum_tids, (array)$flagtids, $toptids);

    // hook model_website_index_category_thread_merge_after.php

    if (!empty($tidarr)) {
        $tidarr = array_unique($tidarr);

        // hook model_website_index_category_thread_unique_after.php

        $pagesize = count($tidarr);

        // hook model_website_index_category_thread_find_before.php

        // 遍历获取的所有tid主题
        $threadlist = well_thread_find_asc($tidarr, $pagesize);

        // hook model_website_index_category_thread_find_after.php

        foreach ($threadlist as &$_thread) {

            // hook model_website_index_category_thread_cate_before.php

            // 各栏目最新内容
            in_array($_thread['tid'], $forum_tids) AND $arrlist['list'][$_thread['fid']]['news'][$_thread['tid']] = $_thread;

            // 全站置顶内容
            !empty($toptids) AND in_array($_thread['tid'], $toptids) AND $arrlist['top'][$_thread['tid']] = $_thread;

            // 首页属性主题
            if (!empty($flagtidarr)) {
                foreach ($flagtidarr as $key => $val) {
                    if (in_array($_thread['tid'], $val['tids'])) {
                        $arrlist['flagarr'][$key][$_thread['tid']] = $_thread;
                        $arrlist['flag'][$_thread['tid']] = $_thread;
                    }
                }
            }

            // hook model_website_index_category_thread_cate_after.php
        }

        unset($threadlist);

        if (isset($arrlist['top'])) {
            $i = 0;
            foreach ($arrlist['top'] as &$val) {
                ++$i;
                $val['i'] = $i;
            }
        }

        if (isset($arrlist['flag'])) {
            $i = 0;
            foreach ($arrlist['flag'] as &$val) {
                ++$i;
                $val['i'] = $i;
            }
        }

        if (isset($arrlist['flagarr'])) {
            foreach ($arrlist['flagarr'] as &$val) {
                $i = 0;
                foreach ($val as &$v) {
                    ++$i;
                    $v['i'] = $i;
                }
            }
        }

        // hook model_website_index_category_thread_after.php

        isset($arrlist['list']) AND $arrlist['list'] = well_array_multisort_key($arrlist['list'], 'rank', TRUE, 'fid');
    }

    // hook model_website_index_category_thread_end.php

    return $arrlist;
}

//---------------------other----------------------
// read.php 统一遍历需要的tid，然后合并去重，再遍历主题表，随后再分类
function well_website_other_thread($thread)
{
    // hook model_website_other_thread_start.php

    $forumlist = GLOBALS('forumlist');
    $fid = array_value($thread, 'fid');
    $forum = array_value($forumlist, $fid);
    if (!$forum) return NULL;
    $tid = array_value($thread, 'tid');
    $tag_text = array_value($thread, 'tag_text');

    // hook model_website_other_thread_before.php

    $arrlist = $flagtids = array();

    // hook model_website_other_thread_flag_start.php
    if ($forum['well_flag']) {
        // 获取各属性对应的tid集合
        $flaglist = well_website_flag_return_thread_tids($forum['well_flag_text']);
        $flagtidarr = array_value($flaglist, 'arr');
        $flagtids = array_value($flaglist, 'tids');
        unset($flaglist);
    }
    // hook model_website_other_thread_flag_end.php

    // hook model_website_other_thread_center.php

    // 此处自行hook合并tid
    $tidarr = array_merge($flagtids);

    // hook model_website_other_thread_middle.php

    if (!empty($tidarr)) {

        $tidarr = array_unique($tidarr);

        // hook model_website_other_thread_unique_after.php

        $pagesize = count($tidarr);

        // hook model_website_other_thread_find_before.php

        // 遍历获取的所有tid主题
        $threadlist = well_thread_find_asc($tidarr, $pagesize);

        // hook model_website_other_thread_find_after.php

        foreach ($threadlist as &$_thread) {

            // hook model_website_other_thread_cate_before.php

            // 属性主题
            if (!empty($flagtidarr)) {
                foreach ($flagtidarr as $key => $val) {
                    if (in_array($_thread['tid'], $val['tids'])) {
                        $arrlist['flag'][$key]['name'] = $val['name'];
                        $arrlist['flag'][$key]['url'] = $val['url'];
                        $arrlist['flag'][$key]['list'][$_thread['tid']] = $_thread;
                        // 栏目所有属性每个属性下的最新主题集合
                        //$arrlist['flag'][$_thread['tid']] = $_thread;

                        // hook model_website_other_thread_flag_before.php
                    }
                }
            }

            // hook model_website_other_thread_cate_after.php
        }

        unset($threadlist);

        if (isset($arrlist['flag'])) {
            foreach ($arrlist['flag'] as &$val) {
                $i = 0;
                foreach ($val['list'] as &$v) {
                    ++$i;
                    $v['i'] = $i;
                }
            }
        }

        // hook model_website_other_thread_i_after.php
    }

    // hook model_website_other_thread_after.php

    return $arrlist;
}

// 随机调用栏目主题tids = array(1,2,3,4);
function well_website_cate_rand_thread($fid, $num = 10)
{
    $arrlist = well_thread_tid__find(array('fid' => $fid), array(), 1, 5000, 'tid', array('tid'));
    if (!$arrlist) return NULL;

    $tidarr = arrlist_values($arrlist, 'tid');
    $n = count($tidarr);

    $arrids = array();
    $count = $num * 2;
    for ($i = 0; $i < $count; ++$i) {
        $arrids[] = mt_rand(0, $n);
    }

    $arrids = array_unique($arrids);

    $i = 0;
    $tids = array();
    foreach ($arrids as $val) {
        ++$i;
        if ($i <= $num && $val) {
            $tids[] = $tidarr[$val];
        }
    }

    //$threadlist = well_thread_find($tids, count($tids));

    return $tids;
}

// hook model_misc_website_end.php

?>