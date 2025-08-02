<?php

namespace App\Services;

use App\Models\DemandUnit;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class DemandUnitService {

    public static function getAccountUnitById($id) {
        $account_unit = DemandUnit::find($id);
        return $account_unit;
    }

    public static function createOrUpdatetUnit($data) {
        $account_unit = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $account_unit = DemandUnit::find($data['id']);
            $old_data = $account_unit->getOriginal();
            // $account_unit->status = $data['status'];
            // $account_unit->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            $description = 'Demand Unit '.$data['name'].' updated by '.auth()->user()->name;
        } else {
            $account_unit = new DemandUnit();
            // $account_unit->status = StatusTypes::ACTIVE;
            // $account_unit->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Demand Unit '.$data['name'].' created by '.auth()->user()->name;
        }

        $account_unit->name = $data['name'];
        $account_unit->save();

        $new_data = $account_unit;


        AuditService::AuditLogEntry(AuditModel::DemandUnit,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$account_unit->id);

        return $account_unit;
    }
}
