<?php


namespace App\Services;
use App\Models\Major;
use App\Models\MessageNotification;
use App\Models\Notification;
use App\Models\PaymentAmount;
use App\Models\PublicOffer;
use App\Models\SiteSetting;
use App\Models\UserPaymentAmount;
use Carbon\Carbon;
use http\Client\Curl\User;

class ViewData
{
    private $method;
    private $response;
    private $model;
    private $vars;
    public function init($model=null,$method='')
    {
        $this->method = $method;
        $this->model =  $model;
        $this->handeModel();
        return $this;
    }
    protected function handeModel(){
        $this->model = ($this->model===null) ? auth()->user() : $this->model;
    }
    public function type($vars = []){
        $this->vars = $vars;
        return $this;
    }

    protected function getResponse($view)
    {
        switch ($view) {

            case 'publicOffer':
                $all = PublicOffer::all();
                $text = '';
                $title = '';
                if($all->count()==2){
                    $text = $all[1]->text;
                    $title = $all[0]->text;
                }
                if($this->vars[0]=='title'){
                    $this->response = $title;
                }else{
                    $this->response = $text;
                }
                break;

            case 'paymentAmountsDetail':
                $this->model = auth()->user();
                $this->response = 'Сумма за добавление авто - '.$this->uniqueAmount('initial').' ₽.
                Сумма за завершение сделки - '.$this->uniqueAmount('success').' ₽';
                break;
            case 'numberOfNewNotifications':
                $this->response = MessageNotification::where('user_id',auth()->user()->id)->where('seen',0)->count();
                if($this->response>0){
                    $this->response = '<span class="notification-head-count blue">'.$this->response.'</span>';
                    break;
                }
                $this->response = '';
                break;
            case 'headerNotifications':
                    $notifications = MessageNotification::where('user_id',auth()->user()->id)->latest()->limit(10)->get();
                    $this->response = '';
                    foreach ($notifications as $notification){
                        if(!$notification->seen){
                            $this->response .= '<li class="notifications__item">'.$notification->message.'</li>'. PHP_EOL;
                            // $this->response .= '<li class="notifications__item notifications__new">'.$notification->message.'</li>'. PHP_EOL;
                        }else{
                            // $this->response .= '<li class="notifications__item">'.$notification->message.'</li>'. PHP_EOL;
                        }
                    }
                break;
            case 'telegramBot':
                   $s = SiteSetting::where('name','telegramBotToken');
                    $this->response = ($s->exists() ? str_replace('+7','',$s->first()->value) : '');
                break;
            case 'whatsapp':
                    $s = SiteSetting::where('name','whatsapp');
                     $this->response = ($s->exists() ? $s->first()->value : '');
                 break;
            case 'whatsappNumber':
                    $s = SiteSetting::where('name','whatsapp');
                     $this->response = ($s->exists() ? preg_replace('/[^0-9]/', '', $s->first()->value) : '');
                 break;
            case 'total_payments_by_lead':
                    call_user_func(array($this, 'totalPaymentByLead'), '');
                break;
            case 'qr':
                $this->response =  '<img src="{{ asset(`img/qr.png`) }}" alt="">';
                break;
            case 'email':
                if(old('email')){
                    $this->response = old('email');
                }else if($this->model !== null){
                    $this->response = $this->model->setting->email===null ? '' : $this->model->setting->email;
                }else{
                    $this->response = '';
                }
                break;
            case 'number':
                if(old('number')){
                    $this->response = old('number');
                }else if($this->model !== null){
                    $this->response = $this->model->setting->number===null ? '' : $this->model->setting->number;
                }else{
                    $this->response = '';
                }
                break;
            case 'promo_number':
                if(auth()->user()->promo===null)
                {

                    $this->response = substr(auth()->user()->setting->number, 2, strlen(auth()->user()->setting->number));
                }else{
                    $this->response = substr(auth()->user()->promo->phone, 2, strlen(auth()->user()->promo->phone));
                }
                break;
            case 'isEmailConfirmed':
                $this->response = ($this->model->email_verified_at !== null && $this->model->email == $this->model->setting->email) ?
                    'Подтверждён' :
                    "<button class='red-link' type='button' id='submitEmail' >Подтвердить</button>";
                if(old('email')){
                    $this->response = '';
                }
                break;
            case 'InvitationCode':
                $code = \Auth::user()->user_who_referred()!==false ? \Auth::user()->user_who_referred()->invitation_code : false;
                $disabled = $code===false ? '' : 'disabled';
                $mask = $code===false ? 'text' : 'password';
    //                $mask = 'text';
                $underText = $code===false ?
                    "<button class='red-link activatePromo' id='submitPromo' type='button' >Активировать</button>"
                    :'';
            if($code===false){
                $this->response ='<div class="inv-code red">
                                    <input type="text" class="form-control" id="invitation-inpup" name="invitationCode" placeholder="Код приглашения" value="'.$code.'">
                                </div><p class="error text-danger d-none">Такого промокода не существует</p>'.$underText;
            }else{
                $this->response ='<div class="inv-code success">
                                    <input type="'.$mask.'" class="form-control" id="invitation-inpup" placeholder="Код приглашения" value="'.$code.'" disabled>
                                </div>';
            }
//                $this->response = '<input type="'.$mask.'" class="form-control" id="invitation-inpup" name = "invitationCode" placeholder="Не указан" value="'.$code.'" '.$disabled.'>
//                                        <p class="error text-danger d-none">Такого промокода не существует</p>';

                break;

            case 'isPhoneConfirmed':
                $this->response = ($this->model->phone_verified_at !== null && $this->model->number ==$this->model->setting->number) ?
                    'Подтверждён' :
                    "<button class='red-link' id='submitPhone' type='button'>Подтвердить</button>";
                if(old('number')){
                    $this->response = '';
                }
                break;
            case 'isUniquePaymentChecked':
                ($this->model->UserPaymentAmounts->count()==0) ?
                    ($this->response = 'checked') :
                    $this->response = ($this->model->unique_payment==true) ? 'checked' : '';
                break;
            case 'isUniquePayment':
                ($this->model->UserPaymentAmounts->count()==0) ?
                    ($this->response = 'd-none') :
                    $this->response = ($this->model->unique_payment==true) ? '' : 'd-none';
                break;
            case 'amount_of_referral_payment':
                $this->method=='' ?
                    $this->defaultAmount('refer') :
                    call_user_func(array($this, $this->method), 'refer');
                break;
            case 'leadStatusName':
                    call_user_func(array($this, $this->method),'' );
                break;

            case 'amount_of_percentage_payment':
                $this->method=='' ?
                    $this->defaultAmount('percentage') :
                    call_user_func(array($this, $this->method), 'percentage');
                break;
            case 'amount_of_min_payment':
                $this->method=='' ?
                    $this->defaultAmount('MinAmountOfPayment') :
                    call_user_func(array($this, $this->method), 'MinAmountOfPayment');
                break;
            case 'amount_of_initial_payment':
                $this->method=='' ?
                    $this->defaultAmount('initial') :
                    call_user_func(array($this, $this->method), 'initial');
                break;
            case 'amount_of_success_payment':
                $this->method=='' ?
                    $this->defaultAmount('success') :
                    call_user_func(array($this, $this->method), 'success');
                break;
            case 'amount_of_nothing_payment':
                $this->method=='' ?
                    $this->defaultAmount('nothing') :
                    call_user_func(array($this, $this->method), 'nothing');
                break;
            case 'amount_of_rejected_payment':
                $this->method=='' ?
                    $this->defaultAmount('rejected') :
                    call_user_func(array($this, $this->method), 'rejected');
                break;
            case 'amount_of_firstPayment':
                $this->method=='' ?
                    $this->defaultAmount('firstPayment') :
                    call_user_func(array($this, $this->method), 'firstPayment');
                break;
            case 'majors':
                $majors = Major::pluck('name','id')->toArray();
                array_walk($majors,function (&$value, $key) {
                    if($this->model===null){
                        $value = '<option>' . $value . '</option>';
                    }else{
                        $this->model->setting->major_id==$key ?
                        $value = '<option selected>' . $value . '</option>':
                            $value = '<option>' . $value . '</option>';
                    }
                });
                $this->response = implode(PHP_EOL,$majors);
                break;
        }
        return $this;
    }
    public function totalPaymentByLead(){
        if($this->model!=null){
            $this->response = $this->model->all_amount();
        }
    }
    public function leadStatusName(){
        if($this->model!=null){
            $this->response = $this->model->status()->user_statuses->comments;
        }
    }
    protected function uniqueAmount($type){

        if($this->model->UserPaymentAmounts->count()==0){
            $this->defaultAmount($type);
        }else{
            if($this->model->UserPaymentAmounts->where('reason_of_payment',$type)->first()===null){
                $this->response = 0;
            }else{
                $this->response = $this->model->UserPaymentAmounts->where('reason_of_payment',$type)->first()->amount;
            }
        }
        return $this->response;
    }

    protected function defaultAmount($type){
        $this->response = PaymentAmount::where('reason_of_payment',$type)->first()->amount;
        return $this->response;
    }
    protected function route($type){

    }

    protected function checkError()
    {
//        if (isset($this->error->error) && ($this->error->error)) {
//            $this->errorMsg = $this->error->error_msg ?? '';
//        }
        return $this;
    }

    public function view($view)
    {
        $this->response = null;
        $this->error = null;
        $this->errorMsg = '';
        $this->responseMsg = '';
        $this->getResponse($view)->checkError()->getResponseMsg();
        return $this->responseMsg;
    }

    protected function getResponseMsg()
    {
        if ($this->errorMsg) {
            $this->responseMsg = $this->errorMsg;
        } else {
            $this->responseMsg = ($this->response!==null) ? $this->response : 'Не обнаружено';
        }
        return $this;
    }

    public function error()
    {
        return ($this->errorMsg != '') ? $this->errorMsg : 'Не обнаружено';
    }









}

