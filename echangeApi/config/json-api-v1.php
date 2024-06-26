<?php

use App\Models\Auction;
use App\Models\AuctionFee;
use App\Models\BidStep;
use App\Models\Category;
use App\Models\Contact;
use App\Models\ContactForm;
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

return [

    /*
    |--------------------------------------------------------------------------
    | Resolver
    |--------------------------------------------------------------------------
    |
    | The API's resolver is the class that works out the fully qualified
    | class name of adapters, schemas, authorizers and validators for your
    | resource types. We recommend using our default implementation but you
    | can override it here if desired.
    */
    'resolver' => \CloudCreativity\LaravelJsonApi\Resolver\ResolverFactory::class,

    /*
    |--------------------------------------------------------------------------
    | Root Namespace
    |--------------------------------------------------------------------------
    |
    | The root namespace for JSON API classes for this API. If `null`, the
    | namespace will default to `JsonApi` within your application's root
    | namespace (obtained via Laravel's `Application::getNamespace()`
    | method).
    |
    | The `by-resource` setting determines how your units are organised within
    | your root namespace.
    |
    | - true:
    |   - e.g. App\JsonApi\Posts\{Adapter, Schema, Validators}
    |   - e.g. App\JsonApi\Comments\{Adapter, Schema, Validators}
    | - false:
    |   - e.g. App\JsonApi\Adapters\PostAdapter, CommentAdapter}
    |   - e.g. App\JsonApi\Schemas\{PostSchema, CommentSchema}
    |   - e.g. App\JsonApi\Validators\{PostValidator, CommentValidator}
    |
    */
    'namespace' => 'App\JsonApi\V1',
    'by-resource' => true,

    /*
    |--------------------------------------------------------------------------
    | Resources
    |--------------------------------------------------------------------------
    |
    | Here you map the list of JSON API resources in your API to the actual
    | record (model/entity) classes they relate to.
    |
    | For example, if you had a `posts` JSON API resource, that related to
    | an Eloquent model `App\Post`, your mapping would be:
    |
    | `'posts' => App\Post::class`
    */
    'resources' => [
        'profile' => User::class,
        'users' => User::class,
        'roles' => Role::class,
        'permissions' => Permission::class,
        'organizations' => Organization::class,
        'resellers' => Reseller::class,
        'reseller-products' => ResellerProduct::class,
        'reseller-services' => ResellerService::class,
        'packages' => Package::class,
        'reseller-invoices' => ResellerInvoice::class,
        'reseller-invoice-items' => ResellerInvoiceItem::class,
        'customers' => Customer::class,
        'logs' => Log::class,
        'subscriptions' => Subscription::class,
        'reseller-payments' => ResellerPayment::class,
        'contacts' => Contact::class,
        'contact-forms' => ContactForm::class,
        'suppliers' => Supplier::class,
        'tags' => Tag::class,
        'locations' => Location::class,
        'files' => File::class,
        'folders' => Folder::class,
        'notifications' => Notification::class,
        'notification-subscriptions' => NotificationSubscription::class,
        'products' => Product::class,
        'warehouses' => Warehouse::class,
        'warehouse-products' => WarehouseProduct::class,
        'purchases-orders' => PurchasesOrder::class,
        'purchases-order-items' => PurchasesOrderItem::class,
        'purchases-invoices' => PurchasesInvoice::class,
        'purchases-invoice-items' => PurchasesInvoiceItem::class,
        'purchases-deliveries' => PurchasesDelivery::class,
        'purchases-delivery-items' => PurchasesDeliveryItem::class,
        'purchases-payments' => PurchasesPayment::class,
        'sales-orders' => SalesOrder::class,
        'sales-order-items' => SalesOrderItem::class,
        'sales-invoices' => SalesInvoice::class,
        'sales-invoice-items' => SalesInvoiceItem::class,
        'sales-deliveries' => SalesDelivery::class,
        'sales-delivery-items' => SalesDeliveryItem::class,
        'sales-payments' => SalesPayment::class,
        'imports' => Import::class,
        'supplier-products' => SupplierProduct::class,
        'stock-movements' => StockMovement::class,
        'stock-movement-items' => StockMovementItem::class,
        'categories' => Category::class,
        'properties' => Property::class,
        'auctions' => Auction::class,
        'auction-fees' => AuctionFee::class,
        'bid-steps' => BidStep::class,
        'taxes' => Tax::class,
        'tax-groups' => TaxGroup::class,
        'options' => Option::class,
        'unit-of-measures' => UnitOfMeasure::class,
        'unit-of-measure-units' => UnitOfMeasureUnit::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Eloquent
    |--------------------------------------------------------------------------
    |
    | Whether your JSON API resources predominantly relate to Eloquent models.
    | This is used by the package's generators.
    |
    | You can override the setting here when running a generator. If the
    | setting here is `true` running a generator with `--no-eloquent` will
    | override it; if the setting is `false`, then `--eloquent` is the override.
    |
    */
    'use-eloquent' => true,

    /*
    |--------------------------------------------------------------------------
    | URL
    |--------------------------------------------------------------------------
    |
    | The API's url, made up of a host, URL namespace and route name prefix.
    |
    | If a JSON API is handling an inbound request, the host will always be
    | detected from the inbound HTTP request. In other circumstances
    | (e.g. broadcasting), the host will be taken from the setting here.
    | If it is `null`, the `app.url` config setting is used as the default.
    | If you set `host` to `false`, the host will never be appended to URLs
    | for inbound requests.
    |
    | The name setting is the prefix for route names within this API.
    |
    */
    'url' => [
        'host' => null,
        'namespace' => '/api/v1',
        'name' => 'api:v1:',
    ],

    /*
    |--------------------------------------------------------------------------
    | Controllers
    |--------------------------------------------------------------------------
    |
    | The default JSON API controller wraps write operations in transactions.
    | You can customise the connection for the transaction here. Or if you
    | want to turn transactions off, set `transactions` to `false`.
    |
    */
    'controllers' => [
        'transactions' => true,
        'connection' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Jobs
    |--------------------------------------------------------------------------
    |
    | Defines settings for the asynchronous processing feature. We recommend
    | referring to the documentation on asynchronous processing if you are
    | using this feature.
    |
    | Note that if you use a different model class, it must implement the
    | asynchronous process interface.
    |
    */
    'jobs' => [
        'resource' => 'queue-jobs',
        'model' => \CloudCreativity\LaravelJsonApi\Queue\ClientJob::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Supported JSON API Extensions
    |--------------------------------------------------------------------------
    |
    | Refer to the JSON API spec for information on supported extensions.
    |
    */
    'supported-ext' => null,

    /*
    |--------------------------------------------------------------------------
    | Encoding Media Types
    |--------------------------------------------------------------------------
    |
    | This defines the JSON API encoding used for particular media
    | types supported by your API. This array can contain either
    | media types as values, or can be keyed by a media type with the value
    | being the options that are passed to the `json_encode` method.
    |
    | These values are also used for Content Negotiation. If a client requests
    | via the HTTP Accept header a media type that is not listed here,
    | a 406 Not Acceptable response will be sent.
    |
    | If you want to support media types that do not return responses with JSON
    | API encoded data, you can do this at runtime. Refer to the
    | Content Negotiation chapter in the docs for details.
    |
    */
    'encoding' => [
        'application/vnd.api+json',
        'application/vnd.ms-excel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Decoding Media Types
    |--------------------------------------------------------------------------
    |
    | This defines the media types that your API can receive from clients.
    | This array is keyed by expected media types, with the value being the
    | service binding that decodes the media type.
    |
    | These values are also used for Content Negotiation. If a client sends
    | a content type not listed here, it will receive a
    | 415 Unsupported Media Type response.
    |
    | Decoders can also be calculated at runtime, and/or you can add support
    | for media types for specific resources or requests. Refer to the
    | Content Negotiation chapter in the docs for details.
    |
    */
    'decoding' => [
        'application/vnd.api+json',
        'application/vnd.ms-excel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Providers
    |--------------------------------------------------------------------------
    |
    | Providers allow vendor packages to include resources in your API. E.g.
    | a Shopping Cart vendor package might define the `orders` and `payments`
    | JSON API resources.
    |
    | A package author will define a provider class in their package that you
    | can add here. E.g. for our shopping cart example, the provider could be
    | `Vendor\ShoppingCart\JsonApi\ResourceProvider`.
    |
    */
    'providers' => [],

];
