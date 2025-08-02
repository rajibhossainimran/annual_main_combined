<?php

namespace App\Services;

use App\Models\Specification;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class SpecificationService
{

    public static function StoreUpdate($data){
        $specification = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $specification = Specification::find($data['id']);
            // $old_data = $specification;
            $old_data = $specification->getOriginal();
            $specification->status = $data['status'];
            $specification->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Specification';
        } else {
            $specification = new Specification();
            $specification->status = StatusTypes::ACTIVE;
            $specification->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Specification '.$data['name'].' created by '.auth()->user()->name;
        }

        $specification->name = $data['name'];
        $specification->save();

        $new_data = $specification;
        if(isset($old_data)) {
            $description = 'Specification '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($specification->getChanges() as $key => $value) {
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
        AuditService::AuditLogEntry(AuditModel::Specification,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$specification->id);

        return $specification;
    }

    public static function getSpecificationById($id) {
        $specification = Specification::find($id);
        return $specification;
    }

    public static function update($data){

    }

}
