<?php

namespace App\Services;

use App\Models\SubOrganization;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class SubOrganizationService {
    public static function getSubOrganizationById($id) {
        $sub_organization = SubOrganization::find($id);
        return $sub_organization;
    }
    
    public static function createOrUpdateSubOrganization($data) {
        $sub_organization = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $sub_organization = SubOrganization::find($data['id']);
            $old_data = $sub_organization;
            // $old_data = $sub_organization->getOriginal();
            $sub_organization->updated_by = auth()->user()->id;
            $sub_organization->status = $data['status'];
            $operation = OperationTypes::Update;
            $description = 'Organization '.$data['name'].' updated by '.auth()->user()->name;
        } else {
            $sub_organization = new SubOrganization();
            $sub_organization->status = StatusTypes::ACTIVE;
            $sub_organization->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Organization '.$data['name'].' created by '.auth()->user()->name;
        }

        $sub_organization->org_id = $data['org_id'];
        $sub_organization->name = $data['name'];
        $sub_organization->code = $data['code'];
        $sub_organization->serial = $data['serial'];
        $sub_organization->division_id = $data['division'];
        $sub_organization->service_id = $data['service'];
        $sub_organization->type = $data['type'];
        $sub_organization->save();

        $new_data = $sub_organization;
        AuditService::AuditLogEntry(AuditModel::Organization,$operation,$description,$old_data,$new_data,$sub_organization->id);

        return $sub_organization;
    }
}