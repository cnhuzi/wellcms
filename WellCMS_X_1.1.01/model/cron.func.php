<?php

// hook model_cron_start.php

// 计划任务
function cron_run($force = 0)
{
    global $runtime;
    // hook model_cron_run_start.php
    $forumlist = GLOBALS('forumlist');
    $conf = GLOBALS('conf');
    $time = GLOBALS('time');

    // hook model_website_cron_run_before.php

    $cron_1_last_date = runtime_get('cron_1_last_date');
    $cron_2_last_date = runtime_get('cron_2_last_date');

    // 每隔 5 分钟执行一次的计划任务
    if ($time > $cron_1_last_date || $force) {
        $lock = cache_get('cron_lock_1');
        if ($lock === NULL) {
            cache_set('cron_lock_1', 1, 10); // 设置 10 秒超时

            sess_gc($conf['online_hold_time']);

            $runtime['onlines'] = max(1, online_count());

            runtime_set('cron_1_last_date', ($time + 300));

            // hook model_cron_5_minutes_end.php

            cache_delete('cron_lock_1');
        }
    }

    // 每日 0 点执行一次的计划任务
    if ($time > $cron_2_last_date || $force) {

        $lock = cache_get('cron_lock_2'); // 高并发下, 如果使用mysql缓存机制实现的锁锁不住
        if ($lock === NULL) {
            cache_set('cron_lock_2', 1, 10); // 设置 10 秒超时

            // hook model_website_cron_daily_start.php

            // 每日统计清 0
            runtime_set('todayposts', 0);
            runtime_set('todaythreads', 0);
            runtime_set('website_todayposts', 0);
            runtime_set('website_todaythreads', 0);
            runtime_set('todayusers', 0);

            // hook model_website_cron_daily_before.php

            if ($forumlist) {
                $fidarr = array();
                foreach ($forumlist as $fid => $forum) {
                    $fidarr[] = $forum['fid'];
                }

                forum__update($fidarr, array('todayposts' => 0, 'todaythreads' => 0));
                forum_list_cache_delete();
            }

            // hook model_website_cron_daily_after.php

            // 当天24点
            $today = strtotime(date('Ymd')) + 86400;
            runtime_set('cron_2_last_date', $today, TRUE);

            // 往前推8个小时，尽量保证在前一天
            // 如果是升级过来和采集的数据，这里会很卡。
            // table_day_cron($time - 8 * 3600);

            // hook model_cron_daily_end.php

            cache_delete('cron_lock_2');
        }

    }
    // hook model_cron_run_end.php
}

// hook model_cron_end.php

?>