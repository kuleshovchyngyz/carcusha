<?php
function shortCodeParse($str,$arr = [],$value=[],$firstPayment = false,$color=false){
    $value = \App\Models\PaymentAmount::pluck('amount','reason_of_payment');
    $arr = \App\Models\PaymentAmount::pluck('reason_of_payment');
    foreach ($arr as $item){
        if($color){
            if($firstPayment && ($item == 'success')){
                $str = str_replace('['.$item.']','<span class="statusRed">'.abs($value['firstPayment']).'</span>',$str);
            }else{
                $str = str_replace('['.$item.']','<span class="statusRed">'.abs($value[$item]).'</span>',$str);
            }
            $str = str_replace(['руб','₽'],['<span class="statusRed">'.'руб'.'</span>','<span class="statusRed">'.'₽'.'</span>'],$str);
        }else{
            if($firstPayment && ($item == 'success')){
                $str = str_replace('['.$item.']',abs($value['firstPayment']),$str);
            }else{
                $str = str_replace('['.$item.']',abs($value[$item]),$str);
            }
        }
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

function selectedColor($color,$current){
    $result = '';
    switch ($color){
        case '#2D9CDB':
            $current == 'blue' ?
                ($result = 'checked'):
                ($result = '');
            break;
        case '#27AE60':
            $current == 'green' ?
                ($result = 'checked'):
                ($result = '');
            break;
        case '#EB5757':
            $current == 'red' ?
                ($result = 'checked'):
                ($result = '');
            break;
    }
    return $result;
}

function color($color){
    if($color=='#EB5757'){
        return 'statusRed';
    }
    if($color=='#27AE60'){
        return 'statusGreen';
    }
    if($color=='#2D9CDB'){
        return 'statusBlue';
    }
    return '';
}
