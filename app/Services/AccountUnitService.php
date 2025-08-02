<?php

namespace App\Services;

use App\Models\AccountUnit;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class AccountUnitService {
    public static function getAccountUnitById($id) {
        $account_unit = AccountUnit::find($id);
        return $account_unit;
    }
    
    public static function createOrUpdateAccountUnit($data) {
        $account_unit = '';
        $new_data = null;
        $old_data = null;
        $description = '';
        $operation = '';

        if(isset($data["id"])) {
            $account_unit = AccountUnit::find($data['id']);
            $old_data = $account_unit->getOriginal();
            $account_unit->status = $data['status'];
            $account_unit->updated_by = auth()->user()->id;
            $operation = OperationTypes::Update;
        } else {
            $account_unit = new AccountUnit();
            $account_unit->status = StatusTypes::ACTIVE;
            $account_unit->created_by = auth()->user()->id;
            $operation = OperationTypes::Create;
            $description = 'Account Unit '.$data['name'].' created by '.auth()->user()->name;
        }
        
        $account_unit->name = $data['name'];
        $account_unit->save();

        $new_data = $account_unit;
        if(isset($old_data)) {
            $description = 'Account unit '.$old_data['name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($account_unit->getChanges() as $key => $value) {
                if($key == 'status') {
                    if($value == StatusTypes::ACTIVE) {
                        $description .= ' '.$key.' changes from Inactive to Active.';
                    } else if($value == StatusTypes::DEACTIVE) {
                        $description .= ' '.$key.' changes from Active to Inactive.';
                    }
                    
                } else if($key != 'updated_at' && $key != 'updated_by' && $key != 'deleted_at' && $key != 'deleted_by') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                }
            }
        }

        AuditService::AuditLogEntry(AuditModel::AccountUnit,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$account_unit->id);

        return $account_unit;
    }
}