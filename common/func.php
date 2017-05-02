<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/13
 * Time: 11:21
 */

function xrange($start,$limit,$step =1){
    for ($i=$start;$i<=$limit;$i +=$step){
        yield $i;
    }
}