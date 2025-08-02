<?php

namespace App\Services;

use App\Models\AuditTrail;

class AuditService {
    public static function AuditLogEntry($model,$operation,$description,$old_data,$new_data,$content_id) {
        $audit = new AuditTrail();
        $audit->ip = \Request::getClientIp();
        $audit->model = $model;
        $audit->operation = $operation;
        $audit->description = $description;
        $audit->perform_by = auth()->user()->id;
        $audit->model_id = $content_id;
        $audit->old_data = $old_data;
        $audit->new_data = $new_data;
        $audit->save();
    }
}