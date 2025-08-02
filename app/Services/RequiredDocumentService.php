<?php

namespace App\Services;

use App\Models\RequiredDocument;
use App\Models\Service;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class RequiredDocumentService {

    public static function StoreUpdate($data)
    {
        $req = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $req = RequiredDocument::find($data['id']);
            $old_data = $req->getOriginal();
//            $req->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
        } else {
            $req = new RequiredDocument();
//            $req->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Tender Documents '.$data['name'].' created by '.auth()->user()->name;
        }

        $req->name = $data['name'];
        $req->file_type = $data['file_type'];
        $req->save();

        $new_data = $req;
        if(isset($old_data)) {
            $description = 'Tender Documents '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($req->getChanges() as $key => $value) {
                if($key == 'name') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                } else if($key == 'file_type') {
                    $description .= ' '.$key.' changes Type '.$old_data[$key].' to '.$value.'.';
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::Service,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$req->id);

        return $req;
    }


}
