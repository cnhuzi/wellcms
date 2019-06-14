<?php exit;
$stat['threads'] = function_exists('thread_count') ? thread_count() : lang('unknown');
    $stat['posts'] = function_exists('post_count') ? post_count() : lang('unknown');
    $stat['attachs'] = function_exists('attach_count') ? attach_count() : lang('unknown');
    $stat['website_threads'] = function_exists('well_thread_count') ? well_thread_count() : lang('unknown');
    $stat['website_posts'] = function_exists('well_post_pid__count') ? well_post_pid__count() : lang('unknown');
    $stat['website_attachs'] = function_exists('well_attach_count') ? well_attach_count() : lang('unknown');
?>