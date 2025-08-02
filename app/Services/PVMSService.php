<?php

namespace App\Services;

use App\Models\AccountUnit;
use App\Models\ItemType;
use App\Models\PVMS;
use App\Utill\Const\AuditModel;
use App\Utill\Const\OperationTypes;
use App\Utill\Const\StatusTypes;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PVMSService
{
    public static function StoreOrUpdate($data){

        $old_data = null;
        if(isset($data["id"])) {
            $pvms = PVMS::find($data['id']);
            $old_data = $pvms->getOriginal();
            $operation = OperationTypes::Update;
            $pvms->updated_by = auth()->user()->id;
            $pvms->pvms_name = $data['pvms_name'];
            $pvms->pvms_id = $data['pvms_name'];
            $pvms->pvms_old_name = $data['pvms_old_name'];
            $pvms->nomenclature = $data['nomenclature'];
            $pvms->account_units_id = $data['account_units_id'];
            $pvms->specifications_id = $data['specifications_id'];
            $pvms->item_groups_id = $data['item_groups_id'];
            $pvms->item_sections_id = $data['item_sections_id'];
            $pvms->item_types_id = $data['item_types_id'];
            $pvms->control_types_id = $data['control_type_id'];
            $pvms->item_departments_id = $data['item_departments_id'];
            $pvms->page_no = $data['page_no'];
            $pvms->remarks = $data['remarks'];
            $pvms->item_source = $data['item_source'];
            $pvms->deleted_at = null;
            $pvms->niv = null;
//            dd($pvms);
//            $pvms->save();
        } else {
            $pvms = new PVMS();
            $operation = OperationTypes::Create;
            $pvms->created_by = auth()->user()->id;
            $pvms->pvms_name = $data['pvms_name'];
            $pvms->pvms_id = $data['pvms_name'];
            $pvms->pvms_old_name = $data['pvms_old_name'];
            $pvms->nomenclature = $data['nomenclature'];
            $pvms->account_units_id = $data['account_units_id'];
            $pvms->specifications_id = $data['specifications_id'];
            $pvms->item_groups_id = $data['item_groups_id'];
            $pvms->item_sections_id = $data['item_sections_id'];
            $pvms->item_types_id = $data['item_types_id'];
            $pvms->control_types_id = $data['control_type_id'];
            $pvms->item_departments_id = $data['item_departments_id'];
            $pvms->page_no = $data['page_no'];
            $pvms->remarks = $data['remarks'];
            $pvms->item_source = $data['item_source'];
            if(isset($data['niv']) && !empty($data['niv'])){
                $pvms->niv = $data['niv'];
            }

            $description = 'PVMS '.$data['pvms_name'].' created by '.auth()->user()->name;
        }
        $pvms->save();
        $new_data = $pvms;
        if(isset($old_data)) {
            $description = 'PVMS '.$old_data['pvms_name']. ' has been updated by '.auth()->user()->name.'.';
            foreach ($pvms->getChanges() as $key => $value) {
                if($key == 'name' || $key == 'code') {
                    $description .= ' '.$key.' changes from '.$old_data[$key].' to '.$value.'.';
                }
            }
        }
        AuditService::AuditLogEntry(AuditModel::PVMS,$operation,$description,isset($old_data) ? json_encode($old_data,JSON_FORCE_OBJECT) : $old_data,$new_data,$pvms->id);

        return $pvms;
    }

    public static function getItemSectionById($id) {
        $service = PVMS::find($id);
        return $service;
    }

    public function Update($data)
    {

    }

    public static function ExcelUpload($id, $pvms_id, $nomenclature,$accounts,$type){

        $pvms = new PVMS();
            $pvms->created_by = auth()->user()->id;
            $pvms->pvms_name = $id;
            $pvms->pvms_id = $pvms_id;
            $pvms->nomenclature = $nomenclature;
            $pvms->account_units_id = $accounts;
            $pvms->item_types_id = $type;
            $pvms->save();

    }

    public static function pvmsUnitWiseStock($pvms_list,$subOrgId) {
        return PVMS::withCount(['batchList as stock_qty' => function ($query) use($subOrgId) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id='.$subOrgId.') AS SIGNED))'));
            },'batchList as afmsd_stock_qty' => function ($query) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_in) - SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=2) AS SIGNED))'));
            },
            'batchList as last_12_month_unit_consume_qty' => function ($query) use($subOrgId) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->whereBetween('created_at', [Carbon::now()->subMonths(12), Carbon::now()])
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id='.$subOrgId.') AS SIGNED))'));
            },
            'batchList as last_12_month_afmsd_consume_qty' => function ($query) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->whereBetween('created_at', [Carbon::now()->subMonths(12), Carbon::now()])
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=2) AS SIGNED))'));
            },
            'batchList as last_3_month_unit_consume_qty' => function ($query) use($subOrgId) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id='.$subOrgId.') AS SIGNED))'));
            },
            'batchList as last_3_month_afmsd_consume_qty' => function ($query) {
                    $query->where('expire_date', '>', Carbon::now())
                    ->whereBetween('created_at', [Carbon::now()->subMonths(3), Carbon::now()])
                    ->select(DB::raw('SUM(CAST((SELECT SUM(stock_out) FROM pvms_store WHERE pvms_store.batch_pvms_id = batch_pvms.id and is_received=1 and pvms_store.sub_org_id=2) AS SIGNED))'));
            }])->whereIn('id',$pvms_list)->get();
    }
}
