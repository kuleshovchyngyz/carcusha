<?php
namespace App\Http\Controllers;

use App\Models\Fantom;
use App\Models\Lead;
use App\Models\Status;
use App\support\Bitrix\ApiConnect;
use Illuminate\Http\Request;

class FantomLeadController extends Controller
{
    public function fantoms()
    {
        $fantoms = Fantom::with(["lead", "user"])->get();
        //maksat

        return view("admin.fantoms", ["fantoms" => $fantoms]);
    }
    public function compareLeads()
    {


        $b = new ApiConnect();
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
        $leads = Lead::with(["status", "user"]);
        $fantoms_ids = $leads
            ->get()
            ->reject(function ($lead) use ($acception_users) {
                return $lead->status->status_type == "finished" ||
                    in_array($lead->user->id, $acception_users);
            })
            ->map(function ($value) {
                return $value->bitrix_lead_id;
            })
            ->diff($bitrix_leads_id)
            ->all();

            $fantoms = $leads->whereIn('bitrix_lead_id',$fantoms_ids)->get();

            return view('admin.fantoms',['fantoms'=>$fantoms]) ;
    }
}
