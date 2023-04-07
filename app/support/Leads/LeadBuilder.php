<?php

namespace App\support\Leads;

use App\Models\Lead;
use App\Models\Status;
use App\Models\User;

class LeadBuilder extends UpdatingLeadStatus
{

    public $vendor;
    public $vendor_model;
    public $vendor_year;
    public $phone;
    public $folder;
    public $user_id;
    public $bitrix_lead_id;
    public $status;
    public $from;
    public $comment;
    private $reason_id;
    private $reason;
    private $payment;

    public function __construct($vendor = '', $vendor_model = '', $vendor_year = '', $phone = '', $folder = '', $user_id = '', $bitrix_lead_id = '', $from = 0, $comment = '')
    {
        $this->vendor = $vendor;
        $this->vendor_model = $vendor_model;
        $this->vendor_year = $vendor_year;
        $this->phone = $phone;
        $this->folder = $folder;
        $this->user_id = $user_id;
        $this->user = User::find($user_id);
        $this->status = Status::where('name', 'Новый лид')->first();
        $this->bitrix_lead_id = $bitrix_lead_id;
        $this->from = $from;
        $this->comment = $comment;
        $this->create_lead();
        parent::__construct($bitrix_lead_id, 1);
    }

    public function create_lead()
    {
        Lead::create([
            'vendor' => $this->vendor,
            'vendor_model' => $this->vendor_model,
            'vendor_year' => $this->vendor_year,
            'phonenumber' => $this->phone,
            'folder' => $this->folder,
            'user_id' => $this->user_id,
            'bitrix_lead_id' => $this->bitrix_lead_id,
            'status_id' => 1,
            'number' => $this->from,
            'comment'=>$this->comment
        ]);

    }
}
