<?php

namespace App\Services;

use App\Models\Csr;
use App\Models\CsrDemand;
use App\Models\VendorBidding;

class CsrService {
    public static function createCsr($tender_id, $pvms_id, $pvms_quantity) {
        $csr = new Csr();
        $csr->tender_id = $tender_id;
        $csr->pvms_id = $pvms_id;
        $csr->pvms_quantity = $pvms_quantity;
        $csr->created_by = auth()->user()->id;
        $csr->updated_by = auth()->user()->id;
        $csr->save();

        return $csr;
    }

    public static function createCsrDemand($csr_id,$notesheet_id,$notesheet_demand_pvms_id,$demand_id,$demand_pvms_id,$pvms_id,$qty) {
        $csr_demand = new CsrDemand();
        $csr_demand->csr_id = $csr_id;
        $csr_demand->notesheet_id = $notesheet_id;
        $csr_demand->notesheet_demand_pvms_id = $notesheet_demand_pvms_id;
        $csr_demand->demand_id = $demand_id;
        $csr_demand->demand_pvms_id = $demand_pvms_id;
        $csr_demand->pvms_id = $pvms_id;
        $csr_demand->qty = $qty;
        $csr_demand->save();
        return $csr_demand;
    }

    public static function createVendorBidding($csr_id,$offered_unit_price,$details) {
        $vendor_bidding = new VendorBidding();
        $vendor_bidding->csr_id = $csr_id;
        $vendor_bidding->vendor_id = auth()->user()->id;
        $vendor_bidding->details = $details;
        $vendor_bidding->brand_name = '';
        $vendor_bidding->pack_size = '';
        $vendor_bidding->mfg = '';
        $vendor_bidding->origin = '';
        $vendor_bidding->dar_no = '';
        $vendor_bidding->offered_unit_price = $offered_unit_price;
        $vendor_bidding->save();

        return $vendor_bidding;
    }
}