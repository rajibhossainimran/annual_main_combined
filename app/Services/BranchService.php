<?php

namespace App\Services;

use App\Models\Branch;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class BranchService {
    public static function getBranchById($id) {
        $branch = Branch::find($id);
        return $branch;
    }
    
    public static function createOrUpdateBranch($data) {
        $branch = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $branch = Branch::find($data['id']);
            $old_data = $branch;
            // $old_data = $branch->getOriginal();
            $branch->updated_by = auth()->user()->id;
            $branch->status = $data['status'];
            $operation = OperationTypes::Update;
            $description = 'Store '.$data->name.' updated by '.auth()->user()->name;
        } else {
            $branch = new Branch();
            $branch->status = StatusTypes::ACTIVE;
            $branch->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Store '.$data['name'].' created by '.auth()->user()->name;
        }

        $branch->sub_org_id = $data['sub_org_id'];
        $branch->name = $data['name'];
        $branch->code = $data['code'];
        $branch->save();

        $new_data = $branch;
        AuditService::AuditLogEntry(AuditModel::Store,$operation,$description,$old_data,$new_data,$branch->id);

        return $branch;
    }
}