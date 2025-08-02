<?php

namespace App\Services;

use App\Models\Organization;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class OrganizationService {
    public static function getOrganizationById($id) {
        $organization = Organization::find($id);
        return $organization;
    }
    
    public static function createOrUpdateOrganization($data) {
        $organization = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $organization = Organization::find($data['id']);
            $old_data = $organization;
            // $old_data = $organization->getOriginal();
            $organization->updated_by = auth()->user()->id;
            $organization->status = $data['status'];
            $operation = OperationTypes::Update;
            $description = 'Governing Body '.$data->name.' updated by '.auth()->user()->name;
        } else {
            $organization = new Organization();
            $organization->status = StatusTypes::ACTIVE;
            $organization->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Governing Body '.$data['name'].' created by '.auth()->user()->name;
        }

        $organization->name = $data['name'];
        $organization->code = $data['code'];
        $organization->save();

        $new_data = $organization;
        AuditService::AuditLogEntry(AuditModel::GoverningBody,$operation,$description,$old_data,$new_data,$organization->id);

        return $organization;
    }
}