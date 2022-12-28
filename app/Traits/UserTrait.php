<?php

namespace App\Traits;

use App\Models\MessageNotification;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PendingAmount;
use App\Models\Reason;
use Illuminate\Support\Facades\DB;

trait UserTrait
{

    public static function boot()
    {
        parent::boot();

        static::retrieved(function ($item) {

        });
        static::updated(function ($item) {


        });

        static::deleted(function ($item) {
        });
        static::deleting(function ($item) {
            DB::transaction(function () use ($item) {
                $reason_ids = Reason::where('reason_name', 'percent')->where('table_id', $item->id)->pluck('id')->toArray();
                $ids = Reason::where('reason_name', 'refer')->where('table_id', $item->id)->pluck('id')->toArray();
                $reason_ids = array_merge($reason_ids,$ids);
                $payment_ids = Payment::whereIn('reason_id', $reason_ids)->pluck('id');
                $pending_amount_ids = PendingAmount::whereIn('payment_id', $payment_ids)->delete();
                Payment::whereIn('id', $payment_ids)->delete();
                Reason::whereIn('id', $reason_ids)->delete();
                $reason_ids = [];
                foreach ($item->leads as $lead) {
                    $reason_ids = Reason::where('reason_name', 'lead')->where('table_id', $lead->bitrix_lead_id)->pluck('id');
                    $payment_ids = Payment::whereIn('reason_id', $reason_ids)->pluck('id');
                    $pending_amount_ids = PendingAmount::whereIn('payment_id', $payment_ids)->delete();
                    Payment::whereIn('id', $payment_ids)->delete();
                    Reason::whereIn('id', $reason_ids)->delete();
                    Notification::where('f_lead_id', $lead->id)->delete();
                    MessageNotification::where('lead_id', $lead->bitrix_lead_id)->delete();
                    $lead->delete();
                }
                optional($item->paymentSetting)->delete();
                optional($item->promo)->delete();
                optional($item->notifier)->delete();
                optional($item->paids())->delete();
                optional($item->violations())->delete();
                optional($item->UserPaymentAmounts())->delete();
                optional($item->setting)->delete();
                optional($item->balance)->delete();
                optional($item->refers())->delete();
                $ids = $item->payment()->pluck('id');
                PendingAmount::whereIn('payment_id',$ids)->delete();
                optional($item->payment())->delete();

            });

        });

        static::created(function ($item) {


        });
    }
}
