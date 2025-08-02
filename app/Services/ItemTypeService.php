<?php

namespace App\Services;

use App\Models\ItemType;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class ItemTypeService {
    public static function getItemTypeById($id) {
        $item_type = ItemType::find($id);
        return $item_type;
    }
    
    public static function createOrUpdateItemType($data) {
        $item_type = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $item_type = ItemType::find($data['id']);
            // $old_data = $item_type;
            $old_data = $item_type->getOriginal();
            $item_type->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Item Type';
        } else {
            $item_type = new ItemType();
            $item_type->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Item Type'.$data['name'].' created by '.auth()->user()->name;
        }

        $item_type->name = $data['name'];
        $item_type->anx = $data['anx'];
        $item_type->save();

        $new_data = $item_type;
        if(isset($old_data)) {
            $description = 'Item type '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($item_type->getChanges() as $key => $value) {
              if($key == 'name' || $key == 'anx') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::ItemType,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$item_type->id);

        return $item_type;
    }
}