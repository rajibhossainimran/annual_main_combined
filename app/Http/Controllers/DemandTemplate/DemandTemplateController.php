<?php

namespace App\Http\Controllers\DemandTemplate;

use App\Http\Controllers\Controller;
use App\Models\DemandTemplate;
use App\Services\DemandService;
use App\Services\DemandTemplateService;
use App\Services\MediaService;
use Illuminate\Http\Request;

class DemandTemplateController extends Controller
{
    //
    public function index(Request $request)
    {
        //
        $demand_template = [];

        if(auth()->user() && isset(auth()->user()->sub_org_id)) {
            $demand_template = DemandTemplate::with('demandTemplatePVMS.PVMS','demandTemplatePVMS.PVMS.itemTypename','demandTemplatePVMS.PVMS.unitName','demandItemType','demandType','createdBy','dmdUnit')
                ->where('template_name', 'LIKE', '%'.$request->keyword.'%')
                ->where('sub_org_id',auth()->user()->sub_org_id)
                ->limit(5)->get();
        }

        return $demand_template;
    }

    public function store(Request $request)
    {
        $data = json_decode($request->data);
        $document_name = '';
        if($request->hasFile('document_file')) {
            $file = $request->file('document_file');
            $document_name = time().'_'.$file->getClientOriginalName();
            MediaService::uploadFile(
                $document_name,
                'demand_documents',
                $file
            );

        }
        $demand_template = DemandTemplateService::createDemandTemplate($data,$document_name);

        if($data->demadType==4){
            foreach ($data->repairPVMS as $demand_pvms) {
                DemandTemplateService::createDemandTemplatePvmsList($demand_pvms,$demand_template->id,true);
            }
        } else {
            foreach ($data->demandPVMS as $demand_pvms) {
                DemandTemplateService::createDemandTemplatePvmsList($demand_pvms,$demand_template->id);
            }
        }

        return $demand_template;
    }
}
