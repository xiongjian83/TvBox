<?php
class TV
{
    const YSP_API = 'https://player-api.yangshipin.cn/v1/player/get_live_info';
    const YSP_CHANNEL = [
        'CCTV4K' => 2000266303,
        'CCTV8K' => 2020603401,
        'CCTV1' => 2000210103,
        'CCTV2' => 2000203603,
        'CCTV3' => 2000203803,
        'CCTV4' => 2000204803,
        'CCTV5' => 2000205103,
        'CCTV5P' => 2000204503,
        'CCTV6' => 2000203303,
        'CCTV7' => 2000510003,
        'CCTV8' => 2000203903,
        'CCTV9' => 2000499403,
        'CCTV10' => 2000203503,
        'CCTV11' => 2000204103,
        'CCTV12' => 2000202603,
        'CCTV13' => 2000204603,
        'CCTV14' => 2000204403,
        'CCTV15' => 2000205003,
        'CCTV16' => 2012375003,
        'CCTV16-4K' => 2012492303,
        'CCTV17' => 2000204203,

        'CCTV兵器科技' => 2012513403,
        'CCTV第一剧场' => 2012514403,
        'CCTV怀旧剧场' => 2012511203,
        'CCTV风云剧场' => 2012513603,
        'CCTV风云音乐' => 2012514103,
        'CCTV风云足球' => 2012514203,
        'CCTV电视指南' => 2012514003,
        'CCTV女性时尚' => 2012513903,
        'CCTV央视文化精品' => 2012513803,
        'CCTV世界地理' => 2012513303,
        'CCTV高尔夫网球' => 2012512503,
        'CCTV央视台球' => 2012513703,
        'CCTV卫生健康' => 2012513503,

        'CGTN' => 2001656803,
        'CGTN纪录' => 2010155403,
        'CGTN西语' => 2010152503,
        'CGTN法语' => 2010153503,
        'CGTN阿语' => 2010155203,
        'CGTN俄语' => 2010152603,

        '北京卫视' => 2000272103,
        '东方卫视' => 2000292403,
        '天津卫视' => 2019927003,
        '重庆卫视' => 2000297803,
        '黑龙江卫视' => 2000293903,
        '辽宁卫视' => 2000281303,
        '河北卫视' => 2000293403,
        '山东卫视' => 2000294803,
        '安徽卫视' => 2000298003,
        '河南卫视' => 2000296103,
        '湖北卫视' => 2000294503,
        '湖南卫视' => 2000296203,
        '江西卫视' => 2000294103,
        '江苏卫视' => 2000295603,
        '浙江卫视' => 2000295503,
        '东南卫视' => 2000292503,
        '广东卫视' => 2000292703,
        '深圳卫视' => 2000292203,
        '广西卫视' => 2000294203,
        '贵州卫视' => 2000293303,
        '四川卫视' => 2000295003,
        '新疆卫视' => 2019927403,
        '海南卫视' => 2000291503,
    ];
    const YSP_RETRY = 10;

    private $__name, $__arg;
    private $__request;

    public function __construct($name)
    {
        $this->__name = $name;
        $this->__arg = (object) [
            'salt' => '0f$IVHi9Qno?G',
            'platform' => '5910204',
            'key' => hex2bin('48e5918a74ae21c972b90cce8af6c8be'),
            'iv' => hex2bin('9a7e7d23610266b1d9fbf98581384d92'),
            'time' => time(),
            'guid' => '0',
            'retry' => 0,
        ];

        if (!isset(self::YSP_CHANNEL[$this->__name])) {
            throw new \Exception('渠道信息错误。', 412);
        }
    }

    public function getUri()
    {
        $this->__arg->time = time();
        $this->__request = curl_init(self::YSP_API);

        curl_setopt_array($this->__request, [
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Referer: https://www.yangshipin.cn/',
                'Cookie: guid=0;vplatform=109',
                'Yspappid: 519748109',
            ],
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => json_encode($this->__getPostData())
        ]);

        try {
            if ($response = json_decode(curl_exec($this->__request))) {
                if ($response->code == 0) {
                    return sprintf('%s', $response->data->playurl);
                } else if ($this->__arg->retry++ < self::YSP_RETRY) {
                    return $this->getUri();
                }
            }
        } finally {
            curl_reset($this->__request);
        }
    }

    private function __getPostData()
    {
        $val = [
            'adjust' => 1,
            'appVer' => 'V1.0.0',
            'app_version' => 'V1.0.0',
            'cKey' => $this->__getKey(),
            'channel' => 'ysp_tx',
            'cmd' => '2',
            'cnlid' => (string) self::YSP_CHANNEL[$this->__name],
            'defn' => 'fhd',
            'devid' => 'devid',
            'dtype' => 1,
            'encryptVer' => '8.1',
            'guid' => $this->__arg->guid,
            'otype' => 'ojson',
            'platform' => $this->__arg->platform,
            'rand_str' => (string) $this->__arg->time,
            "sphttps" => '1',
            "stream" => '2'
        ];
        $val['signature'] = md5(http_build_query($val) . $this->__arg->salt);

        return $val;
    }

    private function __getKey()
    {
        $val = sprintf(
            '|%s|%s|mg3c3b04ba|V1.0.0|%s|%s|https://www.yangshipin.c|mozilla/5.0 (windows nt ||Mozilla|Netscape|Win32|',
            self::YSP_CHANNEL[$this->__name],
            $this->__arg->time,
            $this->__arg->guid,
            $this->__arg->platform
        );

        $len = strlen($val);
        $key = 0;
        for ($i = 0; $i < $len; $i++) {
            $key = ($key << 5) - $key + ord($val[$i]);
            $key &= $key & 0xFFFFFFFF;
        }

        $key = ($key > 2147483648) ? $key - 4294967296 : $key;
        $val = '|' . $key . $val;

        return '--01' . strtoupper(bin2hex(openssl_encrypt($val, 'AES-128-CBC', $this->__arg->key, 1, $this->__arg->iv)));
    }
}

$name = $argv[1] ?? $_GET['name'] ?? 'CCTV-1';
$tv = new TV($name);
header(sprintf('Location: %s', $tv->getUri()));