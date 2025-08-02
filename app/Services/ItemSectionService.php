<?php

namespace App\Services;

use App\Models\ItemSections;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class ItemSectionService
{
    public static function StoreUpdate($data){
        $section = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $section = ItemSections::find($data['id']);
            // $old_data = $section;
            $old_data = $section->getOriginal();
            $section->status = $data['status'];
            $section->code = $data['code'];
            $section->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Item Section';
        } else {
            $section = new ItemSections();
            $section->status = StatusTypes::ACTIVE;
            $section->created_by = auth()->user()->id;
            $section->code = $data['code'];
            $operation = OperationTypes::Create;
            $description = 'Item Section '.$data['name'].' created by '.auth()->user()->name;
        }
        $section->name = $data['name'];
        $section->save();

        $new_data = $section;
        if(isset($old_data)) {
            $description = 'Item section '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($section->getChanges() as $key => $value) {
              if($key == 'name' || $key == 'code') {
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
        AuditService::AuditLogEntry(AuditModel::ItemSection,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$section->id);

        return $section;
    }

    public static function getItemSectionById($id) {
        $service = ItemSections::find($id);
        return $service;
    }
}
