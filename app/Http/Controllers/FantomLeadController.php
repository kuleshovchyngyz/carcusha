<?php
namespace App\Http\Controllers;

use App\Models\Fantom;
use App\Models\Lead;
use App\Models\Status;
use App\Models\UserStatuses;
use App\support\Bitrix\ApiConnect;
use Illuminate\Http\Request;

class FantomLeadController extends Controller
{
    public function backToBirix(Lead $lead){

        dd($lead);
    }
    public function fantoms()
    {
        $bitrix_lead_ids = Fantom::pluck("bitrix_lead_id");
        $fantoms = Lead::with(["user", "status","fantom"])
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

        $acception_users = [94];
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
