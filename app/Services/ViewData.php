<?php


namespace App\Services;
use App\Models\PaymentAmount;
use Carbon\Carbon;
use http\Client\Curl\User;

class ViewData
{
    private $method;
    private $response;
    private $user;
    public function init($user=null,$method='')
    {
        $this->method = $method;
        $this->user = ($user===null) ? auth()->user() : $user;
        return $this;
    }

    protected function getResponse($view)
    {
        switch ($view) {

            case 'qr':
                $this->response =  '<img src="{{ asset(`img/qr.png`) }}" alt="">';
                break;
            case 'email':
                if(old('email')){
                    $this->response = old('email');
                }else if($this->user !== null){
                    $this->response = $this->user->setting->email===null ? '' : $this->user->setting->email;
                }else{
                    $this->response = '';
                }
                break;
            case 'number':
                if(old('number')){
                    $this->response = old('number');
                }else if($this->user !== null){
                    $this->response = $this->user->setting->number===null ? '' : $this->user->setting->number;
                }else{
                    $this->response = '';
                }
                break;
            case 'isEmailConfirmed':
                $this->response = ($this->user->email_verified_at !== null && $this->user->email ==$this->user->setting->email) ?
                    'Подтверждён' :
                    "<a class='red-link' href='#' onclick='submitEmail()' >Подтвердить</a>";
                if(old('email')){
                    $this->response = '';
                }
                break;
            case 'isPhoneConfirmed':
                $this->response = ($this->user->phone_verified_at !== null && $this->user->number ==$this->user->setting->number) ?
                    'Подтверждён' :
                    "<a class='red-link' href='#' onclick='submitPhone()' >Подтвердить</a>";
                if(old('number')){
                    $this->response = '';
                }
                break;
            case 'isUniquePaymentChecked':
                ($this->user->UserPaymentAmounts->count()==0) ?
                    ($this->response = 'checked') :
                    $this->response = ($this->user->unique_payment==true) ? 'checked' : '';
                break;
            case 'isUniquePayment':
                ($this->user->UserPaymentAmounts->count()==0) ?
                    ($this->response = 'd-none') :
                    $this->response = ($this->user->unique_payment==true) ? '' : 'd-none';
                break;
            case 'amount_of_referral_payment':
                $this->method=='' ?
                    $this->defaultAmount('refer') :
                    call_user_func(array($this, $this->method), 'refer');
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
        }
        return $this;
    }

    protected function uniqueAmount($type){
        if($this->user->UserPaymentAmounts->count()==0){
            $this->defaultAmount($type);
        }else{
            $this->response = $this->user->UserPaymentAmounts->where('reason_of_payment',$type)->first()->amount;
        }
    }
    protected function defaultAmount($type){
        $this->response = PaymentAmount::where('reason_of_payment',$type)->first()->amount;
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

