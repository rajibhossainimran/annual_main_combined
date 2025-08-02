<?php

namespace App\Services;

use App\Models\DemandTemplate;
use App\Models\DemandTemplatePvmsList;

class DemandTemplateService {

    public static function createDemandTemplate($data,$document_name) {
        $demand_template = new DemandTemplate();
        $demand_template->sub_org_id = auth()->user()->sub_org_id;
        $demand_template->demand_type_id = $data->demadType ?? null;
        $demand_template->demand_item_type_id = $data->demand_item_type_id ?? null;
        $demand_template->template_name = $data->template_name;
        $demand_template->document_file = $document_name ?? null;
        $demand_template->description = $data->description ?? null;
        $demand_template->description1 = $data->description1 ?? null;
        $demand_template->is_dental_type = $data->isDentalType ?? null;
        $demand_template->created_by = auth()->user()->id;
        $demand_template->save();

        return $demand_template;
    }

    public static function createDemandTemplatePvmsList($data,$demand_template_id,$is_repair = false) {
        $demand_template_pvms_list = new DemandTemplatePvmsList();

        $demand_template_pvms_list->demand_template_id = $demand_template_id;
        $demand_template_pvms_list->p_v_m_s_id = $data->id;
        $demand_template_pvms_list->patient_name = $data->patient_name ?? null;
        $demand_template_pvms_list->patient_id = $data->patient_id ?? null;
        $demand_template_pvms_list->disease = $data->disease ?? null;
        $demand_template_pvms_list->qty = $data->qty ?? null;
        $demand_template_pvms_list->remarks = $data->remarks ?? null;

        $demand_template_pvms_list->ward = $data->ward ?? null;
        $demand_template_pvms_list->authorized_machine = $data->authorized_machine ?? null;
        $demand_template_pvms_list->existing_machine = $data->existing_machine ?? null;
        $demand_template_pvms_list->running_machine = $data->running_machine ?? null;
        $demand_template_pvms_list->disabled_machine = $data->disabled_machine ?? null;

        if($is_repair) {
            $demand_template_pvms_list->issue_date = $data->issue_date ?? null;
            $demand_template_pvms_list->installation_date = $data->installation_date ?? null;
            $demand_template_pvms_list->warranty_date = $data->warranty_date ?? null;
            $demand_template_pvms_list->supplier = $data->supplier ?? null;
        }
        $demand_template_pvms_list->save();

        return $demand_template_pvms_list;
    }
}
