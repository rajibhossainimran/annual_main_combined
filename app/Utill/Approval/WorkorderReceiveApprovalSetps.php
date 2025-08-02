<?php

namespace App\Utill\Approval;

class WorkorderReceiveApprovalSetps{
    
    const approval_steps = [
        'stock-control-officer', 'oic', 'group-incharge'
    ];

    public static function nextStep($current_step) {
        $approval_step_name='';
        if($current_step==null){
            $approval_step_name = Self::approval_steps[0];
        } else {
            $current_step = array_search($current_step, Self::approval_steps);

            if($current_step == (count(Self::approval_steps) - 1)){
                $approval_step_name = 'approved';
            }else{
                $approval_step_name = Self::approval_steps[$current_step + 1];
            }
        }
        
        return $approval_step_name;
    }
    
}