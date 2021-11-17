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
        $this->user =$user;
        return $this;
    }

    protected function getResponse($view)
    {
        switch ($view) {
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
            $this->responseMsg = ($this->response!='') ? $this->response : 'Не обнаружено';
        }
        return $this;
    }

    public function error()
    {
        return ($this->errorMsg != '') ? $this->errorMsg : 'Не обнаружено';
    }









}

