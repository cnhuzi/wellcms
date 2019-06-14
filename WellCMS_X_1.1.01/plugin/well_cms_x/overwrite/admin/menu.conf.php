<?php
/*
 * Copyright (C) 2018 www.wellcms.cn
 */
return array(
    // hook website_admin_menu_conf_start.php
    'content' => array(
        'url' => url('content-list'),
        'text' => lang('well_content'),
        'icon' => 'icon-pencil-square',
        'tab' => array(
            // hook website_admin_menu_conf_content_start.php
            'list' => array('url' => url('content-list'), 'text' => lang('well_list')),
            // hook website_admin_menu_conf_content_before.php
            'top' => array('url' => url('top-list'), 'text' => lang('top')),
            // hook website_admin_menu_conf_content_center.php
            'reply' => array('url' => url('reply-list'), 'text' => lang('well_reply')),
            // hook website_admin_menu_conf_content_middle.php
            'flag' => array('url' => url('flag-list'), 'text' => lang('well_flag')),
            // hook website_admin_menu_conf_content_after.php
            'license' => array('url' => url('content-license'), 'text' => lang('well_license')),
            // hook website_admin_menu_conf_content_end.php
        )
    ),
    // hook website_admin_menu_conf_forum_before.php
    'forum' => array(
        'url' => url('forum-list'),
        'text' => lang('well_column'),
        'icon' => 'icon-comment',
        'tab' => array(
            // hook website_admin_menu_conf_forum_list_before.php
            'list' => array('url' => url('forum-list'), 'text' => lang('well_column')),
            // hook website_admin_menu_conf_forum_list_after.php
        )
    ),
    // hook website_admin_menu_conf_forum_after.php
    'user' => array(
        'url' => url('user-list'),
        'text' => lang('user'),
        'icon' => 'icon-user',
        'tab' => array(
            // hook website_admin_menu_conf_user_list_before.php
            'list' => array('url' => url('user-list'), 'text' => lang('well_list')),
            // hook website_admin_menu_conf_user_list_after.php
            'group' => array('url' => url('group-list'), 'text' => lang('admin_user_group')),
            // hook website_admin_menu_conf_user_group_after.php
            'create' => array('url' => url('user-create'), 'text' => lang('admin_user_create')),
            // hook website_admin_menu_conf_user_create_after.php
        )
    ),
    // hook website_admin_menu_conf_user_after.php
    'template' => array(
        'url' => url('template-code'),
        'text' => lang('well_template'),
        'icon' => 'icon-internet-explorer',
        'tab' => array(
            // hook website_admin_menu_conf_template_code_before.php
            'code' => array('url' => url('template-code'), 'text' => lang('well_code')),
            // hook website_admin_menu_conf_template_code_after.php
            'diy' => array('url' => url('template-diy'), 'text' => 'Diy code'),
            // hook website_admin_menu_conf_template_diy_after.php
        )
    ),
    // hook website_admin_menu_conf_template_after.php
    'setting' => array(
        'url' => url('setting-base'),
        'text' => lang('setting'),
        'icon' => 'icon-cog',
        'tab' => array(
            // hook website_admin_menu_conf_setting_base_before.php
            'base' => array('url' => url('setting-base'), 'text' => lang('setting')),
            // hook website_admin_menu_conf_setting_base_after.php
            'system' => array('url' => url('system-setting'), 'text' => lang('well_website')),
            // hook website_admin_menu_conf_setting_system_after.php
            'smtp' => array('url' => url('setting-smtp'), 'text' => 'SMTP'),
            // hook website_admin_menu_conf_setting_smtp_after.php
        )
    ),
    // hook website_admin_menu_conf_setting_after.php
    'plugin' => array(
        'url' => url('plugin'),
        'text' => lang('plugin'),
        'icon' => 'icon-cogs',
        'tab' => array(
            // hook website_admin_menu_conf_plugin_local_before.php
            'local' => array('url' => url('plugin-local'), 'text' => lang('well_local')),
            // hook website_admin_menu_conf_plugin_local_after.php
            /*'official_free'=>array('url'=>url('plugin-official_free'), 'text'=>lang('admin_plugin_official_free_list')),
            'official_fee'=>array('url'=>url('plugin-official_fee'), 'text'=>lang('admin_plugin_official_fee_list')),*/
        )
    ),
    // hook website_admin_menu_conf_plugin_after.php
    'other' => array(
        'url' => url('other'),
        'text' => lang('other'),
        'icon' => 'icon-wrench',
        'tab' => array(
            // hook website_admin_menu_conf_other_cache_before.php
            'cache' => array('url' => url('other-cache'), 'text' => lang('well_cache')),
            // hook website_admin_menu_conf_other_cache_after.php
            'optimize' => array('url' => url('system-optimize'), 'text' => lang('well_optimize_table')),
            // hook website_admin_menu_conf_website_optimize_after.php
            'map' => array('url' => url('system-map'), 'text' => lang('well_map')),
            // hook website_admin_menu_conf_website_map_after.php
            'increase' => array('url' => url('system-increase'), 'text' => lang('well_increase_thread')),
            // hook website_admin_menu_conf_website_increase_after.php
        )
    ),
    // hook website_admin_menu_conf_end.php
);

?>