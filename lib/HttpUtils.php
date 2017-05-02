<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/30
 * Time: 15:48
 */

class HttpUtils {

    /**
     * 使用curl扩展请求url
     *
     * @param $url
     * @param bool|false $https
     * @return mixed
     */
    static function httpGet($url,$https=false,$callback = null)
    {
        // Logger::Instance()->error("GET URL:$url");

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 200);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_BUFFERSIZE,102400);
        if ($https) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if(is_callable($callback)){
            $callback($ch);
        }

        $file_contents = curl_exec($ch);
        $errNo = curl_errno($ch);
        if($errNo){
            var_dump($errNo,curl_error($ch));
        }

        curl_close($ch);

        //Logger::Instance()->error("RESPONSE::$file_contents");
        return $file_contents;
    }


    static function httpPost($url, $post_data = '', $https = false,$data_type = 'form',$callback = null){
        // Logger::Instance()->error("POST URL:$url\n");
        // Logger::Instance()->error("POST DATA:" . var_export($post_data,true) . "\n");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5000);
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch,CURLOPT_BUFFERSIZE,102400);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        if($data_type == 'json'){
            if(is_array($post_data)) $post_data = json_encode($post_data,JSON_UNESCAPED_UNICODE);
            curl_setopt(
                $ch,
                CURLOPT_HTTPHEADER,
                array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($post_data)
                )
            );
        }

        if($post_data != ''){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        }
        if($https){
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true);
        }
        if(is_callable($callback)){
            $callback($ch);
        }
        $file_contents = curl_exec($ch);
        // Logger::Instance()->error("RESPONSE::{$file_contents}");
        curl_close($ch);
        return $file_contents;
    }

    static function httpPostJson($url,$post_data,$https = false){
        $responseText = self::httpPost($url,$post_data,$https,'json');
        $res = json_decode($responseText,true);
        return $res;
    }

    static function redirect($uri = '', $method = 'location', $http_response_code = 302)
    {
        switch($method)
        {
            case 'refresh'	: @header("Refresh:0;url=".$uri);
                break;
            default			: @header("Location: ".$uri, TRUE, $http_response_code);
                break;
        }
        exit;
    }


    public static function httpRequest($url,$data = []){

        $data = http_build_query($data);        //$postdata = http_build_query($data);

        if($data){
            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => 'Content-type:application/x-www-form-urlencoded',
                    'content' => $data
                    //'timeout' => 60 * 60 // 超时时间（单位:s）
                )
            );
        }else{
            $options = array(
                'http' => array(
                    'method' => 'GET',
                    'header' => 'Content-type:application/x-www-form-urlencoded',
                    //'timeout' => 60 * 60 // 超时时间（单位:s）
                )
            );
        }

        $context = stream_context_create($options);
        $result = file_get_contents($url, false, $context,null,1024000);
        return $result;
    }

}