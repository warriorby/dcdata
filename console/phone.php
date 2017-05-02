<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 10:05
 */
require "db.php";
require "HttpUtils.php";
require "func.php";

$a = 433000;//开始行数
$b = 100;//总共查询条数

$count_arr = $db->query("SELECT count(*) as counts from dc_phone WHERE province is null and updated is null")->fetchAll();
$c = $count_arr['0']['counts'];
$ci = ceil($c/$b);
foreach (xrange(0,$ci-1) as $m){
    echo "---start---:".$m."\n";
    run();
    echo "---end---:".$m."\n";
}

function run(){
    global $db;
    global $b;
    $rs = $db->select("dc_phone",["phone"],["AND"=>["province"=>null,"updated"=>null],'LIMIT'=>$b]);
    $url = "http://apis.juhe.cn/mobile/get?";
    foreach (xrange(0,$b-1) as $num){
        $param = ["key"=>"d92a8fcfe783b55f7802a9805721710b","phone"=>$rs[$num]['phone']];
        $param_str = http_build_query($param);
        $str = $url.$param_str;
        $rs2 = HttpUtils::httpGet($str);
        $arr = json_decode($rs2,true);
        echo $arr['reason']."\n";
        if($arr['error_code'] == 0){
            $ups = [
//                "prefix"=>$arr['data']['prefix'],
                "province"=>$arr['result']['province'],
                "city"=>$arr['result']['city'],
                "isp"=>$arr['result']['company'],
                "types"=>$arr['result']['card'],
                "updated"=>time()
            ];
            $db->update("dc_phone",$ups,['phone'=>$rs[$num]['phone']]);
        }else{
            $ups = [
                "updated"=>time()
            ];
            $db->update("dc_phone",$ups,['phone'=>$rs[$num]['phone']]);
        }

    }
}