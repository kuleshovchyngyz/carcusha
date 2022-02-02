<?php

namespace App\Http\Controllers;

use App\Clients\Bitrix;
use App\Models\Fantom;
use App\Models\Lead;
use App\Models\MessageNotification;
use App\Models\Notification;
use App\Models\Payment;
use App\Models\PendingAmount;
use App\Models\Reason;
use App\Models\Status;
use App\Models\UserStatuses;
use App\support\Bitrix\ApiConnect;
use App\support\Leads\Pay;
use App\support\Leads\UpdatingLeadStatus;
use Illuminate\Http\Request;

class FantomLeadController extends Controller
{
    public function close(Lead $lead){
        $s = Status::where('ID_on_bitrix','close')->first();
        Fantom::where('bitrix_lead_id',$lead->bitrix_lead_id)->delete();
        new UpdatingLeadStatus($lead->bitrix_lead_id,$s);
        return redirect()->back()->with('success_message', [__('Закрыто')]);
    }
    public function delete(Lead $lead){

        $s = Status::where('ID_on_bitrix','delete')->first();
        Fantom::where('bitrix_lead_id',$lead->bitrix_lead_id)->delete();
        new UpdatingLeadStatus($lead->bitrix_lead_id,$s->id);

        $reason_ids = Reason::where('reason_name','lead')->where('table_id',$lead->bitrix_lead_id)->pluck('id');
        $payment_ids = Payment::whereIn('reason_id',$reason_ids)->pluck('id');
        $pending_amount_ids = PendingAmount::whereIn('payment_id',$payment_ids)->delete();
        Payment::whereIn('id',$payment_ids)->delete();
        Reason::whereIn('id',$reason_ids)->delete();
        Notification::where('f_lead_id',$lead->id)->delete();
        MessageNotification::where('lead_id',$lead->bitrix_lead_id)->delete();
        $lead->delete();
        return redirect()->back()->with('success_message', [__('Удалено')]);

        // \App\Models\PendingAmount::latest()->first()->delete();
        // \App\Models\Payment::latest()->first()->delete();
        // \App\Models\Reason::latest()->first()->delete();
        // \App\Models\Notification::latest()->first()->delete();
        // \App\Models\MessageNotification::latest()->first()->delete();
    }
    public function backToBirix(Lead $lead)
    {
        $bitrix = new Bitrix();
        $bitrix->addDeal(
            $lead->vendor ?? "",
            $lead->vendor_model ?? "",
            $lead->vendor_year ?? "",
            $lead->phonenumber,
            $lead->folder,
            $lead->status->index
        );
        $result = $bitrix->addLead();
        $lead->bitrix_lead_id = $result["result"];
        $lead->save();
    }

    public function fantoms()
    {
        $bitrix_lead_ids = Fantom::pluck("bitrix_lead_id");
        $fantoms = Lead::with(["user", "status", "fantom"])
            ->whereIn("bitrix_lead_id", $bitrix_lead_ids)
            ->get();
        $user_statuses = UserStatuses::pluck("name", "id")->toArray();

        return view("admin.fantoms", [
            "fantoms" => $fantoms,
            "statuses" => $user_statuses,
        ]);
    }

    public function compareLeads()
    {
        $b = new ApiConnect();
        $fantoms_ids = [];
        $result = $b->getLeadList();
        $bitrix_leads = $result["result"] ?? [];
        do {
            $data = $b::crmLeadListData;
            $data["start"] = $result["next"];
            $b->setData($data);
            $b->execute();
            $result = $b->getResponse();
            $bitrix_leads = array_merge($bitrix_leads, $result["result"]);
        } while (isset($result["next"]));

        $pendingStatuses = Status::where("status_type", "pending")
            ->pluck("index")
            ->toArray();

        $bitrix_leads_id = collect($bitrix_leads)
            ->filter(function ($value) use ($pendingStatuses) {
                return in_array($value["STATUS_ID"], $pendingStatuses);
            })
            ->pluck("ID");

        $acception_users = [];
        $acception_lead_ids = Fantom::pluck("bitrix_lead_id")->toArray();

        $leads = Lead::with(["status", "user"]);
        $lead_ids = $leads
            ->get()
            ->reject(function ($lead) use (
                $acception_users,
                $acception_lead_ids
            ) {
                return $lead->status->status_type == "finished" ||
                    in_array($lead->user->id, $acception_users) ||
                    in_array($lead->bitrix_lead_id, $acception_lead_ids);
            })
            ->map(function ($value) {
                return $value->bitrix_lead_id;
            });
        if (count($lead_ids) > 0) {
            $fantoms_ids = $lead_ids
                ->diff($bitrix_leads_id)
                ->map(function ($value) {
                    return [
                        "bitrix_lead_id" => $value,
                        "created_at" => \Carbon\Carbon::now(),
                        "updated_at" => \Carbon\Carbon::now(),
                    ];
                })
                ->toArray();
            Fantom::insert($fantoms_ids);
        }
    }
}
