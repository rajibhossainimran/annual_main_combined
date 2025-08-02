<?php

namespace App\Services;

use App\Models\Notesheet;
use App\Models\NotesheetApproval;
use App\Models\NotesheetDemandPVMS;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class NotesheetService {
    public static function createNotesheet($data) {
        $notesheet = new Notesheet();
        $notesheet->notesheet_id = empty($data['notesheet_id'])? time():$data['notesheet_id'];
        $notesheet->notesheet_item_type = $data['notesheet_item_type'];
        $notesheet->total_items = $data['total_items'];
        $notesheet->total_demands = $data['total_demands'];
        $notesheet->notesheet_budget = $data['notesheet_budget'];
        $notesheet->notesheet_details = $data['notesheet_details'];
        $notesheet->notesheet_details1 = $data['notesheet_details1'];
        $notesheet->is_munir_keyboard = $data['is_munir_keyboard'];
        $notesheet->head_clark_note = $data['head_clark_note'];
        $notesheet->is_dental = $data['is_dental'];
        $notesheet->is_repair = $data['is_repair'];
        $notesheet->is_rate_running = $data['is_rate_running'];
        $notesheet->created_by = auth()->user()->id;
        $notesheet->updated_by = auth()->user()->id;
        if(!empty($data["NotesheetDate"])) {
            $notesheet->notesheet_date = $data["NotesheetDate"];
        }
        $notesheet->save();

        return $notesheet;
    }
    public static function createNotesheetDemandPVMS($notesheet_id,$demand_pvms_id,$demand_id,$pvms_id,$quantity,$is_repair,$unit_price) {
        $notesheet_demand_pvms = new NotesheetDemandPVMS();
        $notesheet_demand_pvms->notesheet_id = $notesheet_id;
        if($is_repair == 1) {
            $notesheet_demand_pvms->demand_repair_pvms_id = $demand_pvms_id;
        } else {
            $notesheet_demand_pvms->demand_pvms_id = $demand_pvms_id;
        }
        $notesheet_demand_pvms->demand_id = $demand_id;
        $notesheet_demand_pvms->pvms_id = $pvms_id;
        $notesheet_demand_pvms->total_quantity = $quantity;
        $notesheet_demand_pvms->created_by = auth()->user()->id;
        $notesheet_demand_pvms->updated_by = auth()->user()->id;
        $notesheet_demand_pvms->unit_price=$unit_price;
        $notesheet_demand_pvms->save();

        return $notesheet_demand_pvms;
    }

    public static function deleteNotesheet($notesheet_id) {
        $notesheet_approval = NotesheetApproval::where('notesheet_id',$notesheet_id)->delete();
        $notesheet_demand_pvms = NotesheetDemandPVMS::where('notesheet_id',$notesheet_id)->delete();
        $notesheet = Notesheet::find($notesheet_id)->delete();
        AuditService::AuditLogEntry(AuditModel::Notesheet,OperationTypes::Delete,$notesheet_id.' notesheet deleted',$notesheet,null,$notesheet_id);
        return true;
    }
}
