<?php

$rid = empty($_GET['id']) ? "37917621268" : trim($_GET['id']);
$stream = empty($_GET['media']) ? "flv" : trim($_GET['media']);

function mk_dir($newdir)
{
    $dir = $newdir;
    if (is_dir('./' . $dir)) {
        return $dir;
    } else {
        mkdir('./' . $dir, 0777, true);
        return $dir;
    }
}

function getDouYinUrl($rid, $stream,$cookietext)
{
    $liveurl = "https://live.douyin.com/" . $rid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $liveurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/109.0.0.0 Safari/537.36");
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookietext);
    $response = curl_exec($ch);
    preg_match('/__ac_nonce=(.*?);/', $response, $matches);
    $ac_nonce = $matches[1];

    curl_setopt($ch, CURLOPT_URL, $liveurl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: __ac_nonce=" . $ac_nonce));
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookietext);
    $response = curl_exec($ch);
    preg_match('/ttwid=.*?;/', $response, $matches);
    $ttwid = $matches[0];
    $url = "https://live.douyin.com/webcast/room/web/enter/?aid=6383&app_name=douyin_web&live_id=1&device_platform=web&language=zh-CN&enter_from=web_live&cookie_enabled=true&screen_width=1728&screen_height=1117&browser_language=zh-CN&browser_platform=MacIntel&browser_name=Chrome&browser_version=116.0.0.0&web_rid=" . $rid;
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36",
        "Cookie: " . $ttwid,
        "Accept: */*",
        "Host: live.douyin.com",
        "Connection: keep-alive"
    ));
    $response = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($response, true);
    $status = $json['data']['data'][0]['status'];
    if ($status != 2) {
        return null;
    }
    $value = json_decode($json['data']['data'][0]['stream_url']['live_core_sdk_data']['pull_data']['stream_data'],true);
    $realurl = "";
    foreach ($value as $key => $val) {
        if (array_key_exists('origin',$val)){
            if ($stream == "flv") {
                $realurl = $val['origin']['main']['flv'];
            } elseif ($stream == "hls") {
                $realurl = $val['data']['origin']['main']['hls'];
            }
        }
    }
    return $realurl;

}

$cookietext = './' . mk_dir('cookies/') . md5(microtime()) . '.' . 'txt';
$mediaurl = getDouYinUrl($rid, $stream,$cookietext);
unlink($cookietext);
if ($mediaurl!=null){
    header('location:' . $mediaurl);
    exit();
}