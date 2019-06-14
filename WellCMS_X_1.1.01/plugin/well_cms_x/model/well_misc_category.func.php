<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 栏目管理扩展
 */

// hook model_website_misc_category_start.php

// 获取CMS全部栏目，包括频道的二叉树结构
function well_all_category_tree($forumlist)
{
    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), 1, 1000);
    $forumlist = well_category_tree($forumlist);
    $forumlist = well_array_multisort_key($forumlist, 'rank', FALSE, 'fid');
    return $forumlist;
}

// 门户 获取需要在首页显示的栏目 fid name well_news最新显示数量
function well_index_category($forumlist)
{
    if (!$forumlist) return NULL;

    // hook model_website_index_category_start.php

    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 0, 'well_display' => 1), array('fid' => -1), 1, 1000);

    // hook model_website_index_category_end.php

    return $forumlist;
}

// 门户 获取需要在频道显示的栏目 fid name well_news最新显示数量
function well_category_show($fid)
{
    if (!$fid) return NULL;

    // hook model_website_category_show_start.php

    $forumlist = GLOBALS('forumlist');
    if (!isset($forumlist[$fid])) return NULL;

    // hook model_website_category_show_before.php

    $forum = $forumlist[$fid];

    // hook model_website_category_show_after.php

    $forumlist = $forum['well_son'] ? arrlist_cond_orderby($forumlist, array('well_fup' => $fid, 'well_type' => 1, 'well_forum_type' => 0, 'well_display' => 1), array('fid' => -1), 1, 1000) : NULL;

    // hook model_website_index_category_end.php

    return $forumlist;
}

// 返回所有频道数据 well_get_channellist($forumlist)
function well_channel_key_values($forumlist)
{
    $channellist = arrlist_cond_orderby($forumlist, array('well_forum_type' => 1), array(), 1, 1000);
    $fidarr = arrlist_key_values($channellist, 'fid', 'name');
    $arr = array('0' => lang('well_top_column'));
    foreach ($fidarr as $key => $v) {
        $arr[$key] = $v;
    }
    return $arr;
}

// get category (tree structure)
function well_category_tree($arr)
{
    // 格式化为树状结构 (会舍弃不合格的结构)
    foreach ($arr as $v) {
        // 按照上级pid格式化 归属子栏目到上级栏目
        if ($v['well_fup']) {
            $arr[$v['well_fup']]['son'][$v['fid']] = $arr[$v['fid']];
            unset($arr[$v['fid']]);
        }
    }

    return $arr;
}

// 根据 model 获取 CMS 栏目，model 0文章
function well_website_column($forumlist, $model = 0)
{
    $cond = array('well_type' => 1, 'well_forum_type' => 0);
    $model AND $cond['well_model'] = $model;
    $cmslist = arrlist_cond_orderby($forumlist, $cond, array(), 1, 1000);
    return $cmslist;
}

// 获取CMS全部栏目，包括频道
function well_all_category($forumlist)
{
    return arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), 1, 1000);
}

// 返回CMS栏目数据(仅列表)
function well_return_column($forumlist)
{
    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 0), array(), 1, 1000);
    return $forumlist;
}

// 返回BBS栏目数据(仅列表) 尚未开放bbs频道功能
function well_forum_list($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS所有栏目数据(包括频道)
function well_forum_all_column($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS频道数据
function well_forum_channel($forumlist)
{
    $channellist = arrlist_cond_orderby($forumlist, array('well_type' => 0, 'well_forum_type' => 1), array(), 1, 1000);

    return $channellist;
}

// 显示的导航
function well_filter_nav($forumlist)
{
    foreach ($forumlist as $fid => $forum) {
        if (isset($forum['well_nav_display']) && $forum['well_nav_display'] == 0) {
            unset($forumlist[$fid]);
        }
    }
    return $forumlist;
}

// hook model_website_misc_category_end.php
?>