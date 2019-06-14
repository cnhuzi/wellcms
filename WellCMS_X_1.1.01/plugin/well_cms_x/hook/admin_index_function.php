<?php exit;
function well_get_last_version($stat)
{
    $conf = GLOBALS('conf');
    $longip = GLOBALS('longip');
    $time = GLOBALS('time');
    $last_version = kv_get('website_last_version');
    if ($time - $last_version && $longip != 2130706433) {
        $website_conf = GLOBALS('website_conf');
        kv_set('website_last_version', ($time + 86400));
        $sitename = urlencode($conf['sitename']);
        $sitedomain = urlencode(http_url_path());
        $version = urlencode($website_conf['version']);
        return '<script src="http://client.wellcms.cn/version.htm?sitename=' . $sitename . '&sitedomain=' . $sitedomain . '&users=' . $stat['users'] . '&website_threads=' . $stat['website_threads'] . '&website_posts=' . $stat['website_posts'] . '&version=' . $version . '"></script>';
    } else {
        return '';
    }
}

?>