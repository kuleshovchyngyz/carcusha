<?php

namespace App\support\Leads;

use App\Models\Cars;
use App\Models\Vendors;

class DropDown
{
    public function buffer()
    {

        $results = Vendors::orderBy('name','ASC')->get();
//        $result = Cars::select('vendor','model','modification')->where('vendor','!=','')->orderBy('vendor','ASC')->get()->groupBy('vendor');
//        $result = Cars::select('vendor')->orderBy('vendor','ASC')->get()->groupBy('vendor');

        $buffer = '<option value="">Марка авто</option>';
        foreach($results as $result) {
            $car_vendor = stripslashes($result->name);
            $buffer .= '<option value="'.$car_vendor.'">'.$car_vendor.'</option>';
        }
        return $buffer;
    }
    public function get_car_years() {
        $buffer='';
//        $buffer = '<option value=2006>'.'2006'.' и старше</option>';
//        $buffer = '<option value=2010>'.'2010'.'</option>';
//        $buffer = '<option value=other>'.'другое'.'</option>';

        for($i = date("Y"); $i > date("Y")-15; $i--){
            $buffer .= '<option value="'.$i.'">'.$i.'</option>';
        }
        $buffer .= '<option value=other>'.'другое'.'</option>';
        return $buffer;
    }
    public function get_car_models()
    {
        $car_vendor = trim($_REQUEST['car_vendor']);
        $result = Cars::select('*')
            ->where('vendor',$car_vendor)
            ->where('model','!=','')
            ->orderBy('model','ASC')
            ->get()
            ->groupBy('model');

        $buffer = '<option value="">Модель авто</option>';

        foreach($result as $car_model => $car_model_data) {

            $car_model = stripslashes($car_model);
            $buffer .= '<option value="'.$car_model.'">'.$car_model.'</option>';
        }
        $buffer .= '<option value="Другая модель">Другая модель</option>';
        return $buffer;
    }
}
