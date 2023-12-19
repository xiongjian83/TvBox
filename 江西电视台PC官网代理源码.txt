<?php
$id = isset($_GET['id'])?$_GET['id']:'jxws';
$n = [
   'jxws' => 'tv_jxtv1.m3u8',//江西卫视
   'jxds' => 'tv_jxtv2.m3u8',//江西都市
   'jxjs' => 'tv_jxtv3_hd.m3u8',//江西经视
   'jxys' => 'tv_jxtv4.m3u8',//江西影视
   'jxgg' => 'tv_jxtv5.m3u8',//江西公共
   'jxse' => 'tv_jxtv6.m3u8',//江西少儿
   'jxxw' => 'tv_jxtv7.m3u8',//江西新闻
   'jxyd' => 'tv_jxtv8.m3u8',//江西移动
   'fsgw' => 'tv_fsgw.m3u8',//江西风尚购物
   ];

$etag = '1234abcd';//随意

$auth = md5("1609229748{$n[$id]}{$etag}233face@12&^a");
$post = "t=1609229748&stream={$n[$id]}&uuid=";
$h = [
        "authorization: ".$auth,
        "origin: www.jxntv.cn",
        "Referer: www.jxntv.cn",
        "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/116.0.0.0 Safari/537.36",
        "etag: ".$etag,
        'Sec-Ch-Ua:"Chromium";v="116", "Not)A;Brand";v="24", "Google Chrome";v="116"',
    ];
$ch = curl_init('https://app.jxntv.cn/Qiniu/liveauth/getPCAuth.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
curl_setopt($ch, CURLOPT_HTTPHEADER, $h);
$data = curl_exec($ch);
curl_close($ch);
$t = json_decode($data)->t;
$token = json_decode($data)->token;
$playurl = "https://yun-live.jxtvcn.com.cn/live-jxtv/{$n[$id]}?source=pc&t={$t}&token={$token}";
$str = file_get_contents($playurl);
$burl = "https://yun-live.jxtvcn.com.cn/live-jxtv/";
header("Content-Type: application/vnd.apple.mpegURL");
print_r(preg_replace("/(.*?.ts)/i", $burl."$1",$str));
?>