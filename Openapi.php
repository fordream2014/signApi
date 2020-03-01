<?php
/**
 * Created by PhpStorm.
 * User: xuguang5
 * Date: 2020/3/1
 * Time: 下午3:13
 */

function getRequestParam($name, $default_val = '', $must_need = false) {
    if (!isset($_REQUEST[$name]) && $must_need) {
        throw new Exception("params invalid");
    }
    return !empty($_REQUEST[$name]) ? $_REQUEST[$name] : $default_val;
}

/*
 * 读取请求参数
 */
function formatParams() {
    $params = [];
    $params['appkey'] = getRequestParam('appkey');
    $params['sign'] = getRequestParam('sign');

    $params['data'] = [
        'name' =>getRequestParam('name'),
        'age' => getRequestParam('age'),
        'timestamp' => getRequestParam('timestamp'),
    ];
    return $params;
}

function createSign($data) {
    $salt = 'tv_open_sign';
    $appkey = $data['appkey'];  //分配给业务方
    $params = $data['data'];

    ksort($params);
    $sign = '';
    foreach ($params as $k=>$v) {
        if($v != '') {
            if(is_array($v)) {
                $v = json_encode($v);
                $sign .= $k.'='.$v.'&';
            } else {
                $sign .= $k.'='.$v.'&';
            }
        }
    }
    $sign = trim($sign, '&');
    $sign .= $appkey;
    $sign .= $salt;
    $sign = strtolower($sign);
    return md5($sign);
}

const VALID_APPKEY = [
    '2ace16545c831ef923870dbaf7e6ea2b' //tv_test
];

function validateParams($params) {
    $time = $params['data']['timestamp'];
    $valid_time = 20;
    if(time() - $time > $valid_time) {
        throw new Exception("time out");
    }

    $appkey = $params['appkey'];
    if(!in_array($appkey, VALID_APPKEY)) {
        throw new Exception("invalid appkey");
    }
    return true;
}

//频率，可以使用redis存储
function validateFrequency($sign) {
    $content = file_get_contents("./fre.log");
    $frequency = json_decode($content,true);
    $val = empty($frequency[$sign]) ? 0 : $frequency[$sign];
    var_dump($sign, $val);
    if($val > 1) {
        throw new Exception("out of frequency");
    }
    $frequency[$sign] += 1;
    file_put_contents('./fre.log', json_encode($frequency));
    var_dump("当前频率表：", $frequency);
}

//可以使用内置服务器启动
//php -S 127.0.0.1:9200 -t ./

$params = formatParams();
validateParams($params);

$sign = $params['sign'];
$csign = createSign($params);
if($sign != $csign) {
    throw new Exception("sign invalid");
}

validateFrequency($sign);

echo "Hello World";
exit;



