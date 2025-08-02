<?php

namespace App\Services;

use App\Models\Demand;
use App\Models\DemandApproval;
use App\Models\DemandDocument;
use App\Models\DemandPvms;
use App\Models\DemandRepairPvms;
use App\Models\Disease;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class DemandService
{
    public static function createDemand($data, $demand_item_type_id, $document_name)
    {

        $demand = new Demand();
        $demand->uuid = $data->DemandNo ? self::generateDemandNo($data->DemandNo, $demand_item_type_id) : time();
        $demand->sub_org_id = auth()->user()->sub_org_id;
        $demand->is_published = $data->is_published;
        $demand->is_dental_type = $data->isDentalType;
        if (!isset($data->demadType)) {
            $demand->demand_type_id = 3;
        } else {
            $demand->demand_type_id = $data->demadType;
        }
        $demand->demand_item_type_id = $demand_item_type_id;
        $demand->document_file = $document_name;
        $demand->description = isset($data->description) ? $data->description : null;
        $demand->description1 = isset($data->description1) ? $data->description1 : null;
        $demand->demand_category = isset($data->demand_category) ? $data->demand_category : null;
        $demand->created_by = auth()->user()->id;
        $demand->updated_by = auth()->user()->id;
        $demand->financialYear = isset($data->fy) ? $data->fy : null;
        if (!empty($data->demandDate)) {
            $demand->demand_date = $data->demandDate;
        }

        $demand->save();

        return $demand;
    }

    public static function createDemandPvms($demandPVMS, $demand_id)
    {
        $demand_pvms = new DemandPvms();

        $demand_pvms->demand_id = $demand_id;
        $demand_pvms->p_v_m_s_id = $demandPVMS->id;
        $demand_pvms->qty = $demandPVMS->qty;
        $demand_pvms->reviewd_qty = $demandPVMS->qty;
        $demand_pvms->patient_name = $demandPVMS->patient_name;
        $demand_pvms->patient_id = empty($demandPVMS->patient_id) ? 0 : $demandPVMS->patient_id;
        $demand_pvms->disease = $demandPVMS->disease;
        $demand_pvms->authorized_machine = $demandPVMS->authorized_machine;
        $demand_pvms->existing_machine = $demandPVMS->existing_machine;
        $demand_pvms->running_machine = $demandPVMS->running_machine;
        $demand_pvms->disabled_machine = $demandPVMS->disabled_machine;
        $demand_pvms->ward = $demandPVMS->ward;
        $demand_pvms->remarks = $demandPVMS->remarks;

        $demand_pvms->prev_purchase = !isset($demandPVMS->prev_purchase) ? null : $demandPVMS->prev_purchase;
        $demand_pvms->present_stock = !isset($demandPVMS->present_stock) ? null : $demandPVMS->present_stock;
        $demand_pvms->proposed_reqr = !isset($demandPVMS->proposed_reqr) ? null : $demandPVMS->proposed_reqr;

        $demand_pvms->save();

        return $demand_pvms;
    }

    public static function createDisease($disease_name)
    {
        $disease = Disease::where('diseases_name', $disease_name)->first();

        if (!$disease) {
            $disease = new Disease();
            $disease->diseases_name = $disease_name;
            $disease->save();
        }

        return $disease;
    }

    public static function createDemandRepairPvms($repairPVMS, $demand_id)
    {
        $demand_repair_pvms = new DemandRepairPvms();
        $demand_repair_pvms->demand_id = $demand_id;
        $demand_repair_pvms->p_v_m_s_id = $repairPVMS->id;
        $demand_repair_pvms->issue_date = date('Y-m-d', strtotime($repairPVMS->issue_date));
        $demand_repair_pvms->installation_date = date('Y-m-d', strtotime($repairPVMS->installation_date));
        $demand_repair_pvms->warranty_date = date('Y-m-d', strtotime($repairPVMS->warranty_date));
        $demand_repair_pvms->authorized_machine = $repairPVMS->authorized_machine;
        $demand_repair_pvms->existing_machine = $repairPVMS->existing_machine;
        $demand_repair_pvms->running_machine = $repairPVMS->running_machine;
        $demand_repair_pvms->disabled_machine = $repairPVMS->disabled_machine;
        $demand_repair_pvms->approved_qty = $repairPVMS->disabled_machine;
        $demand_repair_pvms->supplier = $repairPVMS->supplier;
        $demand_repair_pvms->remarks = $repairPVMS->remarks;

        $demand_repair_pvms->save();

        return $demand_repair_pvms;
    }

    public static function generateDemandNo($demand_no, $demand_item_type_id)
    {
        $generated_demand_no = $demand_no;
        if (Demand::where('uuid', $demand_no)->first()) {
            $type_nick_name = '';
            if ($demand_item_type_id == 1) {
                $type_nick_name = 'em';
            } else if ($demand_item_type_id == 3) {
                $type_nick_name = 'ma';
            } else if ($demand_item_type_id == 4) {
                $type_nick_name = 'cr';
            } else if ($demand_item_type_id == 5) {
                $type_nick_name = 'di';
            }
            $generated_demand_no = $demand_no . '-' . $type_nick_name;
        }

        return $generated_demand_no;
    }

    public static function deleteDemand($demand_id)
    {
        $demand_approval = DemandApproval::where('demand_id', $demand_id)->delete();
        $demand_documents = DemandDocument::where('demand_id', $demand_id)->delete();
        $demand_pvms = DemandPvms::where('demand_id', $demand_id)->delete();
        $demand_reapair_pvms = DemandRepairPvms::where('demand_id', $demand_id)->delete();

        $demand = Demand::find($demand_id)->delete();
        AuditService::AuditLogEntry(AuditModel::Demand, OperationTypes::Delete, $demand_id . ' demand deleted', $demand, null, $demand_id);
        return true;
    }
}
