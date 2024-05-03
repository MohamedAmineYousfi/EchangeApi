<?php

use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::namespace('Api\V1\Auth')
    ->middleware('set-locale')
    ->prefix('api/v1')
    ->middleware('json.api')
    ->group(function () {
        Route::post('/login', 'LoginController');
        Route::post('/register', 'RegisterController');
        Route::post('/logout', 'LogoutController')->middleware('auth:api');
        Route::post('/password-forgot', 'ForgotPasswordController');
        Route::post('/verify', 'VerificationController')->middleware(['auth:api']);
        Route::post('/password-reset', 'ResetPasswordController');
    });

Route::namespace('Api\V1\AppConfig')
    ->middleware('set-locale')
    ->prefix('api/v1')
    ->middleware('json.api')
    ->group(function () {
        Route::get('/app-config', 'GetAppConfigController');
    });

Route::middleware('set-locale')
    ->prefix('api/v1')
    ->middleware('check-active')
    ->middleware('auth:api')
    ->group(function () {
        Route::post('/uploads/{resource}/{id}/{field}', 'Api\V1\File\UploadController@upload')->where('path', '.*');
    });

JsonApi::register('v1')
    ->routes(function ($api) {
        $api->get('get-properties', 'Api\V1\PublicWebsite\PublicWebsiteController@getProperties')->name('public-get-properties');
        $api->get('get-property/{property}', 'Api\V1\PublicWebsite\PublicWebsiteController@getProperty')->name('public-get-property');
        $api->get('get-auctions', 'Api\V1\PublicWebsite\PublicWebsiteController@getAuctions')->name('public-get-auctions');
        $api->get('get-active-auctions', 'Api\V1\PublicWebsite\PublicWebsiteController@getActiveAuctions')->name('public-get-active-auctions');
        $api->get('get-organizations', 'Api\V1\PublicWebsite\PublicWebsiteController@getOrganizations')->name('public-get-organizations');
        $api->resource('contact-forms')->only('create')->routes(function ($posts) {});
    });

