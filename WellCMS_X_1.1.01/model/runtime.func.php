<?php

// hook model_runtime_start.php

function runtime_init()
{
    // hook model_runtime_init_start.php

    $runtime = cache_get('runtime'); // 实时运行的数据，初始化！
    if ($runtime === NULL || !isset($runtime['users'])) {
        $runtime = array();
        $runtime['users'] = user_count();
        $runtime['todayusers'] = 0;
        $runtime['todayposts'] = 0;
        $runtime['todaythreads'] = 0;
        $runtime['onlines'] = max(1, online_count());
        $runtime['cron_1_last_date'] = 0;
        $runtime['cron_2_last_date'] = 0;

        // hook model_website_runtime_init_before.php

        cache_set('runtime', $runtime);

    }
    // hook model_runtime_init_end.php
    return $runtime;
}

function runtime_get($k)
{
    global $runtime;
    // hook model_runtime_get_start.php
    //$runtime = $_SERVER['runtime'];
    // hook model_runtime_get_end.php
    return array_value($runtime, $k, NULL);
}

function runtime_set($k, $v)
{
    global $runtime;
    // hook model_runtime_set_start.php
    //$runtime = $_SERVER['runtime'];
    $op = substr($k, -1);
    if ($op == '+' || $op == '-') {
        $k = substr($k, 0, -1);
        !isset($runtime[$k]) AND $runtime[$k] = 0;
        $v = $op == '+' ? ($runtime[$k] + $v) : ($runtime[$k] - $v);
    }

    $runtime[$k] = $v;
    return TRUE;
    // hook model_runtime_set_end.php
}

function runtime_delete($k)
{
    global $runtime;
    // hook model_runtime_delete_start.php
    //$runtime = $_SERVER['runtime'];
    unset($runtime[$k]);
    runtime_save();
    return TRUE;
    // hook model_runtime_delete_end.php
}

function runtime_save()
{
    global $runtime;
    // hook model_runtime_save_start.php
    function_exists('chdir') AND chdir(APP_PATH);
    $r = cache_set('runtime', $runtime);
    // hook model_runtime_save_end.php
}

function runtime_truncate()
{
    // hook model_runtime_truncate_start.php
    cache_delete('runtime');
    // hook model_runtime_truncate_end.php
}

register_shutdown_function('runtime_save');

// hook model_runtime_end.php

?>