<?php

namespace App\Services;

use App\Models\Service;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class ServiceSettingService
{
    public static function StoreUpdate($data){
        $service = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $service = Service::find($data['id']);
            // $old_data = $service;
            $old_data = $service->getOriginal();
            $service->status = $data['status'];
            $service->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
            // $description = 'Update Service';
        } else {
            $service = new Service();
            $service->status = StatusTypes::ACTIVE;
            $service->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Service '.$data['name'].' created by '.auth()->user()->name;
        }

        $service->name = $data['name'];
        $service->save();

        $new_data = $service;
        if(isset($old_data)) {
            $description = 'Service '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($service->getChanges() as $key => $value) {
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
        AuditService::AuditLogEntry(AuditModel::Service,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$service->id);

        return $service;
    }

    public static function getServiceById($id) {
        $service = Service::find($id);
        return $service;
    }
}
