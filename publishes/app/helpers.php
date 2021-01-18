<?php

use Cyvelnet\Laravel5Fractal\Facades\Fractal;


/**
 *
 */
function route_class()
{
    return str_replace('.', '-', Route::currentRouteName());
}

/**
 * 生成随机字符串
 * @param int $length 长度
 * @return string 字符串
 */
function get_str_random($length = 6) {
    return get_random($length, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function get_random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * name 发送 post
 * @param $url
 * @param $request
 * @param $type
 * @return bool|string
 * by Curator
 * at 2019/6/4 10:10
 */

function sendPost($url, $request, $type = null)
{
    try{
        $curl = curl_init();//初始化curl
        curl_setopt($curl, CURLOPT_URL, $url);//抓取指定网页
        curl_setopt($curl, CURLOPT_HEADER, 0);//设置header
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
        curl_setopt($curl, CURLOPT_POST, 1);//post提交方式

        // header 补充
        switch ($type){
            case 'character_recognition':
                // 设置 x-www-form-urlencoded
                curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
                // 数据类型转变
                $request = http_build_query($request);
                $headers = array(
                    'Content-Type'  => 'application/x-www-form-urlencoded'
                );
                break;
            default:
                $headers = false;
        }
        if ($headers){
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        }

        curl_setopt($curl, CURLOPT_POSTFIELDS, $request);

        $result = curl_exec($curl);//运行curl
        curl_close($curl);
        return $result;
    }catch (Exception $e){
        dd($e);
    }

}


/**
 * name 图片转 base64 缺少头部信息
 * @param $ImageFile
 * @return bool|string
 * by Curator
 * at 2019/6/4 10:10
 */

function Base64EncodeImageExpenseMime($ImageFile)
{
    if (file_exists($ImageFile) || is_file($ImageFile)) {
        $image_data = fread(fopen($ImageFile, 'r'), filesize($ImageFile));
        $base64_image = chunk_split(base64_encode($image_data));
        return $base64_image;
    } else {
        return false;
    }
}

/**
 * name 图片转 base64
 * @param $ImageFile
 * @return bool|string
 * by Curator
 * at 2019/6/4 10:10
 */

function Base64EncodeImage($ImageFile)
{
    if (file_exists($ImageFile) || is_file($ImageFile)) {
        $image_info = getimagesize($ImageFile);
        $image_data = fread(fopen($ImageFile, 'r'), filesize($ImageFile));
        $base64_image = 'data:' . $image_info['mime'] . ';base64,' . chunk_split(base64_encode($image_data));
        return $base64_image;
    } else {
        return false;
    }
}

// 推送layuiAdmin表格

/**
 * name 函数名
 * @param $fractal Fractal
 * @return array
 * by mail
 * at 2019/10/24 20:49
 */

function layuiTable($fractal)
{
    $response = $fractal->getArray();
    if (empty($response['meta'])) return ['code'=>0, 'data'=>$response['data']];
    else return ['code'=>0, 'data'=>$response['data'], 'count'=>$response['meta']['pagination']['total']];
}

// 格式化金额
function formatMoney($obj, $field = 'money')
{
    if (is_numeric($obj))
        return number_format($obj, 2);
    else {
        unset($item);
        foreach ($obj as &$item) {
            if (is_array($field)) {
                foreach ($field as $key) {
                    $item->$key = number_format($item->$key, 2);
                }
            } else $item->$field = number_format($item->$field, 2);
        }
        unset($item);
        return $obj;
    }
}

// 格式化数字
function formatDecimal($decimal)
{
        return round($decimal,2);
}

// 格式化显示手机
function formatShowPhone($phone)
{
    if (strlen($phone) == 11) {
        return substr($phone, 0, 3) . ' ' . substr($phone, 3, 4) . ' ' . substr($phone, 7);
    } else {
        return $phone;
    }
}

// 格式化隐藏手机
function formatHiddenPhone($phone)
{
    if (strlen($phone) == 11) {
        return substr($phone, 0, 3) . ' **** ' . substr($phone, 7);
    } else {
        return substr($phone, 0, 3) . '???';
    }
}

// 金额去逗号
function replaceMoney($string)
{
    if (is_array($string) || is_object($string)) {
        $return = array();
        foreach ($string as $item) {
            $return[] = (float)str_replace(',', '', $item);
        }
        return $return;
    }else return (float)str_replace(',', '', $string);
}

// 日期-时间 转 日期
function datetimeToDate($string)
{
    $result = explode(' ', $string);
    if (is_array($result)) return $result[0];
    else return $result;
}

function timestampToExcelDate($time)
{
    if (date('Y-m-d H:i:s', strtotime($time)) == $time) return date('Y/m/d', strtotime($time));
    else return '';
}

// 返回成功数据
function successResult($msg = '操作成功', $data = '') {
    return [
        'status_code'   => 200,
        'message'       => $msg,
        'data'          => $data,
    ];
}

// 返回错误数据
function errorResult($msg = '操作失败') {
    return [
        'status_code'   => 422,
        'message'       => $msg,
    ];
}

// 关闭layer弹窗
function closeLayer($message = null)
{
    $response = '<script>';
    // 若存在通知信息
    if ($message) $response .= 'top.msgSuccess("'.$message.'");';
    // 测试版加入报错信息说明
    $response .= 'let layerIndex = parent.layer.getFrameIndex(window.name);parent.layer.close(layerIndex);</script>';
    return $response;
}


// 驼峰转下划线
function createUnderScore($string)
{
    return lcfirst(strtolower(preg_replace('/((?<=[a-z])(?=[A-Z]))/', '_', $string)));
}

// 下划线转驼峰
function $this->createBigHump($uncamelized_words, $separator = '_')
{
    $uncamelized_words = $separator . str_replace($separator, " ", strtolower($uncamelized_words));
    return ucfirst(ltrim(str_replace(" ", "", ucwords($uncamelized_words)), $separator));
}

/**
 * name 单词单数转成复数
 * @param $string
 * @return mixed
 * by '.$this->articleName.'
 * at 2019/6/19 9:27
 */

function $this->pluralize($string)
{
    $string = strtolower($string);
    //plural pattern
    $plural = array(
        array('/(quiz)$/i', "$1zes"),
        array('/^(ox)$/i', "$1en"),
        array('/([m|l])ouse$/i', "$1ice"),
        array('/(matr|vert|ind)ix|ex$/i', "$1ices"),
        array('/(x|ch|ss|sh)$/i', "$1es"),
        array('/([^aeiouy]|qu)y$/i', "$1ies"),
        array('/([^aeiouy]|qu)ies$/i', "$1y"),
        array('/(hive)$/i', "$1s"),
        array('/(?:([^f])fe|([lr])f)$/i', "$1$2ves"),
        array('/sis$/i', "ses"),
        array('/([ti])um$/i', "$1a"),
        array('/(buffal|tomat)o$/i', "$1oes"),
        array('/(bu)s$/i', "$1ses"),
        array('/(alias|status)$/i', "$1es"),
        array('/(octop|vir)us$/i', "$1i"),
        array('/(ax|test)is$/i', "$1es"),
        array('/s$/i', "s"),
        array('/$/', "s")
    );


    //irregular 不规则
    $irregular = array(
        array('move', 'moves'),
        array('sex', 'sexes'),
        array('child', 'children'),
        array('man', 'men'),
        array('person', 'people')
    );
    //uncountable 不可数
    $uncountable = array(
        'sheep',
        'fish',
        'series',
        'species',
        'money',
        'rice',
        'information',
        'equipment'
    );

    if (in_array($string, $uncountable)) {
        return $string;
    }

    foreach ($irregular as $noun) {
        if ($string == $noun[0]) {
            return $noun[1];
        }
    }

    foreach ($plural as $pattern) {
        $str = preg_replace($pattern[0], $pattern[1], $string);
        if ($str !== null && $str != $string) {
            return $str;
        }
    }
}
/**
 * name 判断是否为手机
 * @return bool
 * by mail
 * at 2020/1/10 12:34
 */
function isMobile()
{
    // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
    if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])) {
        return TRUE;
    }

    // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
    if (isset ($_SERVER['HTTP_VIA'])) {
        return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
    }

    // 判断手机发送的客户端标志,兼容性有待提高
    if (isset ($_SERVER['HTTP_USER_AGENT'])) {
        $clientKeywords = array(
            'mobile','nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap'
        );

        // 从HTTP_USER_AGENT中查找手机浏览器的关键字
        if (preg_match("/(" . implode('|', $clientKeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
            return TRUE;
        }
    }

    if (isset ($_SERVER['HTTP_ACCEPT'])) { // 协议法，因为有可能不准确，放到最后判断
        // 如果只支持wml并且不支持html那一定是移动设备
        // 如果支持wml和html但是wml在html之前则是移动设备
        if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
            return TRUE;
        }
    }
    return FALSE;

}
