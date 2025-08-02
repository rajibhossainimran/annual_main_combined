<?php

namespace App\Services;

use App\Models\Patient;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class PatientsService {

    public static function createOrUpdatetUnit($data) {
        $patient = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $patient = Patient::find($data['id']);
            $patient->updated_by = auth()->user()->id;
            $patient->updated_at = now();
            $old_data = $patient->getOriginal();
            // $account_unit->status = $data['status'];
            // $account_unit->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            $description = 'patient '.$data['name'].' updated by '.auth()->user()->name;
        } else {
            $patient = new Patient();
            $patient->created_by = auth()->user()->id;
            $patient->created_at = now();
            // $account_unit->status = StatusTypes::ACTIVE;
            // $account_unit->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'patient '.$data['name'].' created by '.auth()->user()->name;
        }

        $patient->name = $data['name'];
        $patient->identification_no = $data['type'].'-'.$data['number'];
        $patient->relation = $data['relation'];
        $patient->unit_id = $data['unit_id'];
        $patient->save();

        $new_data = $patient;


        AuditService::AuditLogEntry(AuditModel::Patient,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$patient->id);

        return $patient;
    }
}
