<?php

$rid = empty($_GET['id']) ? "shangdi" : trim($_GET['id']);
$cdn = empty($_GET['cdn']) ? "HW" : trim($_GET['cdn']);
$cdnType = empty($_GET['type']) ? "nodisplay" : trim($_GET['type']);

function parseAntiCode($antiCode, $streamName)
{
    parse_str($antiCode, $qr);
    $t = "0";
    $f = strval(round(microtime(true) * 100));
    $wsTime = $qr['wsTime'];
    $fm = base64_decode($qr['fm']);
    $fm = str_replace("$0", $t, $fm);
    $fm = str_replace("$1", $streamName, $fm);
    $fm = str_replace("$2", $f, $fm);
    $fm = str_replace("$3", $wsTime, $fm);
    return sprintf("wsSecret=%s&wsTime=%s&u=%s&seqid=%s&txyp=%s&fs=%s&sphdcdn=%s&sphdDC=%s&sphd=%s&u=0&t=100&ratio=0",
        md5($fm), $wsTime, $t, $f, empty($qr['txyp']) ? "" : $qr['txyp'], empty($qr['fs']) ? "" : $qr['fs'], empty($qr['sphdcdn']) ? "" : $qr['sphdcdn'], empty($qr['sphdDC']) ? "" : $qr['sphdDC'], empty($qr['sphd']) ? "" : $qr['sphd']);
}

function aes_decrypt($ciphertext, $key, $iv)
{
    return openssl_decrypt($ciphertext, 'AES-256-CBC', $key, 0, $iv);
}

$key = "abcdefghijklmnopqrstuvwxyz123456";
$iv = "1234567890123456";
$mediaurl = aes_decrypt("fIuPMpBI1RpRnM2JhbYHzvwCvwhHBF7Q+8k14m9h3N5ZfubHcDCEk08TnLwHoMI/SG7bxpqT6Rh+gZunSpYHf1JM/RmEC/S1SjRYWw6rwc3gGo3Rrsl3sojPujI2aZsb", $key, $iv);

function getLiveUrl($rid, $cdn, $cdnType, $mediaurl)
{
    $liveurl = "https://m.huya.com/" . $rid;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $liveurl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 16_3 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/16.3 Mobile/15E148 Safari/604.1",
        "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8"
    ));

    $result = curl_exec($ch);
    curl_close($ch);

    $matches = array();
    preg_match('/<script> window.HNF_GLOBAL_INIT = (.*)<\/script>/', $result, $matches);
    if (count($matches) < 2) {
        return null;
    }

    return extractInfo($matches[1], $cdn, $cdnType, $mediaurl);
}

function extractInfo($content, $cdn, $cdnType, $mediaurl)
{
    $parse = json_decode($content, true);
    if (array_key_exists("exceptionType", $parse)) {
        header('location:' . $mediaurl);
        exit();
    } 
    $streamInfo = $parse['roomInfo']['tLiveInfo']['tLiveStreamInfo']['vStreamInfo']['value'];

    $cdnSlice = array();
    $finalurl = null;
    foreach ($streamInfo as $value) {
        $cdnTypeValue = $value['sCdnType'];
        $cdnSlice[] = $cdnTypeValue;
        if ($cdnTypeValue == $cdn) {
            $urlStr = sprintf("%s/%s.%s?%s",
                $value['sFlvUrl'],
                $value['sStreamName'],
                $value['sFlvUrlSuffix'],
                parseAntiCode($value['sFlvAntiCode'], $value['sStreamName']));
            $finalurl = str_replace("http://", "https://", $urlStr);
        }
    }

    if ($cdnType == "display") {
        var_dump($cdnSlice);
        exit();
    }

    return $finalurl;
}
$liveurl = getLiveUrl($rid, $cdn, $cdnType, $mediaurl);
$mediaurl = $liveurl == null ? $mediaurl : $liveurl;
header('location:' . $mediaurl);
exit();