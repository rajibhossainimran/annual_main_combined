<?php

namespace App\Services;

use App\Models\ApprovalLayer;
use App\Models\UserApprovalRole;

class ApprovalLayerService {

    public static function getDefaultLayer() {
        return [
            'all' => ApprovalLayer::where('is_repair', false)->where('is_default', true)->get(),
            'repair' => ApprovalLayer::where('is_repair', true)->where('is_default', true)->get()
        ];
    }

    public static function getOrganizationLayer($org_id) {
        return [
            'all' => ApprovalLayer::where('org_id', $org_id)->where('is_repair', false)->where('is_default', false)->get(),
            'repair' => ApprovalLayer::where('org_id', $org_id)->where('is_repair', true)->where('is_default', false)->get()
        ];
    }

    public static function setOrganizationDefaultLayer($org_id) {
        $approval_layer_exists = ApprovalLayer::where('org_id', $org_id)->exists();
        if(!$approval_layer_exists){
            $approval_layers = ApprovalLayer::where('is_default', true)->get();

            foreach($approval_layers as $approval_layer){
                $new = $approval_layer->replicate();
                $new->org_id = $org_id;
                $new->is_default = false;
                $new->save();
            }
        }
        
    }

    public static function setOrganizationLayer($org_id, $all_layers, $repair_layers){

        ApprovalLayer::where('org_id', $org_id)->delete();
        $count = 0;
        foreach($all_layers as $all_layer){
            $user_approval_role = UserApprovalRole::where('role_key', $all_layer)->first();
            $approval_layer = new ApprovalLayer();
            $approval_layer->name = $all_layer=='mo' ? 'mo' : $user_approval_role->role_name;
            $approval_layer->designation = $all_layer;
            $approval_layer->step = $count++;
            $approval_layer->org_id = $org_id;
            $approval_layer->is_repair = false;
            $approval_layer->save();
        }

        $count = 0;
        foreach($repair_layers as $repair_layer){
            $user_approval_role = UserApprovalRole::where('role_key', $repair_layer)->first();
            $approval_layer = new ApprovalLayer();
            $approval_layer->name = $all_layer=='mo' ? 'mo' : $user_approval_role->role_name;
            $approval_layer->designation = $repair_layer;
            $approval_layer->step = $count++;
            $approval_layer->org_id = $org_id;
            $approval_layer->is_repair = true;
            $approval_layer->save();
        }
    }

    public static function isMoExists($org_id){
        return ApprovalLayer::where('org_id', $org_id)->exists();
    }

}