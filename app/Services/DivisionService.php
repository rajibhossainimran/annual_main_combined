<?php

namespace App\Services;

use App\Models\Division;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class DivisionService {
    public static function getDivisionById($id) {
        $division = Division::find($id);
        return $division;
    }

    public static function createOrUpdateDivision($data) {
        $division = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $division = Division::find($data['id']);
            // $old_data = $division;
            $old_data = $division->getOriginal();
            $division->status = $data['status'];
            $division->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Division';
        } else {
            $division = new Division();
            $division->status = StatusTypes::ACTIVE;
            $division->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Division '.$data['name'].' created by '.auth()->user()->name;
        }

        $division->name = $data['name'];
        $division->code = $data['code'];
        $division->save();

        $new_data = $division;
        if(isset($old_data)) {
            $description = 'Division '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($division->getChanges() as $key => $value) {
              if($key == 'name') {
                $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
              } else if($key == 'status') {
                if($value == StatusTypes::ACTIVE) {
                        $description .= ' '.$key.' changes from Inactive to Active.';
                    } else if($value == StatusTypes::DEACTIVE) {
                        $description .= ' '.$key.' changes from Active to Inactive.';
                    }

                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::Division,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$division->id);

        return $division;
    }
}
