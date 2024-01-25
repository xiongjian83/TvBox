<?php

// 获取 "id" 参数
$id = isset($_GET["id"]) ? $_GET["id"] : "cctv1";

// 构建带有参数的URL
$url = "https://www.histar.tv/live/" . $id; // 替换为您的URL和参数

// 创建cURL会话
$ch = curl_init();

// 设置cURL选项
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
// 添加这两行来忽略 SSL 证书验证
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

// 设置代理服务器地址和端口
$proxy_server = '127.0.0.1'; // 代理服务器地址
$proxy_port = '10809'; // 代理服务器端口
curl_setopt($ch, CURLOPT_PROXY, $proxy_server . ':' . $proxy_port);

// 如果代理服务器需要身份验证，请添加以下选项
// curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'username:password');

// 设置User-Agent头部
$userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/118.0.0.0 Safari/537.36';
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);

// 设置 Referer 和 Origin 头部
$referer = 'https://www.histar.tv'; // 替换为您的 Referer 地址
$origin = 'https://www.histar.tv'; // 替换为您的 Origin 地址
$headers = array(
    "Referer: $referer",
    "Origin: $origin"
);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

// 执行cURL请求并获取响应内容
$content = curl_exec($ch);

// 检查是否有错误发生
if (curl_errno($ch)) {
    echo 'cURL错误：' . curl_error($ch);
} else {
    // 使用正则表达式提取"playUrl"的值
	$pattern = '/<script id="__NEXT_DATA__" type="application\/json">(.*?)<\/script>/s';
	preg_match($pattern, $content, $matches);

	if (isset($matches[1])) {
		$nextDataContent = $matches[1];
		// 解码 JSON 内容
		$nextData = json_decode($nextDataContent, true);
		if ($nextData !== null) {
			$playUrl = $nextData['props']['pageProps']['playUrl'];
			// echo "播放链接: " . $playUrl;
		} else {
			echo "无法解析 JSON 内容";
		}
	} else {
		echo "未找到匹配的内容";
	}


    if ($playUrl) {
        // 打印提取的播放链接
        // echo "提取的播放链接: $playUrl\n";

        // 创建新cURL会话以继续访问播放链接
        $ch2 = curl_init();
		// echo $playUrl;
        curl_setopt($ch2, CURLOPT_URL, $playUrl);
        curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

        // 设置User-Agent、Referer和Origin头部
        curl_setopt($ch2, CURLOPT_USERAGENT, $userAgent);
        curl_setopt($ch2, CURLOPT_HTTPHEADER, $headers);
		// 添加这两行来忽略 SSL 证书验证
		curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch2, CURLOPT_SSL_VERIFYHOST, false);

        // 执行第二个cURL请求并获取响应内容
        $content2 = curl_exec($ch2);

		if (curl_errno($ch2)) {
			echo 'cURL错误（第二个请求）：' . curl_error($ch2);
		} else {
			// 输出访问播放链接后的结果
			echo $content2;
		}

        // 关闭第二个cURL会话
        curl_close($ch2);
    } else {
        // 如果未找到播放链接，您可以采取适当的错误处理措施
        echo "未找到播放链接";
    }

    // 关闭第一个cURL会话
    curl_close($ch);
}
