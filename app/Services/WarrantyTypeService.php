<?php

namespace App\Services;

use App\Models\WarrantyType;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class WarrantyTypeService {
    public static function getWarrantyTypeById($id) {
        $warranty_type = WarrantyType::find($id);
        return $warranty_type;
    }
    
    public static function createOrUpdateWarrantyType($data) {
        $warranty_type = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $warranty_type = WarrantyType::find($data['id']);
            // $old_data = $warranty_type;
            $old_data = $warranty_type->getOriginal();
            $warranty_type->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
        } else {
            $warranty_type = new WarrantyType();
            $warranty_type->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Warranty Type'.$data['name'].' created by '.auth()->user()->name;
        }

        $warranty_type->name = $data['name'];
        $warranty_type->save();

        $new_data = $warranty_type;

        if(isset($old_data)) {
            $description = 'Warranty type '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($warranty_type->getChanges() as $key => $value) {
              if($key == 'name') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                    break;
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::WarrantyType,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$warranty_type->id);

        return $warranty_type;
    }
}