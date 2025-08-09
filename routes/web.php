<?php

use App\Http\Controllers\AfmsdIssueApprovalController;
use App\Http\Controllers\IssueOrderDirectController;
use App\Http\Controllers\ReceivePvmsBatchStockController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HODController;
use App\Http\Controllers\PDFController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\WingController;
use App\Http\Controllers\DemandController;
use App\Http\Controllers\NoticeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Csr\CsrController;
use App\Http\Controllers\DiseaseController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\TrackingContoller;
use App\Utill\Approval\DemandApprovalSetps;
use App\Http\Controllers\PatientsController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WorkorderController;
use App\Http\Controllers\DemandUnitController;
use App\Http\Controllers\IssueOrderController;
use App\Http\Controllers\Audit\AuditController;
use App\Http\Controllers\User\VendorController;
use App\Http\Controllers\AnnualDemandController;
use App\Http\Controllers\CMHDepartmentController;
use App\Http\Controllers\Tender\TenderController;
use App\Http\Controllers\QuerterlyDemandControlle;
use App\Http\Controllers\Patient\PatientController;
use App\Http\Controllers\QuerterlyDemandController;
use App\Http\Controllers\RateRunningPvmsController;
use App\Http\Controllers\RequiredDocumentController;
use App\Http\Controllers\SslCommerzPaymentController;
use App\Http\Controllers\AnnualDemandReportController;
use App\Http\Controllers\Settings\PVMS\PVMSController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\AuthorizedEquipmentController;
use App\Http\Controllers\Notesheet\NotesheetController;
use App\Http\Controllers\CMHDepartmentCategoryController;
use App\Http\Controllers\Settings\Item\ItemTypeController;
use App\Http\Controllers\Settings\Item\ItemGroupController;
use App\Http\Controllers\Settings\Service\ServiceController;
use App\Http\Controllers\User\Organization\BranchController;
use App\Http\Controllers\Settings\Division\DivisionController;
use App\Http\Controllers\Settings\Account\AccountUnitController;
use App\Http\Controllers\Settings\Item\ItemDepartmentController;
use App\Http\Controllers\DemandTemplate\DemandTemplateController;
use App\Http\Controllers\Settings\Warranty\WarrantyTypeController;
use App\Http\Controllers\User\Organization\OrganizationController;
use App\Http\Controllers\Settings\Financial\FinancialYearController;
use App\Http\Controllers\Settings\ItemSection\ItemSectionController;
use App\Http\Controllers\User\Organization\SubOrganizationController;
use App\Http\Controllers\Settings\Specification\SpecificationController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect('login');
});

Route::get('/change-password/expire', function () {
    return view('auth.changePassword');
});
Route::post('/update-password/user/expire', [RegisteredUserController::class, 'UpdatePasswordExpired']);

