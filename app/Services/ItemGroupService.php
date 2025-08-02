<?php

namespace App\Services;

use App\Models\ItemGroup;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;

class ItemGroupService {
    public static function getItemGroupById($id) {
        $item_group = ItemGroup::find($id);
        return $item_group;
    }
    
    public static function createOrUpdateItemGroup($data) {
        $item_group = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $item_group = ItemGroup::find($data['id']);
            // $old_data = $item_group;
            $old_data = $item_group->getOriginal();
            $item_group->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Item Group';
        } else {
            $item_group = new ItemGroup();
            $item_group->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Item Group '.$data['name'].' created by '.auth()->user()->name;
        }

        $item_group->name = $data['name'];
        $item_group->code = $data['code'];
        $item_group->save();

        $new_data = $item_group;
        if(isset($old_data)) {
            $description = 'Item group '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($item_group->getChanges() as $key => $value) {
              if($key == 'name' || $key == 'code') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::ItemGroup,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$item_group->id);

        return $item_group;
    }
}