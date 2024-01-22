<?php
    $id = $_GET['id'] ?? "9249162";
    $apiUrl = 'https://wxapp.douyucdn.cn/api/nc/stream/roomPlayer';
    $postData = "room_id=$id&big_ct=cph-androidmpro&did=10000000000000000000000000001501&mt=2&rate=0";
    $ch = curl_init();
    curl_setopt($ch,CURLOPT_URL,$apiUrl);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,TRUE);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
    $result = curl_exec($ch);
    curl_close($ch);
    $json = json_decode($result);
    $mediaUrl = $json->data->live_url;
    header('location:'.$mediaUrl);
?>
