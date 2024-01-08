<?php
$context = ['ssl' => [
    'verify_peer' => false,
    'verify_peer_name' => false,
]];

function getUri(array $list)
{
    foreach ($list as $key => $val) {
        /**
         * 过滤源
         */
        foreach (['39.134.24.161'] as $filter) {
            if (stripos($val->link, $filter) !== false) {
                unset($list[$key]);
                continue;
            }
        }

        /**
         * 优先源
         */
        foreach (['node1.olelive.com', 'iptv.luas.edu.cn'] as $keyword) {
            if (stripos($val->link, $keyword) !== false) {
                return $val->link;
            }
        }
    }

    if (count($list) > 0) {
        return array_shift($list)->link;
    }
}

$name = $argv[1] ?? $_GET['name'] ?? 'CCTV1';
if ($response = json_decode(@file_get_contents(sprintf('https://api.pearktrue.cn/api/tv/search.php?name=%s', $name), false, stream_context_create($context)))) {
    if ($response->code == 200) {
        if ($uri = getUri($response->data)) {
            header(sprintf('Location: %s', $uri));
        }
    }
}