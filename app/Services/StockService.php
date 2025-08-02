<?php

namespace App\Services;

use App\Models\PvmsStore;
use App\Models\IssueOrderDeliveryApproval;

class StockService
{
    public static function stockEntry($sub_org_id, $from_sub_org_id, $issue_voucher_id, $pvms_id, $batch_pvms_id, $stock_in, $stock_out, $is_received, $branch_id = null, $on_loan_id = null, $is_loan = false)
    {
        $pvms_store = new PvmsStore();
        $pvms_store->sub_org_id = $sub_org_id;
        $pvms_store->from_sub_org_id = $from_sub_org_id;
        $pvms_store->issue_voucher_id = $issue_voucher_id;
        $pvms_store->pvms_id = $pvms_id;
        $pvms_store->batch_pvms_id = $batch_pvms_id;
        $pvms_store->stock_in = $stock_in;
        $pvms_store->stock_out = $stock_out;
        $pvms_store->is_received = $is_received;
        $pvms_store->is_on_loan = $is_loan;

        if (isset($branch_id)) {
            $pvms_store->branch_id = $branch_id;
        }

        if (isset($on_loan_id)) {
            $pvms_store->on_loan_item_id = $on_loan_id;
        }

        $pvms_store->save();

        return $pvms_store;
    }
}
