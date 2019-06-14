<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 * 版块管理扩展
 */

// hook model_website_misc_forum_start.php

// 获取CMS全部栏目，包括频道的二叉树结构
function well_website_all_forum_category_tree($forumlist)
{
    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), 1, 1000);
    $forumlist = well_forum_category_tree($forumlist);
    $forumlist = well_array_multisort_key($forumlist, 'rank', FALSE, 'fid');
    return $forumlist;
}

// 门户 获取需要在首页显示的版块 fid name well_news最新显示数量
function well_website_index_forum($forumlist)
{
    if (!$forumlist) return NULL;

    // hook model_website_index_forum_start.php

    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 0, 'well_display' => 1), array('fid' => -1), 1, 1000);

    // hook model_website_index_forum_end.php

    return $forumlist;
}

// 门户 获取需要在频道显示的版块 fid name well_news最新显示数量
function well_website_category_forum($fid)
{
    if (!$fid) return NULL;

    // hook model_website_category_forum_start.php

    $forumlist = _SERVER('forumlist');
    if (!isset($forumlist[$fid])) return NULL;

    // hook model_website_category_forum_before.php

    $forum = $forumlist[$fid];

    // hook model_website_category_forum_after.php

    $forumlist = $forum['well_son'] ? arrlist_cond_orderby($forumlist, array('well_fup' => $fid, 'well_type' => 1, 'well_forum_type' => 0, 'well_display' => 1), array('fid' => -1), 1, 1000) : NULL;

    // hook model_website_index_forum_end.php

    return $forumlist;
}

// 返回所有频道数据 well_get_channellist($forumlist)
function well_all_channellist($forumlist)
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
function well_forum_category_tree($arr)
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

// 获取CMS 栏目
function well_website_new_forumarr($forumlist, $model = 0)
{
    $cond = array('well_type' => 1, 'well_forum_type' => 0);
    $model AND $cond['well_model'] = $model;

    $cmslist = arrlist_cond_orderby($forumlist, $cond, array(), 1, 1000);
    return $cmslist;
}

// 获取CMS全部栏目，包括频道
function well_website_all_forum($forumlist)
{
    return arrlist_cond_orderby($forumlist, array('well_type' => 1), array(), 1, 1000);
}

// 返回CMS版块数据(仅列表)well_get_cms_forumlist($forumlist)
function well_website_return_forumlist($forumlist)
{
    $forumlist = arrlist_cond_orderby($forumlist, array('well_type' => 1, 'well_forum_type' => 0), array(), 1, 1000);
    return $forumlist;
}

// 返回BBS版块数据(仅列表) 尚未开放bbs频道功能 well_get_bbs_forumlist($forumlist)
function well_bbs_forumlist($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS所有版块数据(包括频道) well_get_bbs_list($forumlist)
function well_bbs_list($forumlist)
{
    $bbslist = arrlist_cond_orderby($forumlist, array('well_type' => 0), array(), 1, 1000);

    return $bbslist;
}

// 返回BBS频道数据well_get_bbs_channellist($forumlist)
function well_bbs_channellist($forumlist)
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

// hook model_website_misc_forum_end.php
?>