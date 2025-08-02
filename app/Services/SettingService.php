<?php

namespace App\Services;

use App\Models\FinancialYear;
use App\Models\Setting;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;

class SettingService
{
    public static function CreateOrUpdate($data)
    {
        $old_data = null;
        $setting = Setting::where('key',$data['key'])->first();
        if(isset($setting) && !empty($setting)){
            $old_data = $setting;
            $setting->updated_by = auth()->user()->id;
            $setting->value = $data['value'];
            $setting->save();
            $description = 'Config '.$old_data['key']. ' has been updated by '.auth()->user()->name.'.';
            AuditService::AuditLogEntry(AuditModel::Config,OperationTypes::Update,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$setting,auth()->user()->id);
        }else{
            $setting = new Setting();
            $setting->created_by = auth()->user()->id;
            $setting->key = $data['key'];
            $setting->value = $data['value'];
            $setting->save();
            $description = 'Config Created by '.auth()->user()->name.'.';
            AuditService::AuditLogEntry(AuditModel::Config,OperationTypes::Create,$description,null,$setting,auth()->user()->id);
        }
        return $setting;
    }
}
