<?php
/**
 * @author xuguang5@staff.weibo.com
 * @date 2020/3/1
 */

function createSign($data) {
    $salt = 'tv_open_sign';
    $appkey = $data['appkey'];  //分配给业务方
    $params = $data['data'];

    ksort($params);
    $sign = '';
    foreach ($params as $k=>$v) {
        if($v != '') {
            $sign .= $k.'='.$v.'&';
        }
    }
    $sign = trim($sign, '&');
    $sign .= $appkey;
    $sign .= $salt;
    $sign = strtolower($sign);
    return md5($sign);
}
$params = [];
$params['appkey'] = $appkey = '2ace16545c831ef923870dbaf7e6ea2b';
$params['data'] = [
    'name' => 'lixg',
    'age' => 12,
    'timestamp' => time(),
];

$sign = createSign($params);
$newp = '';
foreach ($params['data'] as $k=>$v) {
    if($v != '') {
        if(is_array($v)) {
            $v = json_encode($v);
            $newp .= $k.'='.$v.'&';
        } else {
            $newp .= $k.'='.$v.'&';
        }
    }
}
$urlparams .= $newp.'appkey='.$appkey.'&sign='.$sign;

$url = "127.0.0.1:9200/Openapi.php?".$urlparams;
var_dump($url);
exit;
$ch = curl_init();
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POSTFIELDS , http_build_query($params));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$output = curl_exec($ch);
echo $output;
curl_close($ch);

