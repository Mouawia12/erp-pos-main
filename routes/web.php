<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Admin\Auth\LoginController;
use App\Http\Controllers\Admin\HomeController; 
use App\Http\Controllers\Admin\WarehouseController;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\UnitController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CurrencyController;
use App\Http\Controllers\Admin\ExpensesCategoryController;
use App\Http\Controllers\Admin\TaxRatesController;
use App\Http\Controllers\Admin\CustomerGroupController;
use App\Http\Controllers\Admin\CompanyController;
use App\Http\Controllers\Admin\SystemSettingsController;
use App\Http\Controllers\Admin\PosSettingsController;
use App\Http\Controllers\Admin\CashierController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\UserGroupController;
use App\Http\Controllers\Admin\RepresentativeController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\UpdateQuntityController;
use App\Http\Controllers\Admin\SalesController;
use App\Http\Controllers\Admin\PurchaseController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\CompanyStatusReportController;
use App\Http\Controllers\Admin\JournalController;
use App\Http\Controllers\Admin\AccountsTreeController;
use App\Http\Controllers\Admin\AccountSettingController;
use App\Http\Controllers\Admin\ExpensesController;
use App\Http\Controllers\Admin\InitializeController;
use App\Http\Controllers\Admin\EmployerCategoryController;
use App\Http\Controllers\Admin\DeductionController;
use App\Http\Controllers\Admin\RewardController;
use App\Http\Controllers\Admin\AdvancePaymentController;
use App\Http\Controllers\Admin\SalaryDocController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ManufacturingController;
use App\Http\Controllers\Admin\SalonDepartmentController;
use App\Http\Controllers\Admin\SalonReservationController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\AlertController;
use App\Http\Controllers\Admin\ReportAccountController;
use App\Http\Controllers\Admin\CompanyInfoController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\BranchController; 
use App\Http\Controllers\Admin\CatchReciptController;
use App\Http\Controllers\Admin\CostCenterController;
use App\Http\Controllers\Admin\StockCountController;
use App\Http\Controllers\Admin\FiscalYearController;
use App\Http\Controllers\Admin\OpeningBalanceController;
use App\Http\Controllers\Admin\FinancialStatementController;
use App\Http\Controllers\Admin\PosController;
use App\Http\Controllers\Admin\BackupController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\InvoiceTermTemplateController;
use App\Http\Controllers\Admin\ZatcaController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('hash',function(){
    return \Hash::make(123456);
});
Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath', 'persist.locale']
    ], function () {
    Route::get('/', function () {
        if (Auth::guard('admin-web')->check()) {
            return redirect()->route('admin.home');
        }

        return view('admin.auth.login');
    })->name('index');

// *********  admin Routes ******** //
Route::group(
    [
        'namespace' => 'Admin'
    ], function () {
    Auth::routes(
        [
            'verify' => false,
            'register' => false,
            'reset' => false,
            'confirm' => false,
        ]
    );

    Route::get( 'admin/login', [LoginController::class, 'showLoginForm' ] )->name('admin.login');
	Route::post( 'admin/login', [LoginController::class, 'login' ] );

});

Route::group(
    ['middleware' => ['auth:admin-web','single.device','subscription.active'],
        'prefix' => 'admin',
        'namespace' => 'Admin'
    ], function () {

    Route::get('/', function () {
        return redirect()->route('admin.home');
    });
    Route::get('/home',  [HomeController::class, 'index' ])->name('admin.home');
    Route::get( '/lock-screen', [HomeController::class, 'lock_screen' ] )->name('admin.lock.screen');
    Route::post( 'admin/logout', [LoginController::class, 'logout' ] )->name('admin.logout'); 
	
       // admins Routes
       Route::resource('admins', AdminController::class)->names([
        'index' => 'admin.admins.index',
        'create' => 'admin.admins.create',
        'update' => 'admin.admins.update',
        'destroy' => 'admin.admins.destroy',
        'edit' => 'admin.admins.edit',
        'store' => 'admin.admins.store',
        'show' => 'admin.admins.show'
    ]); 
    Route::post('/remove-selected-admins', [AdminController::class, 'remove_selected' ] )->name('remove.selected.admins');
    Route::get('/print-selected-admins', [AdminController::class, 'print_selected' ] )->name('print.selected.admins');
    Route::post('/export-admins-excel', [AdminController::class, 'export_members_excel' ])->name('export.admins.excel');


    // adminProfile Routes
    Route::get('profile/edit/{id}',  [AdminController::class, 'edit_profile' ] )->name('admin.profile.edit');
    Route::patch('profile/update/{id}', [AdminController::class, 'update_profile' ] )->name('admin.profile.update');

    // Roles Routes
    Route::resource('roles', RoleController::class)->names([
        'index' => 'admin.roles.index',
        'create' => 'admin.roles.create',
        'update' => 'admin.roles.update',
        'destroy' => 'admin.roles.destroy',
        'edit' => 'admin.roles.edit',
        'store' => 'admin.roles.store',
    ]);

    // branches Routes
    Route::resource('branches', BranchController::class)->names([
        'index' => 'admin.branches.index',
        'create' => 'admin.branches.create',
        'update' => 'admin.branches.update',
        'destroy' => 'admin.branches.destroy',
        'edit' => 'admin.branches.edit',
        'store' => 'admin.branches.store', 
        'show' => 'admin.branches.show'
    ]);
    
    Route::post('/remove-selected-branches', [BranchController::class, 'remove_selected' ] )->name('remove.selected.branches');
    Route::get('/print-selected-branches', [BranchController::class, 'print_selected' ] )->name('print.selected.branches');
    Route::post('/export-branches-excel',[BranchController::class, 'export_branches_excel' ])->name('export.branches.excel');

    // Backup
    Route::get('/backup/database', [BackupController::class, 'download'])->name('admin.backup.db');

    // Invoice terms templates
    Route::resource('invoice-terms', InvoiceTermTemplateController::class)->names([
        'index' => 'admin.invoice_terms.index',
        'store' => 'admin.invoice_terms.store',
        'update' => 'admin.invoice_terms.update',
        'destroy' => 'admin.invoice_terms.destroy',
        'create' => 'admin.invoice_terms.create',
        'edit' => 'admin.invoice_terms.edit',
        'show' => 'admin.invoice_terms.show',
    ])->except(['show','create','edit']);

    // Owner / Subscribers dashboard (SaaS control panel)
    Route::group(['prefix' => 'owner', 'as' => 'owner.', 'middleware' => ['role:system_owner,admin-web']], function () {
        Route::resource('subscribers', SubscriberController::class)->except(['show']);
        Route::post('subscribers/{subscriber}/renew', [SubscriberController::class, 'renew'])->name('subscribers.renew');
        Route::get('subscribers/{subscriber}/permissions', [SubscriberController::class, 'permissions'])->name('subscribers.permissions');
        Route::post('subscribers/{subscriber}/permissions', [SubscriberController::class, 'updatePermissions'])->name('subscribers.permissions.update');
        Route::delete('documents/{document}', [SubscriberController::class, 'deleteDocument'])->name('documents.destroy');
        Route::post('documents/{document}/archive', [SubscriberController::class, 'archiveDocument'])->name('documents.archive');
    });
    
    // Inventory Routes
       Route::resource('inventory', InventoryController::class)->names([
        'index' => 'admin.inventory.index',
        'create' => 'admin.inventory.create',
        'edit' => 'admin.inventory.edit',
        'destroy' => 'admin.inventory.destroy', 
    ]); 
    Route::get('/inventory/state/{id}',  [InventoryController::class, 'inventory_state' ] )->name('inventory.state');
    Route::get('/private_inventory',  [InventoryController2::class, 'insert_private_inventory' ] )->name('private_inventory');
    
    Route::post('/updateProduct', [InventoryController::class, 'update_weight_item' ] )->name('admin.inventory.update');
    Route::post('/add-item', [InventoryController::class, 'inventory_weight_item' ] )->name('admin.inventory.add');
    Route::post('/inventory/match', [InventoryController::class, 'match_inventory'])->name('admin.inventory.match');
    Route::get('/getItemInventory/{code}', [InventoryController::class, 'getProduct'])->name('getItems');

    Route::get('manufacturing', [ManufacturingController::class, 'index'])->name('admin.manufacturing.index');
    Route::post('manufacturing/recipes', [ManufacturingController::class, 'storeRecipe'])->name('admin.manufacturing.recipes.store');
    Route::post('manufacturing/assemble', [ManufacturingController::class, 'assemble'])->name('admin.manufacturing.assemble');

    Route::get('/brands', [BrandController::class, 'index'])->name('brands');
    Route::post('storeBrand', [BrandController::class, 'store'])->name('storeBrand');
    Route::get('/deleteBrand/{id}', [BrandController::class, 'destroy'])->name('deleteBrand');
    Route::get('/getBrand/{id}', [BrandController::class, 'edit'])->name('getBrand');
    
    Route::get('/units', [UnitController::class, 'index'])->name('units');
    Route::post('storeUnit', [UnitController::class, 'store'])->name('storeUnit');
    Route::get('/cost_centers', [CostCenterController::class, 'index'])->name('cost_centers');
    Route::post('/cost_centers', [CostCenterController::class, 'store'])->name('cost_centers.store');
    Route::get('/cost_centers/{id}', [CostCenterController::class, 'edit'])->name('cost_centers.edit');
    Route::get('/cost_centers/delete/{id}', [CostCenterController::class, 'destroy'])->name('cost_centers.delete');
    Route::get('/deleteUnit/{id}', [UnitController::class, 'destroy'])->name('deleteUnit');
    Route::get('/getUnit/{id}', [UnitController::class, 'edit'])->name('getUnit');
    
    Route::get('/categories', [CategoryController::class, 'index'])->name('categories');
    Route::post('storeCategory', [CategoryController::class, 'store'])->name('storeCategory');
    Route::get('/deleteCategory/{id}', [CategoryController::class, 'destroy'])->name('deleteCategory');
    Route::get('/getCategory/{id}', [CategoryController::class, 'edit'])->name('getCategory');
    Route::get('/categories/tree/json', [CategoryController::class, 'tree'])->name('categories.tree');
    
    Route::get('/currency', [CurrencyController::class, 'index'])->name('currency');
    Route::post('storeCurrency', [CurrencyController::class, 'store'])->name('storeCurrency');
    Route::get('/deleteCurrency/{id}', [CurrencyController::class, 'destroy'])->name('deleteCurrency');
    Route::get('/getCurrency/{id}', [CurrencyController::class, 'edit'])->name('getCurrency');
    
    Route::get('/expenses', [ExpensesCategoryController::class, 'index'])->name('expenses');
    Route::post('storeExpense', [ExpensesCategoryController::class, 'store'])->name('storeExpense');
    Route::get('/deleteExpense/{id}', [ExpensesCategoryController::class, 'destroy'])->name('deleteExpense');
    Route::get('/getExpense/{id}', [ExpensesCategoryController::class, 'edit'])->name('getExpense');
    
    Route::get('/warehouses', [WarehouseController::class, 'index'])->name('warehouses');
    Route::post('storeWarehouse', [WarehouseController::class, 'store'])->name('storeWarehouse');
    Route::get('/getWarehouse/{id}', [WarehouseController::class, 'edit'])->name('getWarehouse');
    Route::get('/get-warehouses-branches/{branche_id}', [WarehouseController::class, 'get_warehouses_branches'])->name('get.warehouses.branches');
    Route::get('/deleteWarehouse/{id}', [WarehouseController::class, 'destroy'])->name('deleteWarehouse');
    
    Route::get('/taxRates', [TaxRatesController::class, 'index'])->name('taxRates');
    Route::post('storeTaxRate', [TaxRatesController::class, 'store'])->name('storeTaxRate');
    Route::get('/deleteTaxRate/{id}', [TaxRatesController::class, 'destroy'])->name('deleteTaxRate');
    Route::get('/getTaxRate/{id}', [TaxRatesController::class, 'edit'])->name('getTaxRate');
    
    Route::get('/clientGroups', [CustomerGroupController::class, 'index'])->name('clientGroups');
    Route::post('storeClientGroup', [CustomerGroupController::class, 'store'])->name('storeClientGroup');
    Route::get('/deleteClientGroup/{id}', [CustomerGroupController::class, 'destroy'])->name('deleteClientGroup');
    Route::get('/getClientGroup/{id}', [CustomerGroupController::class, 'edit'])->name('getClientGroup');
    
    Route::get('/clients/{type}', [CompanyController::class, 'index'])->name('clients');
    Route::post('storeCompany', [CompanyController::class, 'store'])->name('storeCompany');
    Route::get('/deleteCompany/{id}', [CompanyController::class, 'destroy'])->name('deleteCompany');
    Route::get('/getCompany/{id}', [CompanyController::class, 'edit'])->name('getCompany');
    
    Route::get('/system_settings', [SystemSettingsController::class, 'index'])->name('system_settings');
    Route::post('storeSettings', [SystemSettingsController::class, 'store'])->name('storeSettings');
    Route::put('updateSettings', [SystemSettingsController::class, 'update'])->name('updateSettings');
    Route::post('/system_settings/enable-negative-stock', [SystemSettingsController::class, 'enableNegativeStock'])->name('system_settings.enable_negative_stock');
    Route::post('/zatca/onboard', [ZatcaController::class, 'onboard'])->name('zatca.onboard');
    Route::post('/zatca/sales/{sale}/send', [ZatcaController::class, 'sendInvoice'])->name('zatca.sales.send');
    Route::post('/zatca/documents/{document}/resend', [ZatcaController::class, 'resendDocument'])->name('zatca.documents.resend');
    Route::post('/zatca/manual-send', [ZatcaController::class, 'sendByReference'])->name('zatca.manual_send');
    
    Route::get('/pos_settings', [PosSettingsController::class, 'index'])->name('pos_settings');
    Route::post('storePosSettings', [PosSettingsController::class, 'store'])->name('storePosSettings');
    Route::post('updatePosSettings', [PosSettingsController::class, 'update'])->name('updatePosSettings');

    Route::get('/alerts', [AlertController::class, 'index'])->name('alerts.index');
    Route::post('/alerts/refresh', [AlertController::class, 'refresh'])->name('alerts.refresh');
    Route::post('/alerts/{alert}/read', [AlertController::class, 'markRead'])->name('alerts.read');
    Route::post('/alerts/{alert}/resolve', [AlertController::class, 'resolve'])->name('alerts.resolve');
    
    Route::get('/cashiers', [CashierController::class, 'index'])->name('cashiers');
    Route::post('storeCashier', [CashierController::class, 'store'])->name('storeCashier');
    Route::get('/deleteCashier/{id}', [CashierController::class, 'destroy'])->name('deleteCashier');
    Route::get('/getCashier/{id}', [CashierController::class, 'edit'])->name('getCashier');
    
    Route::get('/users', [UserController::class, 'index'])->name('users');
    Route::post('storeUser', [UserController::class, 'store'])->name('storeUser');
    Route::get('/deleteUser/{id}', [UserController::class, 'destroy'])->name('deleteUser');
    Route::get('/getUser/{id}', [UserController::class, 'edit'])->name('getUser');
    Route::post('reset_password', [UserController::class, 'reset_password'])->name('reset_password');     
   
    Route::get('/user_groups', [UserGroupController::class, 'index'])->name('user_groups');
    Route::post('storeUserGroup', [UserGroupController::class, 'store'])->name('storeUserGroup');
    Route::get('/deleteUserGroup/{id}', [UserGroupController::class, 'destroy'])->name('deleteUserGroup');
    Route::get('/getUserGroup/{id}', [UserGroupController::class, 'edit'])->name('getUserGroup');
    
    
    Route::get('/representatives', [RepresentativeController::class, 'index'])->name('representatives');
    Route::post('storeRepresentative', [RepresentativeController::class, 'store'])->name('storeRepresentative');
    Route::get('/deleteRepresentative/{id}', [RepresentativeController::class, 'destroy'])->name('deleteRepresentative');
    Route::get('/getRepresentative/{id}', [RepresentativeController::class, 'edit'])->name('getRepresentative');
    Route::get('/getRepresentativeClients/{id}', [RepresentativeController::class, 'show'])->name('getRepresentativeClients');
    Route::post('connect_to_client', [RepresentativeController::class, 'connect_to_client'])->name('connect_to_client');
    Route::get('disconnectClientRep/{id}', [RepresentativeController::class, 'disconnectClientRep'])->name('disconnectClientRep');
    Route::post('/representatives/{representative}/documents', [RepresentativeController::class, 'storeDocument'])->name('representatives.documents.store');
    Route::delete('/representatives/documents/{document}', [RepresentativeController::class, 'deleteDocument'])->name('representatives.documents.delete');

    Route::get('/products', [ProductController::class, 'index'])->name('products');
    Route::get('/products/create', [ProductController::class, 'create'])->name('createProduct');
    Route::post('products/create', [ProductController::class, 'store'])->name('storeProduct');
    Route::get('/products/edit/{id}', [ProductController::class, 'edit'])->name('editProduct');
    Route::post('/products/update/{id}', [ProductController::class, 'update'])->name('updateProduct');
    Route::post('/products/delete', [ProductController::class, 'delete'])->name('product.delete');
    Route::get('/products/generate-barcode', [ProductController::class, 'generateBarcode'])->name('products.generate_barcode');
    Route::get('/getProduct/{code}', [ProductController::class, 'getProduct'])->name('getProduct');
    Route::get('/get-product-warehouse/{warehouse_id}/{code}', [ProductController::class, 'get_product_warehouse'])->name('get.product.warehouse');
    Route::get('/get-product-batches/{warehouse_id}/{product_id}', [ProductController::class, 'getProductBatches'])->name('get.product.batches');
    Route::get('/products/{id}/locations', [ProductController::class, 'locations'])->name('products.locations');
    Route::get('/products/print_barcode', [ProductController::class, 'print_barcode'])->name('print_barcode');
    Route::post('/products/print_barcode', [ProductController::class, 'do_print_barcode'])->name('preview_barcode');
    Route::get('/products/print_qr', [ProductController::class, 'print_qr'])->name('print_qr');
    Route::post('/products/print_qr', [ProductController::class, 'do_print_qr'])->name('preview_qr');

    Route::get('/salon/departments', [SalonDepartmentController::class, 'index'])->name('salon.departments');
    Route::post('/salon/departments', [SalonDepartmentController::class, 'store'])->name('salon.departments.store');
    Route::post('/salon/departments/{id}', [SalonDepartmentController::class, 'update'])->name('salon.departments.update');
    Route::delete('/salon/departments/{id}', [SalonDepartmentController::class, 'destroy'])->name('salon.departments.delete');

    Route::get('/salon/reservations', [SalonReservationController::class, 'index'])->name('salon.reservations');
    Route::post('/salon/reservations', [SalonReservationController::class, 'store'])->name('salon.reservations.store');
    Route::post('/salon/reservations/{id}', [SalonReservationController::class, 'update'])->name('salon.reservations.update');
    Route::delete('/salon/reservations/{id}', [SalonReservationController::class, 'destroy'])->name('salon.reservations.delete');

    // Quotations
    Route::resource('quotations', QuotationController::class);
    Route::post('quotations/{quotation}/convert', [QuotationController::class, 'convertToInvoice'])->name('quotations.convert');

    // Promotions
    Route::resource('promotions', PromotionController::class);

    // Warehouse transfers
    Route::resource('transfers', WarehouseTransferController::class)->except(['edit','update']);
    Route::post('transfers/{transfer}/approve', [WarehouseTransferController::class, 'approve'])->name('transfers.approve');
    Route::post('transfers/{transfer}/reject', [WarehouseTransferController::class, 'reject'])->name('transfers.reject');
    Route::post('transfers/{transfer}/damaged', [WarehouseTransferController::class, 'markDamaged'])->name('transfers.damaged');
    Route::get('reports/transfers', [ReportTransferController::class, 'index'])->name('reports.transfers');

    // Stock counts (inventory)
    Route::resource('stock_counts', StockCountController::class)->except(['edit','update','show']);
    Route::post('stock_counts/{stock_count}/approve', [StockCountController::class, 'approve'])->name('stock_counts.approve');

    // Fiscal years
    Route::get('fiscal_years', [FiscalYearController::class, 'index'])->name('fiscal_years.index');
    Route::post('fiscal_years', [FiscalYearController::class, 'store'])->name('fiscal_years.store');
    Route::post('fiscal_years/{fiscal_year}/close', [FiscalYearController::class, 'close'])->name('fiscal_years.close');
    Route::post('fiscal_years/{fiscal_year}/close-entries', [FiscalYearController::class, 'closeWithEntries'])->name('fiscal_years.close_entries');
    Route::post('fiscal_years/{fiscal_year}/open', [FiscalYearController::class, 'open'])->name('fiscal_years.open');

    // Financial reports
    Route::get('reports/trial-balance', [FinancialReportController::class, 'trialBalance'])->name('reports.trial_balance');
    Route::get('reports/general-ledger', [FinancialReportController::class, 'generalLedger'])->name('reports.general_ledger');
    Route::get('reports/account-balances', [FinancialReportController::class, 'accountBalances'])->name('reports.account_balances');
    Route::get('reports/income-statement', [FinancialStatementController::class, 'incomeStatement'])->name('reports.income_statement');
    Route::get('reports/income-statement-totals', [FinancialStatementController::class, 'incomeStatementTotals'])->name('reports.income_statement_totals');
    Route::get('reports/trading-account', [FinancialStatementController::class, 'tradingAccount'])->name('reports.trading_account');
    Route::get('reports/profit-loss', [FinancialStatementController::class, 'profitAndLoss'])->name('reports.profit_loss');
    Route::get('reports/balance-sheet', [FinancialStatementController::class, 'balanceSheet'])->name('reports.balance_sheet');

    Route::get('/update_qnt', [UpdateQuntityController::class, 'index'])->name('update_qnt');
    Route::get('/add_update_qnt', [UpdateQuntityController::class, 'create'])->name('add_update_qnt');
    Route::post('/store_update_qnt', [UpdateQuntityController::class, 'store'])->name('store_update_qnt');
    Route::get('/deleteUpdate_qnt/{id}', [UpdateQuntityController::class, 'destroy'])->name('deleteUpdate_qnt');
    Route::get('/edit_Update_qnt/{id}', [UpdateQuntityController::class, 'edit'])->name('edit_Update_qnt');
    Route::post('/update_update_qnt/{id}', [UpdateQuntityController::class, 'update'])->name('update_update_qnt');
    Route::get('/getUpdateQntBillNo', [UpdateQuntityController::class, 'getUpdateQntBillNo'])->name('getUpdateQntBillNo');

    Route::get('/sales', [SalesController::class, 'index'])->name('sales');
    Route::get('/sales/add', [SalesController::class, 'create'])->name('add_sale');
    Route::post('/sales/add', [SalesController::class, 'store'])->name('store_sale');
    Route::get('/getLastSalesBill', [SalesController::class, 'getLastSalesBill'])->name('getLastSalesBill');
    Route::get('/get-sales-number/{id}', [SalesController::class, 'get_sale_no'])->name('get.sale.no');
    //Route::get('/get-sales-pos-number/{id}', [SalesController::class, 'getNo'])->name('get.sale.pos.no');
    Route::get('/get-sales-pos-number/{type}/{id}', [SalesController::class, 'get_sales_pos_no'])->name('get.sale.pos.no');
    
    Route::get('/sales/return/create', [SalesController::class, 'sales_return_create'])->name('sales.return.create');
    Route::get('/sales/return', [SalesController::class, 'sales_return'])->name('sales.return');
    Route::get('/sales/return/{id}', [SalesController::class, 'returnSale'])->name('add_return');
    Route::post('/sales/return/{id}', [SalesController::class, 'storeReturn'])->name('store_return'); 
    Route::get('/get-sales-return-number/{type}/{id}', [SalesController::class, 'get_return_sales_pos_no'])->name('get.sale.return.no');
    Route::get('/preview_sales/{id}', [SalesController::class, 'show'])->name('preview_sales');
    Route::get('/print-sales/{id}', [SalesController::class, 'print'])->name('print.sales');
    Route::get('/get-sales/{id}', [SalesController::class, 'get_sales'])->name('get.sales');
    Route::get('/pos', [SalesController::class, 'pos'])->name('pos');
    Route::get('/print_last_pos', [SalesController::class, 'print_last_pos'])->name('print_last_pos');
    Route::get('/customers/{customer}/vehicles', [SalesController::class, 'customerVehicles'])->name('customers.vehicles');

    Route::get('/pos-product-list-img', [ProductController::class, 'pos_product_list_img'])->name('pos.product.list.img');
    Route::get('/get_product_list_img/{warehouse_id}', [ProductController::class, 'get_product_list_img'])->name('get_product_list_img');
    

    Route::get('/purchases', [PurchaseController::class, 'index'])->name('purchases');
    Route::get('/add_purchase', [PurchaseController::class, 'create'])->name('add_purchase');
    Route::post('/add_purchase', [PurchaseController::class, 'store'])->name('store_purchase');
   // Route::get('/get-purchase-number/{id}', [PurchaseController::class, 'getNo'])->name('get.purchase.number');
    Route::get('/get-purchase-number/{type}/{id}', [PurchaseController::class, 'get_purchases_no'])->name('get.purchase.number');
    Route::get('/preview_purchase/{id}', [PurchaseController::class, 'show'])->name('preview_purchase');
    Route::get('/purchase/return', [PurchaseController::class, 'purchase_return'])->name('purchase.return');
    Route::get('/return_purchase/{id}', [PurchaseController::class, 'edit'])->name('return_purchase');
    //Route::get('/get-return-purchase-number/{id}', [PurchaseController::class, 'getReturnNo'])->name('get.return.purchase.number');
    Route::get('/get-return-purchase-number/{type}/{id}', [PurchaseController::class, 'get_return_purchases_no'])->name('get.return.purchase.number');
    Route::post('/return_purchase/{id}', [PurchaseController::class, 'update'])->name('return_purchase_store');
    Route::get('/delete_purchase/{id}', [PurchaseController::class, 'destroy'])->name('delete_purchase');

    Route::get('/purchases/payments/{id}',[PaymentController::class,'getPurchasesPayments'])->name('purchases_payments');
    Route::get('/purchases/payments/add/{id}',[PaymentController::class,'addPurchasesPayment'])->name('add_purchases_payments');
    Route::post('/purchases/payments/add/{id}',[PaymentController::class,'storePurchasesPayment'])->name('store_purchases_payments');
    Route::get('/purchases/payments/delete/{id}',[PaymentController::class,'deletePurchasesPayment'])->name('delete_purchases_payments');

    // Reports: expiry / near-expiry items
    Route::get('/reports/expiry', [ReportController::class, 'expiryReport'])->name('reports.expiry');
    Route::post('/reports/expiry', [ReportController::class, 'expiryReport'])->name('reports.expiry.search');
    Route::get('/reports/quotations', [ReportController::class, 'quotationsReport'])->name('reports.quotations');
    Route::get('/reports/inventory-value', [ReportController::class, 'inventoryValueReport'])->name('reports.inventory_value');
    Route::get('/reports/inventory-aging', [ReportController::class, 'inventoryAgingReport'])->name('reports.inventory_aging');
    Route::get('/reports/inventory-variance', [ReportController::class, 'inventoryVarianceReport'])->name('reports.inventory_variance');
    Route::get('/reports/clients-balance', [ReportController::class, 'clientsBalanceReport'])->name('reports.clients_balance');
    Route::get('/reports/vendors-balance', [ReportController::class, 'vendorsBalanceReport'])->name('reports.vendors_balance');
    Route::get('/reports/clients-movement', [ReportController::class, 'clientsMovementReport'])->name('reports.clients_movement');
    Route::get('/reports/vendors-movement', [ReportController::class, 'vendorsMovementReport'])->name('reports.vendors_movement');
    Route::get('/reports/clients-aging', [ReportController::class, 'clientAging'])->name('reports.clients_aging');
    Route::get('/reports/representatives', [ReportController::class, 'representativesReport'])->name('reports.representatives');
    Route::get('/reports/salon-services', [ReportController::class, 'salonServicesReport'])->name('reports.salon.services');
    // Reports: low stock
    Route::get('/reports/low-stock', [ReportController::class, 'lowStockReport'])->name('reports.low_stock');
    Route::post('/reports/low-stock', [ReportController::class, 'lowStockReport'])->name('reports.low_stock.search');
    Route::get('/sales/payments/{id}',[PaymentController::class,'getSalesPayments'])->name('sales_payments');
    Route::get('/sales/payments/show/{remain}',[PaymentController::class,'showSalePayment'])->name('show_sales_payments');
    Route::get('/sales/payments/add/{id}',[PaymentController::class,'addSalePayment'])->name('add_sales_payments');
    Route::post('/sales/payments/add/{id}',[PaymentController::class,'storeSalePayment'])->name('store_sales_payments');
    Route::post('/sales-pos/payments/add/{id}',[PaymentController::class,'MakeSalePayment'])->name('store.sales.pos.payments');
    Route::get('/sales/payments/delete/{id}',[PaymentController::class,'deleteSalePayment'])->name('delete_sales_payments');
    Route::get('/money-entry-list', [PaymentController::class, 'money_entry_list'])->name('money.entry.list');
    Route::get('/money-exit-list', [PaymentController::class, 'money_exit_list'])->name('money.exit.list');

    Route::get('/incoming_list',[JournalController::class,'incoming_list'])->name('incoming_list');
    Route::post('/search_incoming_list', [JournalController::class, 'search_incoming_list'])->name('search_incoming_list');
    Route::get('/incoming_list_new',[JournalController::class, 'incoming_list_new'])->name('incoming_list_new');
    Route::post('/search_incoming_list_new', [JournalController::class, 'search_incoming_list_new'])->name('search_incoming_list_new');
    
    Route::get('/balance_sheet', [JournalController::class, 'balance_sheet'])->name('balance_sheet');
    Route::post('/search_balance_sheet', [JournalController::class, 'search_balance_sheet'])->name('search_balance_sheet');

    Route::get('/accounts',[AccountsTreeController::class,'index'])->name('accounts_list');
    Route::get('/accounts/create',[AccountsTreeController::class,'create'])->name('create_account');
    Route::post('/accounts/create',[AccountsTreeController::class,'store'])->name('store_account');
    Route::get('/accounts/get_level/{parent}',[AccountsTreeController::class,'getLevel'])->name('get_account_level');
    Route::get('/accounts/edit/{id}',[AccountsTreeController::class,'edit'])->name('edit_account');
    Route::post('/accounts/edit/{id}',[AccountsTreeController::class,'update'])->name('update_account');
    Route::get('/accounts/delete/{id}',[AccountsTreeController::class,'destroy'])->name('delete_account');
    Route::get('manual_number', [JournalController::class, 'manual_number'])->name('manual_number');
    
    Route::get('/account_settings',[AccountSettingController::class,'index'])->name('account_settings_list');
    Route::get('/account_settings/create',[AccountSettingController::class,'create'])->name('create_account_settings');
    Route::post('/account_settings/create',[AccountSettingController::class,'store'])->name('store_account_settings');
    Route::get('/account_settings/edit/{id}',[AccountSettingController::class,'edit'])->name('edit_account_settings');
    Route::post('/account_settings/edit/{id}',[AccountSettingController::class,'update'])->name('update_account_settings');
    Route::get('/account_settings/delete/{id}',[AccountSettingController::class,'destroy'])->name('delete_account_settings');
    Route::get('/accounts/journals/{type}',[AccountsTreeController::class,'journals'])->name('journals');
    Route::get('/accounts/journals/preview/{id}',[AccountsTreeController::class,'previewJournal'])->name('preview_journal');
    Route::post('/accounts/journals_search', [AccountsTreeController::class, 'journals_search'])->name('journals_search');
    Route::get('/reports/vendor-aging',[ReportController::class,'vendorAging'])->name('reports.vendor_aging');

    Route::get('/accounts/manual',[JournalController::class,'create'])->name('manual_journal');
    Route::post('/accounts/manual',[JournalController::class,'store'])->name('store_manual');
    Route::get('/accounts/opening-balances', [OpeningBalanceController::class, 'index'])->name('opening_balances.index');
    Route::post('/accounts/opening-balances', [OpeningBalanceController::class, 'store'])->name('opening_balances.store');
    Route::get('/accounts/opening-balances/accounts/{code}', [OpeningBalanceController::class, 'searchAccounts'])->name('opening_balances.accounts');
    Route::get('/getAccounts/{code}', [AccountsTreeController::class, 'getAccount'])->name('getAccounts');
    Route::get('/journals/delete/{id}',[JournalController::class,'delete'])->name('delete_journal');
    
    Route::get('/expenses', [ExpensesController::class, 'index'])->name('expenses'); 
    Route::post('/storeExpense', [ExpensesController::class, 'store'])->name('storeExpense');
    Route::get('/getExpense/{id}', [ExpensesController::class, 'show'])->name('getExpense');
    Route::get('/printExpense/{id}', [ExpensesController::class, 'print'])->name('printExpense');
    Route::get('/get-expenses-no/{branch_id}', [ExpensesController::class, 'get_expense_no'])->name('get.expenses.no');
    Route::get('/expenses_type_destroy/{id}', [ExpenseTypeController::class, 'destroy'])->name('expenses_type_destroy');

    Route::get('/catches', [CatchReciptController::class, 'index'])->name('catches'); 
    Route::post('/storeCatch', [CatchReciptController::class, 'store'])->name('storeCatch');
    Route::get('/getCatch/{id}', [CatchReciptController::class, 'show'])->name('getCatch');
    Route::get('/getSupplierAccount/{id}', [CatchReciptController::class, 'getSupplierAccount'])->name('getSupplierAccount');
    Route::get('/catche_destroy/{id}', [CatchReciptController::class, 'destroy'])->name('catche_destroy');
    Route::get('/printCatch/{id}', [CatchReciptController::class, 'print'])->name('printCatch');
    Route::get('/get-catch-recipt-no/{branch_id}', [CatchReciptController::class, 'get_Catch_no'])->name('get.catch.recipt.no');
    
    /*
    Route::get('/box_expenses_list', [ExpensesController::class, 'index'])->name('box_expenses_list');
    Route::get('/create_expenses', [ExpensesController::class, 'create'])->name('create_expenses');
    Route::post('/box_expenses_store', [ExpensesController::class, 'store'])->name('box_expenses_store');
    Route::get('/view_expenses/{id}', [ExpensesController::class, 'show'])->name('view_expenses');
    */

    Route::get('/init',[InitializeController::class,'getIntialize'])->name('init');
    Route::get('/subscribe_data',[InitializeController::class,'subscribeData'])->name('subscribe_data');
    Route::post('/init',[InitializeController::class,'storeInitialize'])->name('store_init');

    Route::get('/settings',[SystemSettingsController::class,'settings'])->name('settings');

    Route::get('/employer-categories',[EmployerCategoryController::class,'index'])->name('employer.categories.index');
    Route::get('/create-employer-category',[EmployerCategoryController::class,'create'])->name('employer.categories.create');
    Route::post('/create-employer-category',[EmployerCategoryController::class,'store'])->name('employer.categories.store');
    Route::get('/update-employer-category/{employerCategory}',[EmployerCategoryController::class,'edit'])->name('employer.categories.edit');
    Route::post('/update-employer-category/{employerCategory}',[EmployerCategoryController::class,'update'])->name('employer.categories.update');
    Route::get('/delete-employer-category/{employerCategory}',[EmployerCategoryController::class,'destroy'])->name('employer.categories.delete');

    Route::get('/employers',[EmployerController::class,'index'])->name('employers.index');
    Route::get('/create-employer',[EmployerController::class,'create'])->name('employers.create');
    Route::post('/create-employer',[EmployerController::class,'store'])->name('employers.store');
    Route::get('/update-employer/{employer}',[EmployerController::class,'edit'])->name('employers.edit');
    Route::post('/update-employer/{employer}',[EmployerController::class,'update'])->name('employers.update');
    Route::get('/delete-employer/{employer}',[EmployerController::class,'destroy'])->name('employers.delete');

    Route::resource('deduction',DeductionController::class);
    Route::resource('reward',RewardController::class);
    Route::resource('advance_payments',AdvancePaymentController::class);

    Route::get('/salary',[SalaryDocController::class,'index'])->name('salary_docs');
    Route::get('/open_salary',[SalaryDocController::class,'openSalaryDoc'])->name('open_salary');
    Route::post('/get_salary',[SalaryDocController::class,'getSalaryDoc'])->name('get_salary');
    Route::post('/store_salary',[SalaryDocController::class,'storeSalary'])->name('store_salary');

    Route::get('/companyInfo', [CompanyInfoController::class, 'index'])->name('companyInfo');
    Route::post('/storeCompanyInfo', [CompanyInfoController::class, 'store'])->name('storeCompanyInfo');

      
    Route::get('/daily_sales_report', [ReportController::class, 'daily_sales_report'])->name('daily_sales_report');
    Route::get('/daily-sales-report-search/{date}/{warehouse}/{branch_id}/{customer_id?}/{vehicle_plate?}/{cost_center_id?}', [ReportController::class, 'daily_sales_report_search'])
        ->name('daily.sales.report.search');

    Route::get('/sales_item_report', [ReportController::class, 'sales_item_report'])->name('sales_item_report');
    Route::get('/sales_item_report_search/{fdate}/{tdate}/{warehouse}/{branch_id}/{item}/{supplier}/{vehicle_plate?}/{cost_center_id?}', [ReportController::class, 'sales_item_report_search'])
        ->name('sales.item.report.search');

    Route::get('/sales_return_report', [ReportController::class, 'sales_return_report'])->name('sales.return.report');
    Route::get('/sales-return-report-search/{fdate}/{tdate}/{warehouse}/{bill_no}/{vendor}/{branch_id}/{cost_center_id?}', [ReportController::class, 'sales_return_report_search'])
        ->name('sales.return.report.search');

    Route::get('/purchase_report', [ReportController::class, 'purchase_report'])->name('purchase_report');
    Route::get('/purchase-report-search/{fdate}/{tdate}/{warehouse}/{bill_no}/{vendor}/{branch_id}/{cost_center_id?}', [ReportController::class, 'purchase_report_search'])
        ->name('purchase.report.search');

    Route::get('/purchases_return_report', [ReportController::class, 'purchases_return_report'])->name('purchases_return_report');
    Route::get('/purchases-return-report-search/{fdate}/{tdate}/{warehouse}/{bill_no}/{vendor}/{branch_id}/{cost_center_id?}', [ReportController::class, 'purchases_return_report_search'])
        ->name('purchases.return.report.search');

    Route::get('/items_report', [ReportController::class, 'items_report'])->name('items_report');
    Route::get('/items-report-search/{category}/{brand}/{warehouse}/{branch_id}', [ReportController::class, 'items_report_search'])->name('items.report.search');

    Route::get('/items_limit_report', [ReportController::class, 'items_limit_report'])->name('items_limit_report');
    Route::get('/items-limit-report-search/{category}/{brand}/{warehouse}/{branch_id}', [ReportController::class, 'items_limit_report_search'])->name('items.limit.report.search');

    Route::get('/items_no_balance_report', [ReportController::class, 'items_no_balance_report'])->name('items_no_balance_report');
    Route::get('/items-no-balance-report-search/{category}/{brand}/{warehouse}/{branch_id}', [ReportController::class, 'items_no_balance_report_search'])
        ->name('items.no.balance.report.search');

    Route::get('/items_stock_report', [ReportController::class, 'items_stock_report'])->name('items_stock_report');
    Route::get('/items-stock-report-search/{fdate}/{tdate}/{warehouse}/{branch_id}/{item}', [ReportController::class, 'items_stock_report_search'])
        ->name('items.stock.report.search');

    Route::get('/items_purchased_report', [ReportController::class, 'items_purchased_report'])->name('items_purchased_report');
    Route::get('/items-purchased-report-search/{fdate}/{tdate}/{warehouse}/{branch_id}/{item}/{supplier}', [ReportController::class, 'items_purchased_report_search'])
        ->name('items.purchased.report.search');

    Route::get('/reports/clients-status', [CompanyStatusReportController::class, 'clients'])->name('reports.clients.status');
    Route::get('/reports/clients-status-search', [CompanyStatusReportController::class, 'clientsSearch'])->name('reports.clients.status.search');
    Route::get('/reports/vendors-status', [CompanyStatusReportController::class, 'vendors'])->name('reports.vendors.status');
    Route::get('/reports/vendors-status-search', [CompanyStatusReportController::class, 'vendorsSearch'])->name('reports.vendors.status.search');
    Route::get('/client_balance_report/{id}/{slag}',[ReportController::class,'client_balance_report'])->name('client_balance_report');
    
    Route::get('/account_movement_report', [ReportAccountController::class, 'account_movement_report'])->name('account_movement_report');
    Route::post('/account_movement_report', [ReportAccountController::class, 'account_movement_report_search'])->name('account_movement_report_search');
    Route::get('/account_company_report_search/{id}', [ReportAccountController::class, 'account_company_report_search'])->name('account.company.report.search');
    
    Route::get('/account_companies_details_report', [ReportAccountController::class, 'account_companies_details_report'])->name('account_companies_details_report');
    Route::post('/account_companies_details_report', [ReportAccountController::class, 'account_companies_details_search'])->name('account_companies_details_search');
    
    Route::get('/reports/account_balance', [ReportAccountController::class, 'account_balance'])->name('account_balance');
    Route::post('/reports/account_balance', [ReportAccountController::class, 'account_balance_search'])->name('search_account_balance');

    Route::get('/reports/tax-declaration', [ReportAccountController::class, 'tax_declaration'])->name('tax.declaration');
    Route::post('/reports/tax-declaration-result', [ReportAccountController::class, 'tax_declaration_report_search'])->name('tax.declaration.result');

    Route::get('/reports/inventory/{id}', [ReportAccountController::class, 'inventory_report'])->name('inventory.report');
    
    //Route::get('/reports/account_balance',[ReportController::class,'account_balance'])->name('account_balance');
    //Route::post('/reports/account_balance',[ReportController::class,'account_balance'])->name('search_account_balance');
  
});
});
