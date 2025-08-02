<?php

namespace App\Services;

use App\Models\AnnualDemand;
use App\Models\AnnualDemandDepatment;
use App\Models\AnnualDemandPvms;
use App\Models\AnnualDemandPvmsUnitDemand;
use App\Models\AnnualDemandUnit;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class AnnualDemandService {
    public static function createAnnualDemand($data) {
        $annual_demand = AnnualDemand::where('financial_year_id',$data["financial_year_id"])->first();

        if(!$annual_demand){
            $annual_demand = new AnnualDemand();
            $annual_demand->financial_year_id = $data["financial_year_id"];
            $annual_demand->save();
            AuditService::AuditLogEntry(AuditModel::AnnualDemand,OperationTypes::Create,"Create annual demand for financial year ".$annual_demand->financialYear->name,null,$annual_demand,$annual_demand->id);
        }

        return $annual_demand;
    }
    public static function createAnnualDemandUnit($data) {
        $annual_demand_unit = AnnualDemandUnit::where('annual_demand_id',$data["annual_demand_id"])
                                    ->where('sub_org_id',auth()->user()->sub_org_id)->first();
        if(!$annual_demand_unit){
            $annual_demand_unit = new AnnualDemandUnit();
            $annual_demand_unit->annual_demand_id = $data["annual_demand_id"];
            $annual_demand_unit->sub_org_id = auth()->user()->sub_org_id;
            $annual_demand_unit->save();
        }

        if(isset($data['is_ready'])) {
            $annual_demand_unit->is_ready = $data["is_ready"];
            $annual_demand_unit->save();
        }

        return $annual_demand_unit;
    }

    public static function createAnnualDemandDepartment($annual_demand_id,$department_id) {
        $annual_demand_department = AnnualDemandDepatment::where('annual_demand_id',$annual_demand_id)->where('department_id',$department_id)->first();
        if(!$annual_demand_department) {
            $annual_demand_department = new AnnualDemandDepatment();
            $annual_demand_department->annual_demand_id = $annual_demand_id;
            $annual_demand_department->department_id = $department_id;
            $annual_demand_department->save();
        }
        return $annual_demand_department;
    }

    public static function createAnnualDemandDepartmentPvms($annual_demand_depatment_id,$pvms_id) {
        $annual_demand_pvms = AnnualDemandPvms::where('annual_demand_depatment_id',$annual_demand_depatment_id)->where('pvms_id',$pvms_id)->first();
        if(!$annual_demand_pvms) {
            $annual_demand_pvms = new AnnualDemandPvms();
            $annual_demand_pvms->annual_demand_depatment_id = $annual_demand_depatment_id;
            $annual_demand_pvms->pvms_id = $pvms_id;
            $annual_demand_pvms->save();
            AuditService::AuditLogEntry(AuditModel::AnnualDemandPvms,OperationTypes::Create,"Add annual demand pvms",null,$annual_demand_pvms,$annual_demand_pvms->id);
        }
        return $annual_demand_pvms;
    }
    public static function createAnnualDemandPvmsUnitDemand($annual_demand_unit_id,$annual_demand_pvms) {
        $annual_demand_pvms_unit_demand = AnnualDemandPvmsUnitDemand::where('annual_demand_unit_id',$annual_demand_unit_id)
                                            ->where('annual_demand_pvms_id',$annual_demand_pvms['annual_demand_pvms_id'])
                                            ->first();
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';
        if(!$annual_demand_pvms_unit_demand) {
            $annual_demand_pvms_unit_demand = new AnnualDemandPvmsUnitDemand();
            $annual_demand_pvms_unit_demand->annual_demand_pvms_id = $annual_demand_pvms['annual_demand_pvms_id'];
            $annual_demand_pvms_unit_demand->annual_demand_unit_id = $annual_demand_unit_id;
            $operation = OperationTypes::Create;
            $description = 'Annual Demand estimated quantity added by unit';
        } else {
            $old_data = $annual_demand_pvms_unit_demand->getOriginal();
            $operation = OperationTypes::Update;
            $description = 'Annual Demand estimated quantity modified by unit';
        }
        $annual_demand_pvms_unit_demand->estimated_qty = $annual_demand_pvms['estimated_qty'];
        $annual_demand_pvms_unit_demand->unit_remarks = $annual_demand_pvms['unit_remarks'];
        $annual_demand_pvms_unit_demand->save();
        AuditService::AuditLogEntry(AuditModel::AnnualDemandPvmsUnitDemand,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$annual_demand_pvms_unit_demand,$annual_demand_pvms_unit_demand->id);
        return $annual_demand_pvms_unit_demand;
    }
    public static function updateAnnualDemandPvmsUnitDemand($id,$data) {
        $new_data = null;
        $old_data = null;
        $description = 'Annual demand unit estimation has been modified';
        $annual_demand_pvms_unit_demand = AnnualDemandPvmsUnitDemand::where('id',$id)->first();
        $old_data = $annual_demand_pvms_unit_demand->getOriginal();

        $annual_demand_pvms_unit_demand->estimated_qty = $data['estimated_qty'];
        $annual_demand_pvms_unit_demand->unit_remarks = $data['unit_remarks'];

        if(isset($data['afmsd_qty'])) {
            $annual_demand_pvms_unit_demand->afmsd_qty = $data['afmsd_qty'];
        }
        if(isset($data['dg_qty'])) {
            $annual_demand_pvms_unit_demand->dg_qty = $data['dg_qty'];
        }
        if(isset($data['afmsd_remarks'])) {
            $annual_demand_pvms_unit_demand->afmsd_remarks = $data['afmsd_remarks'];
        }
        if(isset($data['dg_remarks'])) {
            $annual_demand_pvms_unit_demand->dg_remarks = $data['dg_remarks'];
        }
        $annual_demand_pvms_unit_demand->save();
        AuditService::AuditLogEntry(AuditModel::AnnualDemandPvmsUnitDemand,OperationTypes::Update,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$annual_demand_pvms_unit_demand,$annual_demand_pvms_unit_demand->id);

        return $annual_demand_pvms_unit_demand;
    }
}
