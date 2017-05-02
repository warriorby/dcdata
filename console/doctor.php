<?php
/**
 * Created by PhpStorm.
 * User: xjwan
 * Date: 17-4-30
 * Time: 上午10:32
 */
require "../config/local_db.php";
require "../lib/HttpUtils.php";
require "../common/func.php";

//foreach(xrange(1,146) as $m){
    echo "---start---:\n";
    $url = "http://m.mingyizhudao.com/api/doctor?api=7&source=0&app=0&page=1&getcount=1";
    $rs2 = HttpUtils::httpGet($url);
    $arr = json_decode($rs2,true);

    if($arr['errorCode'] == 0){
        //城市医生
        foreach($arr['dataCity'] as $city){
            $city_id = $city['id'];
            echo $city['name']."\n";
            $i = 0;//跳出次数
            foreach(xrange(1,50) as $m){
                if($i>0){
                    break;
                }
                $city_url = "http://m.mingyizhudao.com/api/doctor?api=7&source=0&app=0&city=$city_id&page=$m&getcount=1";
                $city_rs = HttpUtils::httpGet($city_url);
                $city_arr = json_decode($city_rs,true);
                //判断是否为空,为空跳出本次循环
                if(empty($city_arr['results'])){
                    $i++;
                    continue;
                }else{
                    foreach($city_arr['results'] as $ct){
                        //判断是否存在医生
                        $doc_arr = $db->select("dc_doctor","name",["AND"=>['name'=>$ct['name'],'hospital'=>$ct['hpName']]]);
                        if(empty($doc_arr)){
                            $data = [
                                "name"=>$ct['name'],
                                "a_title"=>$ct['aTitle'],
                                "m_title"=>$ct['mTitle'],
                                "hospital"=>$ct['hpName'],
                                "dept_name"=>$ct['hpDeptName'],
                                "desc"=>$ct['desc'],
                                "updated"=>time()
                            ];
                            $id = $db->insert('dc_doctor',$data);
                            echo "---insert---:".$id."\n";
                        }else{
                            $ups = [
                                'city'=>$city['name'],
                                'updated'=>time()
                            ];
                            $up_res = $db->update("dc_doctor",$ups,["AND"=>['name'=>$ct['name'],'hospital'=>$ct['hpName']]]);
                            echo "---update---:".$up_res."\n";
                        }
                    }
                }
            }

        }
    }
    echo "---end---:\n";
//}
