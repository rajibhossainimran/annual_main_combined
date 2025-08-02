<?php

namespace App\Services;

use App\Models\ItemDepartment;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class ItemDepartmentService {
    public static function getItemDepartmentById($id) {
        $item_department = ItemDepartment::find($id);
        return $item_department;
    }
    
    public static function createOrUpdateItemDepartment($data) {
        $item_department = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $item_department = ItemDepartment::find($data['id']);
            // $old_data = $item_department;
            $old_data = $item_department->getOriginal();
            $item_department->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Item Department';
        } else {
            $item_department = new ItemDepartment();
            $item_department->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Item Department '.$data['name'].' created by '.auth()->user()->name;
        }

        $item_department->name = $data['name'];
        $item_department->save();

        $new_data = $item_department;
        if(isset($old_data)) {
            $description = 'Item department '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($item_department->getChanges() as $key => $value) {
              if($key == 'name') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                    break;
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::ItemDepartment,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$item_department->id);

        return $item_department;
    }
}