<?php
function shortCodeParse($str,$arr = [],$value=[]){
    $value = \App\Models\PaymentAmount::all()->pluck('amount','reason_of_payment');
    $arr = \App\Models\PaymentAmount::all()->pluck('reason_of_payment');
    foreach ($arr as $item){

        $str = str_replace('['.$item.']',abs($value[$item]),$str);
    }
    $r = '/сумма\(.*?\)/';
    if(preg_match($r, $str, $matches)){
        foreach ($matches as $item){
            $i = $item;
            $item = str_replace('сумма','', $item);
            $item = str_replace('(','', $item);
            $item = str_replace(')','', $item);
            $item = explode('+',$item);
            $sum = (array_sum($item));
            $str = str_replace($i,$sum,$str);

        }
        //dump($matches);
    }

    return $str;
}

function mkmk(){
    return 'asdfasdf';
}