JsonApi::register('v1')
    ->middleware('set-locale')
    ->middleware('auth:api')
    ->middleware('check-active')
    ->middleware('two-factor')
    ->routes(function ($api) {
        $api->get('me', 'Api\V1\MeController@readProfile')->name('readProfile');
        $api->patch('me', 'Api\V1\MeController@updateProfile')->name('updateProfile');
        $api->post('me/toggle-two-fa', 'Api\V1\User\Toggle2FAController@toggle2FA')->name('toggle2FA');

        $api->resource('users')->routes(function ($posts) {
            $posts->get('{record}/activate', 'Api\V1\User\ActivateUserController@activate');
            $posts->get('{record}/deactivate', 'Api\V1\User\DeactivateUserController@deactivate');
        });

        $api->resource('roles')->routes(function ($posts) {
        });
        $api->resource('permissions')->only('index')->routes(function ($posts) {
        });
        $api->resource('organizations')->routes(function ($posts) {
            $posts->post('{record}/generate_subscription_invoice', 'Api\V1\Organization\GenerateSubscriptionInvoiceController@generate');
        });
        $api->resource('resellers')->routes(function ($posts) {
        });
        $api->resource('reseller-products')->routes(function ($posts) {
        });
        $api->resource('reseller-services')->routes(function ($posts) {
        });
        $api->resource('packages')->routes(function ($posts) {
        });
        $api->resource('reseller-invoices', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\ResellerInvoice\ValidateResellerInvoiceController@validate');
            $posts->get('{record}/cancel', 'Api\V1\ResellerInvoice\CancelResellerInvoiceController@cancel');
        });
        $api->resource('reseller-invoice-items')->routes(function ($posts) {
        });
        $api->resource('customers', [])->routes(function ($posts) {
        });
        $api->resource('logs')->only('index', 'read');
        $api->resource('subscriptions')->routes(function ($posts) {
            $posts->get('{record}/renew', 'Api\V1\Subscription\RenewSubscriptionController@renew');
        });
        $api->resource('reseller-payments', [])->routes(function ($posts) {
        });
        $api->resource('tags')->routes(function ($posts) {
        });
        $api->resource('categories')->routes(function ($posts) {
        });
        $api->resource('properties')->routes(function ($posts) {
            $posts->get('exports', 'Api\V1\Property\PropertiesExportController@exports');
            $posts->get('{record}/activate', 'Api\V1\Property\ActivatePropertyController@activate');
            $posts->post('{record}/transactions', 'Api\V1\Property\TransactionPropertyController@update');
            $posts->delete('{record}/transactions', 'Api\V1\Property\TransactionPropertyController@destroy');
            $posts->get('{record}/deactivate', 'Api\V1\Property\DeactivatePropertyController@deactivate');
            $posts->get('{record}/print-transaction-receipt', 'Api\V1\Property\PropertyPrintController@propertyTransactionPrintInvoice');
            $posts->get('export-thumbnails', 'Api\V1\Property\PropertyPrintController@downloadThumbnails');
            $posts->post('export-thumbnails-for-selection', 'Api\V1\Property\PropertyPrintController@downloadThumbnailsForSelection');
            $posts->post('print-letters', 'Api\V1\Property\PropertyPrintController@downloadLetters');
            $posts->post('print-letters-for-selection', 'Api\V1\Property\PropertyPrintController@downloadLettersForSelection');
        });
        $api->resource('auctions')->routes(function ($posts) {
            $posts->post('{record}/reload-fees', 'Api\V1\Auction\ReloadAuctionFeesController@reloadFees');
        });
        $api->resource('auction-fees')->routes(function ($posts) {
        });
        $api->resource('bids')->routes(function ($posts) {
        });
        $api->resource('bid-steps')->routes(function ($posts) {
        });
        $api->resource('contacts')->routes(function ($posts) {
        });
        $api->resource('suppliers', [])->routes(function ($posts) {
        });
        $api->resource('locations', [])->routes(function ($posts) {
            $posts->post('attach-users/{record}', 'Api\V1\Location\UserAddToLocationController@attachUserToLocation');
            $posts->post('detach-users/{record}', 'Api\V1\Location\UserAddToLocationController@detachUserToLocation');
        });
        $api->resource('files', [])->routes(function ($posts) {
        });
        $api->resource('folders', [])->routes(function ($posts) {
        });
        $api->resource('notifications')->only('index', 'read')->routes(function ($posts) {
            $posts->post('mark-as-read', 'Api\V1\Notification\MarkAsReadController@markAsRead');
            $posts->post('mark-as-unread', 'Api\V1\Notification\MarkAsUnReadController@markAsUnRead');
        });
        $api->resource('products')->routes(function ($posts) {
        });
        $api->resource('notification-subscriptions')->only('index', 'read', 'create', 'delete');
        $api->resource('warehouses', [])->routes(function ($posts) {
            $posts->get('{record}/apply-configurations', "Api\V1\Warehouse\ApplyConfigurationsController@applyConfigurations");
            $posts->delete('{record}/detach', "Api\V1\Warehouse\DetachWarehouseFromUsedByListController@detachWarehouse");
        });
        $api->resource('warehouse-products', [])->routes(function ($posts) {
        });
        $api->resource('purchases-orders', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\PurchasesOrder\ValidatePurchasesOrderController@validate');
            $posts->get('{record}/cancel', 'Api\V1\PurchasesOrder\CancelPurchasesOrderController@cancel');
            $posts->post('{record}/generate-invoice', 'Api\V1\PurchasesOrder\GenerateInvoiceController@generate');
            $posts->post('{record}/generate-delivery', 'Api\V1\PurchasesOrder\GenerateDeliveryController@generate');
            $posts->get('{record}/print', 'Api\V1\PurchasesOrder\PurchaseOrderPrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\PurchasesOrder\PurchaseOrderPrintController@sendMail');
        });
        $api->resource('purchases-order-items')->routes(function ($posts) {
        });
        $api->resource('purchases-invoices', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\PurchasesInvoice\ValidatePurchasesInvoiceController@validate');
            $posts->get('{record}/cancel', 'Api\V1\PurchasesInvoice\CancelPurchasesInvoiceController@cancel');
            $posts->get('{record}/print', 'Api\V1\PurchasesInvoice\PurchaseInvoicePrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\PurchasesInvoice\PurchaseInvoicePrintController@sendMail');
            $posts->get('{record}/payments/print', 'Api\V1\PurchasesInvoice\PurchasesInvoicePaymentPrintController@download');
            $posts->post('{record}/payments/send-mail', 'Api\V1\PurchasesInvoice\PurchasesInvoicePaymentPrintController@sendMail');
        });
        $api->resource('purchases-invoice-items')->routes(function ($posts) {
        });
        $api->resource('purchases-deliveries', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\PurchasesDelivery\ValidatePurchasesDeliveryController@validate');
            $posts->get('{record}/cancel', 'Api\V1\PurchasesDelivery\CancelPurchasesDeliveryController@cancel');
            $posts->get('{record}/print', 'Api\V1\PurchasesDelivery\PurchasesDeliveryPrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\PurchasesDelivery\PurchasesDeliveryPrintController@sendMail');
        });
        $api->resource('purchases-delivery-items')->routes(function ($posts) {
        });
        $api->resource('purchases-payments', [])->routes(function ($posts) {
        });
        $api->resource('sales-orders', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\SalesOrder\ValidateSalesOrderController@validate');
            $posts->get('{record}/cancel', 'Api\V1\SalesOrder\CancelSalesOrderController@cancel');
            $posts->post('{record}/generate-invoice', 'Api\V1\SalesOrder\GenerateInvoiceController@generate');
            $posts->post('{record}/generate-delivery', 'Api\V1\SalesOrder\GenerateDeliveryController@generate');
            $posts->get('{record}/print', 'Api\V1\SalesOrder\SalesOrderPrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\SalesOrder\SalesOrderPrintController@sendMail');
        });
        $api->resource('sales-order-items')->routes(function ($posts) {
        });
        $api->resource('sales-invoices', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\SalesInvoice\ValidateSalesInvoiceController@validate');
            $posts->get('{record}/cancel', 'Api\V1\SalesInvoice\CancelSalesInvoiceController@cancel');
            $posts->get('{record}/print', 'Api\V1\SalesInvoice\SalesInvoicePrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\SalesInvoice\SalesInvoicePrintController@sendMail');
            $posts->post('{record}/payments/send-mail', 'Api\V1\SalesInvoice\SalesInvoicePaymentPrintController@sendMail');
            $posts->get('{record}/payments/print', 'Api\V1\SalesInvoice\SalesInvoicePaymentPrintController@download');
        });
        $api->resource('sales-invoice-items')->routes(function ($posts) {
        });
        $api->resource('sales-deliveries', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\SalesDelivery\ValidateSalesDeliveryController@validate');
            $posts->get('{record}/cancel', 'Api\V1\SalesDelivery\CancelSalesDeliveryController@cancel');
            $posts->get('{record}/print', 'Api\V1\SalesDelivery\SalesDeliveryPrintController@download');
            $posts->post('{record}/send-mail', 'Api\V1\SalesDelivery\SalesDeliveryPrintController@sendMail');
        });
        $api->resource('sales-delivery-items')->routes(function ($posts) {
        });
        $api->resource('sales-payments', [])->routes(function ($posts) {
        });
        $api->resource('supplier-products', [])->routes(function ($posts) {
        });
        $api->resource('stock-movements', [])->routes(function ($posts) {
            $posts->get('{record}/validate', 'Api\V1\StockMovement\ValidateStockMovementController@validate');
            $posts->get('{record}/cancel', 'Api\V1\StockMovement\CancelStockMovementController@cancel');
        });
        $api->resource('stock-movement-items', [])->routes(function ($posts) {
            $posts->post('bulk/create', 'Api\V1\StockMovementItem\BulkCreateController@bulkCreate');
            $posts->post('bulk/update', 'Api\V1\StockMovementItem\BulkUpdateController@bulkUpdate');
            $posts->post('bulk/delete', 'Api\V1\StockMovementItem\BulkDeleteController@bulkDelete');
        });

        $api->resource('imports', [])->routes(function ($posts) {
            $posts->get('/models', "Api\V1\Import\ImportController@getModelInfoList");
            $posts->get('{record}/run-dry', "Api\V1\Import\RunDryController@runDry");
            $posts->get('{record}/run', "Api\V1\Import\RunController@run");
            $posts->get('{record}/product-synchronize', "Api\V1\Import\RunProductSynchronizeController@synchronize");
            $posts->get('{record}/product-dry-synchronize', "Api\V1\Import\RunDryProductSynchronizeController@drySynchronize");
        });

        $api->resource('options', [])->only('index', 'read', 'update')->routes(function ($posts) {
        });
        $api->resource('taxes', [])->routes(function ($posts) {
        });
        $api->resource('tax-groups', [])->routes(function ($posts) {
        });
        $api->resource('unit-of-measures', [])->routes(function ($posts) {
        });
        $api->resource('unit-of-measure-units', [])->routes(function ($posts) {
        });
    });
