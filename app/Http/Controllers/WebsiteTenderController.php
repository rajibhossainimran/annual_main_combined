<?php

namespace App\Http\Controllers;

use App\Models\WebsiteTender;
use Illuminate\Http\Request;

class WebsiteTenderController extends Controller
{
    public function store(Request $request) {

        if($request->api_key==env('API_KEY')){
            $filename =  'tender_'.$request->tender_no.'.'.$request->pdf_file->extension();

            $request->pdf_file->move(public_path('uploads'), $filename);
    
            $website_tender = new WebsiteTender();
            $website_tender->title = $request->title;
            $website_tender->details = $request->details;
            $website_tender->last_submission_date = $request->last_submission_date;
            $website_tender->tender_no = $request->tender_no;
            $website_tender->pdf_file = $filename;
            $website_tender->save();
        }else{
            return response(['message' => 'api key mismatched'], 401);
        }
        

    }
}
