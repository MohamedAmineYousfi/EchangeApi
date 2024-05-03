<?php

namespace App\Providers;

use App\Models\Auction;
use App\Models\Category;
use App\Models\Contact;
use App\Models\Customer;
use App\Models\File;
use App\Models\Folder;
use App\Models\Import;
use App\Models\Location;
use App\Models\Log;
use App\Models\Notification;
use App\Models\NotificationSubscription;
use App\Models\Option;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Permission;
use App\Models\Product;
use App\Models\Property;
use App\Models\PropertyTransaction;
use App\Models\PurchasesDelivery;
use App\Models\PurchasesDeliveryItem;
use App\Models\PurchasesInvoice;
use App\Models\PurchasesInvoiceItem;
use App\Models\PurchasesOrder;
use App\Models\PurchasesOrderItem;
use App\Models\PurchasesPayment;
use App\Models\Reseller;
use App\Models\ResellerInvoice;
use App\Models\ResellerInvoiceItem;
use App\Models\ResellerPayment;
use App\Models\ResellerProduct;
use App\Models\ResellerService;
use App\Models\Role;
use App\Models\SalesDelivery;
use App\Models\SalesDeliveryItem;
use App\Models\SalesInvoice;
use App\Models\SalesInvoiceItem;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesPayment;
use App\Models\StockMovement;
use App\Models\StockMovementItem;
use App\Models\Subscription;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\Tag;
use App\Models\Tax;
use App\Models\TaxGroup;
use App\Models\UnitOfMeasure;
use App\Models\UnitOfMeasureUnit;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use App\Policies\AuctionPolicy;
use App\Policies\CategoryPolicy;
use App\Policies\ContactPolicy;
use App\Policies\CustomerPolicy;
use App\Policies\FilePolicy;
use App\Policies\FolderPolicy;
use App\Policies\ImportPolicy;
use App\Policies\LocationPolicy;
use App\Policies\LogPolicy;
use App\Policies\NotificationPolicy;
use App\Policies\NotificationSubscriptionPolicy;
use App\Policies\OptionPolicy;
use App\Policies\OrganizationPolicy;
use App\Policies\PackagePolicy;
use App\Policies\PermissionPolicy;
use App\Policies\ProductPolicy;
use App\Policies\PropertyPolicy;
use App\Policies\PropertyTransactionPolicy;
use App\Policies\PurchasesDeliveryItemPolicy;
use App\Policies\PurchasesDeliveryPolicy;
use App\Policies\PurchasesInvoiceItemPolicy;
use App\Policies\PurchasesInvoicePolicy;
use App\Policies\PurchasesOrderItemPolicy;
use App\Policies\PurchasesOrderPolicy;
use App\Policies\PurchasesPaymentPolicy;
use App\Policies\ResellerInvoiceItemPolicy;
use App\Policies\ResellerInvoicePolicy;
use App\Policies\ResellerPaymentPolicy;
use App\Policies\ResellerPolicy;
use App\Policies\ResellerProductPolicy;
use App\Policies\ResellerServicePolicy;
use App\Policies\RolePolicy;
use App\Policies\SalesDeliveryItemPolicy;
use App\Policies\SalesDeliveryPolicy;
use App\Policies\SalesInvoiceItemPolicy;
use App\Policies\SalesInvoicePolicy;
use App\Policies\SalesOrderItemPolicy;
use App\Policies\SalesOrderPolicy;
use App\Policies\SalesPaymentPolicy;
use App\Policies\StockMovementItemPolicy;
use App\Policies\StockMovementPolicy;
use App\Policies\SubscriptionPolicy;
use App\Policies\SupplierPolicy;
use App\Policies\SupplierProductPolicy;
use App\Policies\TagPolicy;
use App\Policies\TaxGroupsPolicy;
use App\Policies\TaxPolicy;
use App\Policies\UnitOfMeasurePolicy;
use App\Policies\UnitOfMeasureUnitPolicy;
use App\Policies\UserPolicy;
use App\Policies\WarehousePolicy;
use App\Policies\WarehouseProductPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Role::class => RolePolicy::class,
        Permission::class => PermissionPolicy::class,
        Organization::class => OrganizationPolicy::class,
        Reseller::class => ResellerPolicy::class,
        ResellerProduct::class => ResellerProductPolicy::class,
        ResellerService::class => ResellerServicePolicy::class,
        ResellerInvoice::class => ResellerInvoicePolicy::class,
        ResellerInvoiceItem::class => ResellerInvoiceItemPolicy::class,
        Customer::class => CustomerPolicy::class,
        Log::class => LogPolicy::class,
        Package::class => PackagePolicy::class,
        Subscription::class => SubscriptionPolicy::class,
        ResellerPayment::class => ResellerPaymentPolicy::class,
        Contact::class => ContactPolicy::class,
        Supplier::class => SupplierPolicy::class,
        Tag::class => TagPolicy::class,
        Location::class => LocationPolicy::class,
        Folder::class => FolderPolicy::class,
        File::class => FilePolicy::class,
        Notification::class => NotificationPolicy::class,
        NotificationSubscription::class => NotificationSubscriptionPolicy::class,
        Warehouse::class => WarehousePolicy::class,
        WarehouseProduct::class => WarehouseProductPolicy::class,
        Product::class => ProductPolicy::class,
        PurchasesOrder::class => PurchasesOrderPolicy::class,
        PurchasesOrderItem::class => PurchasesOrderItemPolicy::class,
        PurchasesInvoice::class => PurchasesInvoicePolicy::class,
        PurchasesInvoiceItem::class => PurchasesInvoiceItemPolicy::class,
        PurchasesDelivery::class => PurchasesDeliveryPolicy::class,
        PurchasesDeliveryItem::class => PurchasesDeliveryItemPolicy::class,
        PurchasesPayment::class => PurchasesPaymentPolicy::class,
        SalesOrder::class => SalesOrderPolicy::class,
        SalesOrderItem::class => SalesOrderItemPolicy::class,
        SalesInvoice::class => SalesInvoicePolicy::class,
        SalesInvoiceItem::class => SalesInvoiceItemPolicy::class,
        SalesDelivery::class => SalesDeliveryPolicy::class,
        SalesDeliveryItem::class => SalesDeliveryItemPolicy::class,
        SalesPayment::class => SalesPaymentPolicy::class,
        SupplierProduct::class => SupplierProductPolicy::class,
        StockMovement::class => StockMovementPolicy::class,
        StockMovementItem::class => StockMovementItemPolicy::class,
        Import::class => ImportPolicy::class,
        Category::class => CategoryPolicy::class,
        Property::class => PropertyPolicy::class,
        Auction::class => AuctionPolicy::class,
        Tax::class => TaxPolicy::class,
        TaxGroup::class => TaxGroupsPolicy::class,
        Option::class => OptionPolicy::class,
        UnitOfMeasure::class => UnitOfMeasurePolicy::class,
        UnitOfMeasureUnit::class => UnitOfMeasureUnitPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();
    }
}