Route::middleware(['auth', 'isExpired'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dgms/activity', [DashboardController::class, 'dgmsActivity'])->name('dgms.activity');
    Route::get('/remarks-template', [DemandController::class, 'searchUserRemarks'])->name('remarks_template.get');
    Route::get('/annual-demand', [AnnualDemandController::class, 'index'])->name('annual_demand.index');
    Route::get('/annual-demand/unit-estimation', [AnnualDemandController::class, 'unit_extimation'])->name('annual_demand.unit');
    Route::post('/annual-demand/unit-estimation', [AnnualDemandController::class, 'unit_extimation_create'])->name('annual_demand.unit');
    Route::post('/annual-demand/unit-approve/{id}', [AnnualDemandController::class, 'annual_demand_unit_approve'])->name('annual_demand.unit_approve');
    Route::get('/annual-demand-unit/{id}', [AnnualDemandController::class, 'annual_demand_unit'])->name('annual_demand_unit.list');
    Route::get('/annual-demand/create', [AnnualDemandController::class, 'create'])->name('annual_demand.create');
    Route::get('/annual_demand/download/excel/{id}', [AnnualDemandController::class, 'export']);
    Route::get('/annual_demand_list/download/excel/{id}', [AnnualDemandController::class, 'export_list']);
    Route::get('/dashboard-demand-notesheet-csr', [DashboardController::class, 'cdn_count'])->name('cdn_count');
    Route::get('/audit', [AuditController::class, 'index'])->name('audit');
    Route::get('/audit-log', [AuditController::class, 'audit_log'])->name('audit_log');
    Route::get('/demand_ready_for_noteshet', [NotesheetController::class, 'demand_ready_for_noteshet'])->name('demand_ready_for_noteshet');
    Route::get('/demand_ready_for_noteshet_re_tender', [NotesheetController::class, 'demand_ready_for_noteshet_re_tender'])->name('demand_ready_for_noteshet_re_tender');
    Route::get('/get_all_notesheet', [NotesheetController::class, 'get_all_notesheet'])->name('get_all_notesheet');
    //    role & permission
    Route::get('/all/permission', [RoleController::class, 'allPermission'])->name('all.permission');
    Route::get('/add/permission', [RoleController::class, 'addPermission'])->name('add.permission');
    Route::post('/store/permission', [RoleController::class, 'StorePermission'])->name('store.permission');
    Route::get('/edit/permission/{id}', [RoleController::class, 'EditPermission'])->name('edit.permission');
    Route::post('/update/permission', [RoleController::class, 'UpdatePermission'])->name('update.permission');
    Route::post('/delete/permission/{id}', [RoleController::class, 'destroy'])->name('delete.permission');
    Route::get('/user-approval-roles', [RoleController::class, 'userApprovalRoles'])->name('all.userApprovalRoles');
    Route::get('/get-loged-user-approval-role', [RoleController::class, 'logedUserApprovalRole'])->name('all.logedUserApprovalRole');

    Route::get('/create/permission', [RoleController::class, 'createPermission'])->name('create.permission');
    Route::post('/unit-wise-item-issue', [ReportController::class, 'unitWiseItemIssueApi']);

    //    users
    Route::get('/all/user', [RegisteredUserController::class, 'index'])->name('all.user');
    Route::get('/all/vendor', [VendorController::class, 'index'])->name('all.vendor');
    Route::get('/all/vendor-json', [VendorController::class, 'indexJson'])->name('all.vendor.json');
    Route::get('/add/user', [RegisteredUserController::class, 'create'])->name('add.user');
    Route::get('/add/vendor', [VendorController::class, 'create'])->name('add.vendor');
    Route::get('/edit/user/{id}', [RegisteredUserController::class, 'edit'])->name('edit.user');
    Route::post('/store/user', [RegisteredUserController::class, 'store'])->name('store.user');
    Route::post('/store/vendor', [VendorController::class, 'store'])->name('store.vendor');
    Route::post('/update/user/{id}', [RegisteredUserController::class, 'update'])->name('update.user');
    Route::post('/delete/user/{id}', [RegisteredUserController::class, 'delete'])->name('delete.user');
    Route::get('/change-password/user', [RegisteredUserController::class, 'ChangePassword'])->name('change.password.user');
    Route::post('/update-password/user', [RegisteredUserController::class, 'UpdatePassword'])->name('update.password.user');
    Route::get('/change/password/user/{id}', [RegisteredUserController::class, 'UserPasswordChange'])->name('password.user');
    Route::post('/update-password/user/all', [RegisteredUserController::class, 'UpdatePasswordAll'])->name('update.password.user.all');
    Route::get('/edit/vendor/{id}', [VendorController::class, 'edit'])->name('edit.vendor');
    Route::post('/delete/vendor/{id}', [VendorController::class, 'delete'])->name('delete.vendor');
    Route::post('/update/vendor/{id}', [VendorController::class, 'update'])->name('update.vendor');
    Route::get('/change/user/digital-sign', [RegisteredUserController::class, 'digitalSign'])->name('change.sign.user');
    Route::post('/update/user/digital/sign', [RegisteredUserController::class, 'digitalSignUpdate'])->name('update.sign.user');

    //    menu
    Route::get('/add/menu', [MenuController::class, 'addMenu'])->name('add.menu');
    Route::post('/store/menu', [MenuController::class, 'storeMenu'])->name('store.menu');
    Route::post('/store/submenu', [MenuController::class, 'storeSubmenu'])->name('store.submenu');
    Route::get('/menu/get-parent', [MenuController::class, 'getParentMenu'])->name('menu.parent');
    //       Organization
    Route::get('/organization', [OrganizationController::class, 'index'])->name('all.organization');
    Route::get('/add/organization', [OrganizationController::class, 'create'])->name('add.organization');
    Route::post('/store/organization', [OrganizationController::class, 'store'])->name('store.organization');
    Route::get('/edit/organization/{id}', [OrganizationController::class, 'edit'])->name('edit.organization');
    Route::post('/update/organization/{id}', [OrganizationController::class, 'update'])->name('update.organization');
    Route::post('/delete/organization/{id}', [OrganizationController::class, 'destroy'])->name('delete.organization');
    //      Sub Organization
    Route::get('/sub-organization', [SubOrganizationController::class, 'index'])->name('all.sub.organization');
    Route::get('/add/sub-organization', [SubOrganizationController::class, 'create'])->name('add.sub.organization');
    Route::post('/store/sub-organization', [SubOrganizationController::class, 'store'])->name('store.sub.organization');
    Route::get('/edit/sub-organization/{id}', [SubOrganizationController::class, 'edit'])->name('edit.sub.organization');
    Route::post('/update/sub-organization/{id}', [SubOrganizationController::class, 'update'])->name('update.sub.organization');
    Route::post('/delete/sub-organization/{id}', [SubOrganizationController::class, 'destroy'])->name('delete.sub.organization');
    Route::get('/unit-list-api', [SubOrganizationController::class, 'getSubOrganizationsList']);
    Route::get('/department-list-api', [ItemDepartmentController::class, 'getDepartmentListApi']);
    Route::get('/item-type-list-api', [ItemTypeController::class, 'getItemTypeListApi']);
    Route::get('/branch-list-api', [BranchController::class, 'getBranchesList']);
    Route::get('/pvms-with-stock-api', [ReportController::class, 'getPvmsWithAvilableStockList']);
    Route::post('/pvms-unit-stock-out', [ReportController::class, 'unitStockOut']);
    //      Branch
    Route::get('/branch', [BranchController::class, 'index'])->name('all.branch');
    Route::get('/add/branch', [BranchController::class, 'create'])->name('add.branch');
    Route::post('/store/branch', [BranchController::class, 'store'])->name('store.branch');
    Route::get('/edit/branch/{id}', [BranchController::class, 'edit'])->name('edit.branch');
    Route::post('/update/branch/{id}', [BranchController::class, 'update'])->name('update.branch');
    Route::post('/delete/branch/{id}', [BranchController::class, 'destroy'])->name('delete.branch');

    //demand
    Route::get('/patient-units', [PatientController::class, 'patientUnits']);
    Route::get('/demand-template', [DemandTemplateController::class, 'index']);
    Route::post('/demand-template', [DemandTemplateController::class, 'store']);

    Route::post('/return-demand-to-cmd', [DemandController::class, 'returnToCmd']);
    Route::post('/demand/mark-seen', [DemandController::class, 'markAsSeen'])->name('demand.markAsSeen');




    Route::get('/demand-approval-steps', [DemandController::class, 'demandStepsApi']);
    Route::get('/demand-types', [DemandController::class, 'demandTypes']);
    Route::get('/suggested-demand-no-prefix.js', [DemandController::class, 'suggestedSemandNoPrefixJs']);
    Route::get('/demand-edit.js', [DemandController::class, 'demandEditJs']);
    Route::post('/remove-demand-pvms/{id}', [DemandController::class, 'deleteDemandPVMS']);
    Route::get('/demand/api/{id}', [DemandController::class, 'showApi']);
    Route::get('/annual-demand/api', [AnnualDemandController::class, 'showApi']);
    Route::get('/annual-unit-demand/api', [AnnualDemandController::class, 'showUnitApi']);
    Route::post('/annual-demand/approve', [AnnualDemandController::class, 'approveList']);
    Route::post('/annual-demand-unit-estimation/approve', [AnnualDemandController::class, 'approveUnit']);
    Route::post('/annual-demand-unit-estimation/approve-by-dept', [AnnualDemandController::class, 'approveByDept']);
    Route::delete('/annual-demand-remove-pvms/{id}', [AnnualDemandController::class, 'removeAnuualDemandPvms']);
    Route::get('/tender/api/{id}', [TenderController::class, 'showApi']);
    Route::post('/demand-approve', [DemandController::class, 'approve']);
    Route::post('/demand-approve-changeuuid', [DemandController::class, 'approveChangeUuid']);
    Route::post('/demand-send-for-reapprove', [DemandController::class, 'demandSendForReapprove']);
    Route::post('/notesheet-approve', [NotesheetController::class, 'approve']);
    Route::get('/notesheet-approval-steps', [NotesheetController::class, 'notesheetApprovalStepsApi']);
    Route::get('/csr-approval-steps', [CsrController::class, 'csrApprovalStepsApi']);
    Route::get('/get-hod-users', [CsrController::class, 'get_hod_users']);
    Route::get('/wings-json', [WingController::class, 'indexJson']);
    Route::get('/wings-by-org/{org_id}', [WingController::class, 'indexByOrgJson']);
    Route::get('/wings/users/{wing_id}', [WingController::class, 'wingUsers']);
    Route::post('/demand-update-document/{demand_id}', [DemandController::class, 'updateDocumentFile']);
    Route::resource('/demand', DemandController::class);
    Route::get('/pvms-list-api', [PVMSController::class, 'pvms_with_stock_of_unit']);
    Route::resource('/csr', CsrController::class);
    Route::get('/csr-cover-letter', [CsrController::class, 'csr_cover_letter']);
    Route::get('/all_csr_completed_tender_list', [CsrController::class, 'get_all_tender_of_completed_csr']);
    Route::get('/get-tender-for-csr', [CsrController::class, 'get_tender_ready_for_csr']);
    Route::post('/csr-approval', [CsrController::class, 'approve']);
    Route::post('/save-cover-letter', [CsrController::class, 'save_cover_letter']);
    Route::get('/get-tender-with-csr-pvms', [CsrController::class, 'get_tender_with_csr_pvms']);
    Route::get('/vendor-csr-json', [CsrController::class, 'vendorCsrJson']);


    Route::get('/pvms-stock-add', [ReceivePvmsBatchStockController::class, 'create']);
    Route::post('/pvms-stock-add', [ReceivePvmsBatchStockController::class, 'store']);

    Route::get('/pvms-stock-del', [ReceivePvmsBatchStockController::class, 'subtract']);
    Route::post('/pvms-stock-del', [ReceivePvmsBatchStockController::class, 'stockdel']);


    Route::get('/get_user_submitted_docs/{tender_id}/{user_id}', [TenderController::class, 'get_user_submitted_docs']);
    Route::get('/demand/download/pdf/{id}', [PDFController::class, 'demandPDF'])->name('demandPDF');
    Route::get('/demand/download/pdfuuid/{id}', [PDFController::class, 'demandPDFByUUID'])->name('demandPDF');
    Route::get('/issue-order/download/pdf/{id}', [PDFController::class, 'issueOrderPDF'])->name('issueOrderPDF');
    Route::get('/tender/download/pdf/{id}', [PDFController::class, 'tenderPDF'])->name('tenderPDF');
    Route::get('/demand/download/pdf-attached/', [PDFController::class, 'patientFile'])->name('patientFile');
    Route::get('/demand/pdf-attached/open/{id}', [PDFController::class, 'patientFileOpen'])->name('patientFileOpen');
    Route::get('/tender/pdf/cover-letter/{id}', [PDFController::class, 'csrCoverLetter'])->name('csrCoverLetter');

    Route::post('/tender-verify-document', [TenderController::class, 'tender_verify_vendor_doc']);

    Route::resource('/required-document', RequiredDocumentController::class);
    Route::resource('/patient', PatientController::class);
    Route::get('/patient-search', [PatientController::class, 'search']);
    Route::get('/patient-check-indetification_no', [PatientController::class, 'checkIdentificationNo']);
    Route::resource('/disease', DiseaseController::class);
    Route::resource('/notesheet', NotesheetController::class);
    Route::get('/create-retender-notesheet', [NotesheetController::class, 're_tender'])->name('re_tender.index');
    Route::get('/getLoogedUserApproval', [NotesheetController::class, 'getLoogedUserApproval']);
    Route::get('/suggested_notesheet_no_prefix.js', [NotesheetController::class, 'suggested_notesheet_no_prefix_js']);
    Route::resource('/tender', TenderController::class);
    Route::get('/uniq_tender/{tender_no}', [TenderController::class, 'uniq_tender_no']);
    Route::get('/uniq_notesheet/{notesheet_no}', [NotesheetController::class, 'uniq_notesheet_no']);
    Route::get('/uniq_demand_no/{demand_no}', [DemandController::class, 'uniq_demand_no']);
    Route::get('/get_notesheet_readyfor_tender', [TenderController::class, 'get_notesheet_readyfor_tender']);
    Route::get('/get_all_tender', [TenderController::class, 'get_all_tender']);
    Route::post('/tender_demo_xls', [TenderController::class, 'tender_demo_xls']);
    Route::post('/tender-update/{id}', [TenderController::class, 'update']);
    Route::get('/get_all_item_types', [ItemTypeController::class, 'get_all_types']);
    Route::get('/get_required_documents', [RequiredDocumentController::class, 'get_required_documents']);
    Route::get('/workorder-receive/next-crv', [WorkorderController::class, 'nextCrv']);

    Route::get('/workorder/new', [WorkorderController::class, 'newWorkorderCount']);
    Route::get('/workorder/details-json/{id}', [WorkorderController::class, 'showJson']);
    Route::get('/workorder-receive/details-json/{id}', [WorkorderController::class, 'showReceiveJson']);
    Route::get('/workorder/receive', [WorkorderController::class, 'workorderReceived'])->name('workorder.receive');
    Route::get('/workorder/receive/create', [WorkorderController::class, 'workorderReceivedCreate'])->name('workorder.receive.create');
    Route::post('/workorder/receive/store', [WorkorderController::class, 'workorderReceiveStore'])->name('workorder.receive.store');
    Route::get('/workorder/receive/{id}/edit', [WorkorderController::class, 'workorderReceivedEdit'])->name('workorder.receive.edit');
    Route::put('/workorder/receive/{id}/update', [WorkorderController::class, 'workorderReceivedUpdate'])->name('workorder.receive.update');
    Route::get('/workorder/json', [WorkorderController::class, 'workordersJson'])->name('workorder.csrs.json');
    Route::get('/workorder/csrs-json', [WorkorderController::class, 'workorderCsrJson'])->name('workorder.csr.json');
    Route::post('/workorder/approve', [WorkorderController::class, 'approval'])->name('workorder.approve');
    Route::post('/workorder/receive/approve', [WorkorderController::class, 'workorderReceiveApprove'])->name('workorder.receive.approve');
    Route::post('/workorder-update-document', [WorkorderController::class, 'workorderUpdateDocument'])->name('workorder.update.document');
    Route::post('/workorder-receive-update-document', [WorkorderController::class, 'workorderReceiveUpdateDocument'])->name('workorder_receive.update.document');
    Route::post('/workorder-pvms-remove', [WorkorderController::class, 'workorderPvmsRemove'])->name('workorder.remove');
    Route::resource('/workorder', WorkorderController::class);
    Route::resource('/annual_demand', AnnualDemandController::class);
    Route::get('/querterly_demand', [QuerterlyDemandController::class, 'index'])->name('querterly_demand.index');
    Route::post('/querterly_demand', [QuerterlyDemandController::class, 'store'])->name('querterly_demand.store');
    Route::put('/querterly_demand/{id}/update', [QuerterlyDemandController::class, 'update'])->name('querterly_demand.update');
    Route::get('/querterly_demand/create', [QuerterlyDemandController::class, 'create'])->name('querterly_demand.create');
    Route::get('/querterly_demand/view/{id}', [QuerterlyDemandController::class, 'show']);
    Route::get('/querterly_demand/approval/{id}', [QuerterlyDemandController::class, 'approval']);
    Route::get('/querterly_demand/details/json/{id}', [QuerterlyDemandController::class, 'showJson']);
    Route::get('/querterly_demand/details/delivery/json/{id}', [QuerterlyDemandController::class, 'showJsonDelivery']);
    Route::post('/querterly_demand/approve', [QuerterlyDemandController::class, 'approve']);
    Route::get('/querterly_demand/delivery', [QuerterlyDemandController::class, 'deliveryIndex'])->name('querterly_demand.delivery.index');
    Route::get('/querterly_demand/delivery/create/{demand_id}', [QuerterlyDemandController::class, 'deliveryCreate']);
    Route::post('/querterly_demand/delivery/store', [QuerterlyDemandController::class, 'deliveryStore']);
    Route::get('/querterly_demand/receive', [QuerterlyDemandController::class, 'receiveIndex'])->name('querterly_demand.receive.index');
    Route::get('/querterly_demand/receive/create/{receive_id}', [QuerterlyDemandController::class, 'receiveCreate']);
    Route::get('/querterly_demand/receive/details/json/{receive_id}', [QuerterlyDemandController::class, 'receiveDetailsJson']);
    Route::post('/querterly_demand/receive/store', [QuerterlyDemandController::class, 'receiveStore']);
    Route::get('/on-loan', [DemandController::class, 'on_loan'])->name('on-loan.index');
    Route::post('/on-loan-receive-stock', [DemandController::class, 'on_loan_receive_into_stock']);
    Route::get('/on-loan/api/{id}', [DemandController::class, 'on_loan_item_api']);
    Route::get('/on-loan-receive/{id}', [DemandController::class, 'on_loan_receive'])->name('on_loan.receive');
    Route::post('/on-loan', [DemandController::class, 'store_on_loan']);
    Route::get('/on-loan/list/json', [DemandController::class, 'onLoanListJson']);
    Route::get('/on-loan/create', [DemandController::class, 'create_on_loan'])->name('on_loan.create');
    Route::get('/on-loan-stock-adjust/create', [DemandController::class, 'onLoanStockAdjust'])->name('on_loan.stock.create');
    Route::post('/on-loan-stock-adjust/store', [DemandController::class, 'onLoanStockAdjustStore'])->name('on_loan.store');
    // dashboard search
    Route::get('dashboard/search', [DashboardController::class, 'search'])->name('search');

    Route::group(['prefix' => '/vendor'], function () {
        Route::get('/profile', [VendorController::class, 'profile'])->name('profile');
        Route::get('/tender-file/{id}', [VendorController::class, 'tenderFile'])->name('tender.file');
        Route::get('/download-tender-files/{id}', [TenderController::class, 'download_files'])->name('download_tender_files');
        Route::post('/submit-file', [VendorController::class, 'submitFile'])->name('submit.file');
        Route::get('/tender/purchase/{id}', [VendorController::class, 'purchase'])->name('purchase');
        Route::post('/tender/purchase/success', [VendorController::class, 'PaymentSuccess'])->name('purchase_success');
        Route::get('/tender/view/{id}', [VendorController::class, 'viewTender'])->name('viewTender');
    });

    Route::group(['prefix' => '/payment'], function () {
        Route::get('/', [PaymentController::class, 'index'])->name('payment.index');
        Route::get('/payment-report/{start_date}/{end_date}', [PaymentController::class, 'payment_report'])->name('payment.report');
        Route::get('/payment-report-monthly/{date}', [PaymentController::class, 'payment_report_monthly'])->name('payment.report_monthly');
    });

    // afmsd groupincharge data 
    Route::get('/purchase-data/{id}', [ReportController::class, 'getPurchaseData']);
    // afmsd get data in a table 
    Route::get('/all-issued-purchases', [ReportController::class, 'getAllIssuedPurchases']);
    // issue route new added
    Route::get('/issue-page/{id}', function ($id) {
        return view('admin.purchase_pvms_delivery.issue-page', ['id' => $id]);
    })->name('issue-page');






    // Report
    Route::get('/purchase-type-unit-stock/{id}', [ReportController::class, 'purchase_pvms_unit_stock']);
    Route::get('/receive-voucher-no-search', [ReportController::class, 'pvms_receive_voucher_no_search']);
    Route::get('/pvms-receive', [ReportController::class, 'purchase_pvms_receive'])->name('pvms.receive');
    Route::post('/lp-item-received', [ReportController::class, 'lp_item_received']);
    Route::post('/purchase-pvms-delivery-received', [ReportController::class, 'purchase_pvms_delivery_received']);
    Route::get('/unit-issue', [ReportController::class, 'purchase_pvms'])->name('purchase.pvms');
    Route::get('/unit-issue1', [ReportController::class, 'purchase_pvms'])->name('unit_issue.index');
    Route::get('/unit-delivery', [ReportController::class, 'unit_delivery'])->name('unit.delivery');
    Route::get('/unit-delivery-approval', [IssueOrderController::class, 'UnitDeliveryApproval'])->name('unit.delivery.approval');
    Route::post('/unit-delivery/approve/{id}', [IssueOrderController::class, 'approveUnitDelivery'])->name('unitDelivery.approve');
    Route::post('/purchase-type/approve/{id}', [IssueOrderController::class, 'approvePurchaseType'])->name('purchaseType.approve');
    Route::get('/purchase-type/details/{id}', [IssueOrderController::class, 'getPurchaseTypeDetails']);
    Route::get('/purchase-type/detail', [IssueOrderController::class, 'getPurchaseDetails']);


    // afmsd 
    Route::post('/afmsd-pvms-delivery-approvals', [AfmsdIssueApprovalController::class, 'storeIssueApprovl']);
    Route::get('/afmsd-pvms-delivery-approval', [ReportController::class, 'IssueApprovals']);
    Route::get('/afmsd-pvms-delivery-approval-afmsdCo', [ReportController::class, 'IssueApprovalsAfmsdCo']);
    Route::get('/afmsd-pvms-delivery-approval-group-incharge', [ReportController::class, 'IssueApprovalsAfmsdGroupIncharge']);
    Route::get('/afmsd-pvms-delivery-approval_groupIncharge', [AfmsdIssueApprovalController::class, 'IssueApprovalsItemForGroupIng']);
    Route::put('/afmsd-pvms-delivery-approvals-stockControlOfficer/{id}', [ReportController::class, 'updateAfmsdApprovalStockControlOfficer']);
    Route::put('/afmsd-pvms-delivery-approvals-group-incharge/{id}', [ReportController::class, 'updateAfmsdApprovalGroupIncharge']);




    Route::post('/purchase-pvms-delivery', [ReportController::class, 'purchase_pvms_delivery']);
    Route::post('/item-pvms-issue-delivery', [ReportController::class, 'purchase_pvms_issue_delivery']);
    Route::get('/purchase-order-list', [ReportController::class, 'purchase_order_list'])->name('purchase.pvms.order.list');
    Route::post('/purchase-order-list-approval', [ReportController::class, 'purchase_order_list_approval'])->name('purchase.pvms.order.list.approval');
    Route::group(['prefix' => '/report'], function () {
        Route::get('/company-order-due', [PVMSController::class, 'company_order_due'])->name('company.order_due.index');
        Route::get('/pvms-stock', [PVMSController::class, 'pvms_stock'])->name('pvms.stock.index');
        Route::get('/pvms-stock-transition', [PVMSController::class, 'pvms_stock_transition'])->name('pvms.stock_transition.index');
        Route::get('/pvms-expire-date-wise', [PVMSController::class, 'pvms_expire_date_wise'])->name('pvms.expire_date_wise.index');
        Route::get('/pvms-stock-position', [PVMSController::class, 'pvms_stock_position'])->name('pvms.stock_position.index');
        Route::get('/voucher-dispatch', [PVMSController::class, 'voucher_dispatch'])->name('voucher.dispatch.index');
        Route::get('/pvms-transit', [PVMSController::class, 'pvms_transit'])->name('report.transit.index');
        Route::get('/pvms-on-loan', [PVMSController::class, 'pvms_on_loan'])->name('report.onloan.index');
        Route::get('/pvms-on-loan-adjustment', [PVMSController::class, 'pvms_on_loan_adjustment'])->name('report.onloanadjustment.index');
        Route::get('/pvms-stock/{id}', [PVMSController::class, 'pvms_stock_batch_wise'])->name('pvms.stock.batch');
        // get data for batch and total stock
        Route::get('/get-pvms-store/{pvms_id}', [PVMSController::class, 'byPvmsId']);
        Route::post('/check-received-items', [PVMSController::class, 'checkReceivedItems']);


        Route::get('/supply-source-list-api', [ReportController::class, 'supply_source_list']);
        Route::get('/pvms-stock-list-api', [PVMSController::class, 'pvms_stock_list']);
        Route::get('/pvms-stock-position-list-api', [PVMSController::class, 'pvms_stock_position_list']);
        Route::get('/pvms-transit-list-api', [PVMSController::class, 'pvms_transit_list']);
        Route::get('/company-due-order-api', [WorkorderController::class, 'company_order_due_api']);
        Route::get('/pvms-on-loan-list-api', [PVMSController::class, 'pvms_on_loan_list']);
        Route::get('/pvms-stock-transition-api', [PVMSController::class, 'pvms_stock_transition_list']);
        Route::get('/voucher-dispatch-list-api', [PVMSController::class, 'voucher_dispatch_list']);
        Route::get('/pvms-on-loan-adjust-list-api', [PVMSController::class, 'pvms_on_loan_adjust_list']);
        Route::get('/pvms-expire-date-wise-list-api', [PVMSController::class, 'pvms_expire_given_month_list']);
        Route::get('/tracking', [TrackingContoller::class, 'index'])->name('report.tracking.index');
        Route::get('/tracking-info', [TrackingContoller::class, 'track'])->name('report.tracking.search');
        Route::get('/search-by-date', [ReportController::class, 'index'])->name('report.search.index');
        Route::get('/supply-source', [ReportController::class, 'supply_source'])->name('report.supply_source.index');
        Route::get('/search-by-date/search', [ReportController::class, 'search'])->name('report.search.search');
        Route::get('/purchase-type', [ReportController::class, 'purchase'])->name('report.purchase');
        Route::get('/purchase-type/search', [ReportController::class, 'purchase'])->name('report.purchase.search');
        Route::post('/purchase-type/store', [ReportController::class, 'store'])->name('report.purchase.store');
        Route::get('/annual-demand', [AnnualDemandReportController::class, 'index'])->name('report.annual.demand.index');
        Route::get('/annual-demand/result', [AnnualDemandReportController::class, 'annualDemand'])->name('report.annual.demand.result');
        Route::get('/bill-process', [ReportController::class, 'billProcess'])->name('report.bill.process');
        Route::get('/bill-process/result', [ReportController::class, 'billProcessResult'])->name('report.bill.process.result');
        Route::get('/annual-demand-unit', [ReportController::class, 'annualUnitDemand'])->name('report.annual-demand-unit');
        Route::get('/annual-demand-unit/json', [ReportController::class, 'annualUnitDemandJson'])->name('report.annual-demand-unit.json');
        Route::get('/unit-wise-item-issue', [ReportController::class, 'unitWiseItemIssue'])->name('report.unit-wise-item-issue');

        Route::get('/latest-afmsd-receive', [ReportController::class, 'latest_afmsd_receive'])->name('latest.afmsd.receive');
        Route::get('/latest-afmsd-receive-api', [ReportController::class, 'latest_afmsd_receive_api']);

        Route::get('/upcoming-expiry', [ReportController::class, 'upcoming_expiry'])->name('upcoming.expiry');

        // Notices
        Route::get('/urgent-notices', [NoticeController::class, 'index'])->name('urgent.notices');
        Route::get('/urgent-notices/create', [NoticeController::class, 'create'])->name('urgent.notices.create');
        Route::post('/urgent-notices/store', [NoticeController::class, 'store'])->name('urgent.notices.store');
    });

    Route::get('/issue-order', [IssueOrderController::class, 'index'])->name('issue.order');
    Route::get('/issue-order/details/{id}', [IssueOrderController::class, 'getPurchaseDetailsbyId']);
    Route::post('/issue-order/approve/{id}', [IssueOrderController::class, 'approveOrder'])->name('issueOrder.approve');
    Route::get('/manual-issue-afmsd', [IssueOrderController::class, 'createManualIssue'])->name('issue.manual');
    Route::post('/save-manual-afmsd-isse', [IssueOrderController::class, 'saveManualIssue'])->name('issue.manual');

    Route::get('/cmh-list', [IssueOrderController::class, 'cmhList']);

    // Route::resource('/manual-issue-afmsd', IssueOrderController::class);
    //    setting get_all_types
    Route::group(['prefix' => '/settings'], function () {
        //Group Management
        Route::get('/group-management', [ItemGroupController::class, 'index'])->name('all.group.management');
        Route::get('/add/group-management', [ItemGroupController::class, 'create'])->name('add.group.management');
        Route::post('/add/group-management', [ItemGroupController::class, 'store'])->name('store.group.management');
        Route::get('/edit/group-management/{id}', [ItemGroupController::class, 'edit'])->name('edit.group.management');
        Route::post('/edit/group-management/{id}', [ItemGroupController::class, 'update'])->name('update.group.management');
        Route::post('/delete/group-management/{id}', [ItemGroupController::class, 'destroy'])->name('delete.group.management');
        //account-unit
        Route::get('/account-units', [AccountUnitController::class, 'index'])->name('all.account.units');
        Route::get('/add/account-units', [AccountUnitController::class, 'create'])->name('add.account.unit');
        Route::post('/add/account-units', [AccountUnitController::class, 'store'])->name('store.account.unit');
        Route::get('/edit/account-units/{id}', [AccountUnitController::class, 'edit'])->name('edit.account.unit');
        Route::post('/edit/account-units/{id}', [AccountUnitController::class, 'update'])->name('update.account.unit');
        Route::post('/delete/account-units/{id}', [AccountUnitController::class, 'destroy'])->name('delete.account.unit');
        //demand-unit
        Route::get('/demand-units', [DemandUnitController::class, 'index'])->name('all.demand.unit');
        Route::get('/add/demand-units', [DemandUnitController::class, 'create'])->name('add.demand.unit');
        Route::post('/add/demand-units', [DemandUnitController::class, 'store'])->name('store.demand.unit');
        Route::get('/edit/demand-units/{id}', [DemandUnitController::class, 'edit'])->name('edit.demand.unit');
        Route::post('/edit/demand-units/{id}', [DemandUnitController::class, 'update'])->name('update.demand.unit');
        Route::post('/delete/demand-units/{id}', [DemandUnitController::class, 'destroy'])->name('delete.demand.unit');
        Route::get('/get-sub-organization', [DemandController::class, 'getSubOrganization'])->name('get.sub.organization');
        //item types
        Route::get('/item-types', [ItemTypeController::class, 'index'])->name('all.item.types');
        Route::get('/add/item-types', [ItemTypeController::class, 'create'])->name('add.item.types');
        Route::post('/add/item-types', [ItemTypeController::class, 'store'])->name('store.item.types');
        Route::get('/edit/item-types/{id}', [ItemTypeController::class, 'edit'])->name('edit.item.types');
        Route::post('/edit/item-types/{id}', [ItemTypeController::class, 'update'])->name('update.item.types');
        Route::post('/delete/item-types/{id}', [ItemTypeController::class, 'destroy'])->name('delete.item.types');
        //item departments
        Route::get('/item-department', [ItemDepartmentController::class, 'index'])->name('all.item.department');
        Route::get('/add/item-department', [ItemDepartmentController::class, 'create'])->name('add.item.department');
        Route::post('/add/item-department', [ItemDepartmentController::class, 'store'])->name('store.item.department');
        Route::get('/edit/item-department/{id}', [ItemDepartmentController::class, 'edit'])->name('edit.item.department');
        Route::post('/edit/item-department/{id}', [ItemDepartmentController::class, 'update'])->name('update.item.department');
        Route::post('/delete/item-department/{id}', [ItemDepartmentController::class, 'destroy'])->name('delete.item.department');
        //warranty types
        Route::get('/warranty-type', [WarrantyTypeController::class, 'index'])->name('all.warranty.type');
        Route::get('/add/warranty-type', [WarrantyTypeController::class, 'create'])->name('add.warranty.type');
        Route::post('/add/warranty-type', [WarrantyTypeController::class, 'store'])->name('store.warranty.type');
        Route::get('/edit/warranty-type/{id}', [WarrantyTypeController::class, 'edit'])->name('edit.warranty.type');
        Route::post('/edit/warranty-type/{id}', [WarrantyTypeController::class, 'update'])->name('update.warranty.type');
        Route::post('/delete/warranty-type/{id}', [WarrantyTypeController::class, 'destroy'])->name('delete.warranty.type');
        //division
        Route::get('/division', [DivisionController::class, 'index'])->name('all.division');
        Route::get('/add/division', [DivisionController::class, 'create'])->name('add.division');
        Route::post('/add/division', [DivisionController::class, 'store'])->name('store.division');
        Route::get('/edit/division/{id}', [DivisionController::class, 'edit'])->name('edit.division');
        Route::post('/edit/division/{id}', [DivisionController::class, 'update'])->name('update.division');
        Route::post('/delete/division/{id}', [DivisionController::class, 'destroy'])->name('delete.division');

        //        specification
        Route::get('/specification', [SpecificationController::class, 'index'])->name('all.specification');
        Route::get('/add/specification', [SpecificationController::class, 'create'])->name('add.specification');
        Route::post('/store/specification', [SpecificationController::class, 'store'])->name('store.specification');
        Route::get('/edit/specification/{id}', [SpecificationController::class, 'edit'])->name('edit.specification');
        Route::post('/update/specification', [SpecificationController::class, 'update'])->name('update.specification');
        Route::post('/delete/specification/{id}', [SpecificationController::class, 'delete'])->name('delete.specification');

        //        Services
        Route::get('/service', [ServiceController::class, 'index'])->name('all.service');
        Route::get('/add/service', [ServiceController::class, 'create'])->name('add.service');
        Route::post('/store/service', [ServiceController::class, 'store'])->name('store.service');
        Route::get('/edit/service/{id}', [ServiceController::class, 'edit'])->name('edit.service');
        Route::post('/update/service', [ServiceController::class, 'update'])->name('update.service');
        Route::post('/delete/service/{id}', [ServiceController::class, 'delete'])->name('delete.service');

        //        Financial Year
        Route::get('/financial-year', [FinancialYearController::class, 'index'])->name('all.financial.year');
        Route::get('/financial-years/api', [FinancialYearController::class, 'indexApi'])->name('all.financial.year.api');
        Route::get('/add/financial-year', [FinancialYearController::class, 'create'])->name('add.financial.year');
        Route::post('/store/financial-year', [FinancialYearController::class, 'store'])->name('store.financial.year');
        Route::get('/edit/financial-year/{id}', [FinancialYearController::class, 'edit'])->name('edit.financial.year');
        Route::post('/update/financial-year', [FinancialYearController::class, 'update'])->name('update.financial.year');
        Route::post('/delete/financial-year/{id}', [FinancialYearController::class, 'delete'])->name('delete.financial.year');

        //        Item Section
        Route::get('/item-section', [ItemSectionController::class, 'index'])->name('all.item.section');
        Route::get('/add/item-section', [ItemSectionController::class, 'create'])->name('add.item.section');
        Route::post('/store/item-section', [ItemSectionController::class, 'store'])->name('store.item.section');
        Route::get('/edit/item-section/{id}', [ItemSectionController::class, 'edit'])->name('edit.item.section');
        Route::post('/update/item-section', [ItemSectionController::class, 'update'])->name('update.item.section');
        Route::post('/delete/item-section/{id}', [ItemSectionController::class, 'delete'])->name('delete.item.section');

        //        PVMS
        Route::get('/pvms', [PVMSController::class, 'index'])->name('all.pvms');
        Route::get('/pvms/search', [PVMSController::class, 'search'])->name('all.search');
        Route::get('/pvms/search-annual-demand', [PVMSController::class, 'search_annual_demand'])->name('all.search.anuual_demand');
        Route::get('/add/pvms', [PVMSController::class, 'create'])->name('add.pvms');
        Route::post('/store/pvms', [PVMSController::class, 'store'])->name('store.pvms');
        Route::get('/edit/pvms/{id}', [PVMSController::class, 'edit'])->name('edit.pvms');
        Route::post('/update/pvms', [PVMSController::class, 'update'])->name('update.pvms');
        Route::get('/add-pvms-stock/pvms/{id}', [PVMSController::class, 'addPvmsStock'])->name('edit.add-pvms-stock');
        Route::post('/update-pvms-stock', [PVMSController::class, 'updatePvmsStock'])->name('update-pvms-stock');
        Route::post('/delete/pvms/{id}', [PVMSController::class, 'delete'])->name('delete.pvms');

        //        NIV
        Route::get('/add/pvms/niv', [PVMSController::class, 'createNIV'])->name('add.pvms.niv');
        Route::post('/store/pvms/niv', [PVMSController::class, 'storeNIV'])->name('store.pvms.niv');

        //        settings
        Route::get('/config', [SettingController::class, 'index'])->name('all.config');
        Route::get('/add/config', [SettingController::class, 'create'])->name('add.config');
        Route::post('/store/config', [SettingController::class, 'store'])->name('store.config');
        Route::get('/edit/config/{id}', [SettingController::class, 'edit'])->name('edit.config');
        Route::post('/update/config', [SettingController::class, 'update'])->name('update.config');
        Route::post('/delete/config/{id}', [SettingController::class, 'delete'])->name('delete.config');

        //        tender documents
        Route::get('/tender/documents', [RequiredDocumentController::class, 'index'])->name('all.tender.documents');
        Route::get('/add/tender/documents', [RequiredDocumentController::class, 'create'])->name('add.tender.documents');
        Route::post('/store/tender/documents', [RequiredDocumentController::class, 'store'])->name('store.tender.documents');
        Route::get('/edit/tender/documents/{id}', [RequiredDocumentController::class, 'edit'])->name('edit.tender.documents');
        Route::post('/update/tender/documents', [RequiredDocumentController::class, 'update'])->name('update.tender.documents');
        Route::post('/delete/tender/documents/{id}', [RequiredDocumentController::class, 'delete'])->name('delete.tender.documents');

        //        CMH Department
        Route::get('/cmh/department', [CMHDepartmentController::class, 'index'])->name('all.CMHDepartment');
        Route::get('/add/cmh/department', [CMHDepartmentController::class, 'create'])->name('add.CMHDepartment');
        Route::post('/store/cmh/department', [CMHDepartmentController::class, 'store'])->name('store.CMHDepartment');
        Route::get('/edit/cmh/department/{id}', [CMHDepartmentController::class, 'edit'])->name('edit.CMHDepartment');
        Route::post('/update/cmh/department', [CMHDepartmentController::class, 'update'])->name('update.CMHDepartment');
        Route::post('/delete/cmh/department/{id}', [CMHDepartmentController::class, 'delete'])->name('delete.CMHDepartment');

        //        CMH Department Category
        Route::get('/cmh/department/category', [CMHDepartmentCategoryController::class, 'index'])->name('all.CMHDepartmentCategory');
        Route::get('/add/cmh/department/category', [CMHDepartmentCategoryController::class, 'create'])->name('add.CMHDepartmentCategory');
        Route::post('/store/cmh/department/category', [CMHDepartmentCategoryController::class, 'store'])->name('store.CMHDepartmentCategory');
        Route::get('/edit/cmh/department/category/{id}', [CMHDepartmentCategoryController::class, 'edit'])->name('edit.CMHDepartmentCategory');
        Route::post('/update/cmh/department/category', [CMHDepartmentCategoryController::class, 'update'])->name('update.CMHDepartmentCategory');
        Route::post('/delete/cmh/department/category/{id}', [CMHDepartmentCategoryController::class, 'delete'])->name('delete.CMHDepartmentCategory');

        //        Authorized Equipment
        Route::get('/authorized/equipment', [AuthorizedEquipmentController::class, 'index'])->name('all.Authorized');
        Route::get('/add/authorized/equipment', [AuthorizedEquipmentController::class, 'create'])->name('add.Authorized');
        Route::post('/store/authorized/equipment', [AuthorizedEquipmentController::class, 'store'])->name('store.Authorized');
        Route::get('/edit/authorized/equipment/{id}', [AuthorizedEquipmentController::class, 'edit'])->name('edit.Authorized');
        Route::post('/update/authorized/equipment', [AuthorizedEquipmentController::class, 'update'])->name('update.Authorized');
        Route::post('/delete/authorized/equipment/{id}', [AuthorizedEquipmentController::class, 'delete'])->name('delete.Authorized');
        Route::get('/authorized/equipment/dept-name', [AuthorizedEquipmentController::class, 'GetDept'])->name('GetDept');

        //        HOD
        Route::get('/cmh/hod', [HODController::class, 'index'])->name('all.HOD');
        Route::get('/add/cmh/hod', [HODController::class, 'create'])->name('add.HOD');
        Route::post('/store/cmh/hod', [HODController::class, 'store'])->name('store.HOD');
        Route::get('/edit/cmh/hod/{id}', [HODController::class, 'edit'])->name('edit.HOD');
        Route::post('/update/cmh/hod', [HODController::class, 'update'])->name('update.HOD');
        Route::post('/delete/cmh/hod/{id}', [HODController::class, 'delete'])->name('delete.HOD');

        // Patient
        Route::get('/patient', [PatientsController::class, 'index'])->name('all.patient');
        Route::get('/add/patient', [PatientsController::class, 'create'])->name('add.patient');
        Route::post('/store/patient', [PatientsController::class, 'store'])->name('store.patient');
        Route::get('/edit/patient/{id}', [PatientsController::class, 'edit'])->name('edit.patient');
        Route::post('/update/patient/{id}', [PatientsController::class, 'update'])->name('update.patient');
        Route::post('/delete/patient/{id}', [PatientsController::class, 'destroy'])->name('delete.patient');

        // Supplier
        Route::get('/supplier', [SupplierController::class, 'index'])->name('all.supplier');
        Route::get('/add/supplier', [SupplierController::class, 'create'])->name('add.supplier');
        Route::post('/store/supplier', [SupplierController::class, 'store'])->name('store.supplier');
        Route::get('/edit/supplier/{id}', [SupplierController::class, 'edit'])->name('edit.supplier');
        Route::post('/update/supplier/{id}', [SupplierController::class, 'update'])->name('update.supplier');
        Route::post('/delete/supplier/{id}', [SupplierController::class, 'destroy'])->name('delete.supplier');

        // Rate running pvms
        Route::get('/rate-running-pvms', [RateRunningPvmsController::class, 'index'])->name('all.rate.running');
        Route::get('/add/rate-running-pvms', [RateRunningPvmsController::class, 'create'])->name('add.rate.running');
        Route::post('/store/rate-running-pvms', [RateRunningPvmsController::class, 'store'])->name('store.rate.running');
        Route::get('/edit/rate-running-pvms/{id}', [RateRunningPvmsController::class, 'edit'])->name('edit.rate.running');
        Route::post('/update/rate-running-pvms/{id}', [RateRunningPvmsController::class, 'update'])->name('update.rate.running');
        Route::post('/delete/rate-running-pvms/{id}', [RateRunningPvmsController::class, 'destroy'])->name('delete.rate.running');

        Route::resource('/wing', WingController::class);
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// SSLCOMMERZ Start
Route::get('/example1', [SslCommerzPaymentController::class, 'exampleEasyCheckout']);
Route::get('/example2', [SslCommerzPaymentController::class, 'exampleHostedCheckout']);

Route::post('/pay', [SslCommerzPaymentController::class, 'index']);
Route::post('/pay-via-ajax', [SslCommerzPaymentController::class, 'payViaAjax']);

Route::post('/success', [SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [SslCommerzPaymentController::class, 'cancel']);

Route::post('/ipn', [SslCommerzPaymentController::class, 'ipn']);
//SSLCOMMERZ END

// import pvms excel file
Route::get('pvms/import/excel', [PVMSController::class, 'importView'])->name('import.view');
Route::post('pvms/import/excel/upload', [PVMSController::class, 'import'])->name('import.pvms');
Route::get('notesheet/download/pdf/{id}', [PDFController::class, 'notesheetPDF']);
Route::get('csr/download/pdf/{id}', [PDFController::class, 'csrPDF']);
Route::get('workorder/download/pdf/{id}', [PDFController::class, 'workOrder']);
Route::get('issue/download/pdf/{id}', [PDFController::class, 'issuePDF']);
Route::get('demo/download/pdf/{id}', [PDFController::class, 'generate_pdf']);
Route::get('pvms/import/excel/check', [PVMSController::class, 'checkDuplicate']);

// afmsd issue PDF 
Route::get('/issue/print/{id}', [PDFController::class, 'afmsdIssuePDF'])->name('afmsdIssuePDF');

Route::get('pvms/import/excel/stock', [PVMSController::class, 'importViewStock'])->name('import.stock.view');
Route::post('pvms/import/excel/stock/upload', [PVMSController::class, 'importStock'])->name('import.stock');

require __DIR__ . '/auth.php';
