<?php

namespace App\Services;

use App\Models\Supllier;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class SupllierService {
    public static function getSupllierById($id) {
        $supllier = Supllier::find($id);
        return $supllier;
    }
    
    public static function createOrUpdateSupllier($data) {
        $supllier = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $supllier = Supllier::find($data['id']);
            // $old_data = $supllier;
            $old_data = $supllier->getOriginal();
            $supllier->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Supplier';
        } else {
            $supllier = new Supllier();
            $supllier->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Supplier '.$data['name'].' created by '.auth()->user()->name;
        }

        $supllier->name = $data['name'];
        $supllier->code = $data['code'];
        $supllier->address = $data['address'];
        $supllier->contact_no = $data['contact_no'];
        $supllier->save();

        $new_data = $supllier;
        if(isset($old_data)) {
            $description = 'Supllier '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($supllier->getChanges() as $key => $value) {
                if($key != 'updated_at' && $key != 'updated_by' && $key != 'deleted_at' && $key != 'deleted_by') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                }
            }
        }

        AuditService::AuditLogEntry(AuditModel::Supplier,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$supllier->id);

        return $supllier;
    }
}