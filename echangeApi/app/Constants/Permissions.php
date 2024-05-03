<?php

namespace App\Constants;

use ReflectionClass;

class Permissions
{
    public const PERM_VIEW_APP_DEFAULT = 'view app default';

    public const PERM_VIEW_APP_CRM = 'view app crm';

    public const PERM_VIEW_APP_SALES = 'view app sales';

    public const PERM_VIEW_APP_PURCHASES = 'view app purchases';

    public const PERM_VIEW_APP_INVENTORY = 'view app inventory';

    public const PERM_VIEW_APP_FILE_EXPLORER = 'view app file explorer';

    public const PERM_VIEW_APP_RESELLER = 'view app reseller';

    public const PERM_VIEW_APP_ADMIN = 'view app admin';

    public const PERM_VIEW_APP_AUCTION = 'view app auction';

    public const PERM_VIEW_MODULE_RESELLER_PAYMENTS = 'view module reseller payments';

    public const PERM_VIEW_ANY_RESELLER_PAYMENTS = 'view any reseller payments';

    public const PERM_VIEW_RESELLER_PAYMENTS = 'view reseller payments';

    public const PERM_CREATE_RESELLER_PAYMENTS = 'create reseller payments';

    public const PERM_VIEW_MODULE_SUBSCRIPTIONS = 'view module subscriptions';

    public const PERM_VIEW_ANY_SUBSCRIPTIONS = 'view any subscriptions';

    public const PERM_VIEW_SUBSCRIPTIONS = 'view subscriptions';

    public const PERM_CREATE_SUBSCRIPTIONS = 'create subscriptions';

    public const PERM_EDIT_SUBSCRIPTIONS = 'edit subscriptions';

    public const PERM_DELETE_SUBSCRIPTIONS = 'delete subscriptions';

    public const PERM_VIEW_MODULE_PACKAGES = 'view module packages';

    public const PERM_VIEW_ANY_PACKAGES = 'view any packages';

    public const PERM_VIEW_PACKAGES = 'view packages';

    public const PERM_CREATE_PACKAGES = 'create packages';

    public const PERM_EDIT_PACKAGES = 'edit packages';

    public const PERM_DELETE_PACKAGES = 'delete packages';

    public const PERM_VIEW_MODULE_CUSTOMERS = 'view module customers';

    public const PERM_VIEW_ANY_CUSTOMERS = 'view any customers';

    public const PERM_VIEW_CUSTOMERS = 'view customer';

    public const PERM_CREATE_CUSTOMERS = 'create customer';

    public const PERM_EDIT_CUSTOMERS = 'edit customer';

    public const PERM_DELETE_CUSTOMERS = 'delete customer';

    public const PERM_VIEW_MODULE_RESELLER_INVOICES = 'view module reseller invoices';

    public const PERM_VIEW_ANY_RESELLER_INVOICES = 'view any reseller invoices';

    public const PERM_VIEW_RESELLER_INVOICES = 'view reseller invoice';

    public const PERM_CREATE_RESELLER_INVOICES = 'create reseller invoice';

    public const PERM_EDIT_RESELLER_INVOICES = 'edit reseller invoice';

    public const PERM_DELETE_RESELLER_INVOICES = 'delete reseller invoice';

    public const PERM_VIEW_MODULE_ORGANIZATIONS = 'view module organizations';

    public const PERM_VIEW_ANY_ORGANIZATIONS = 'view any organizations';

    public const PERM_VIEW_ORGANIZATIONS = 'view organization';

    public const PERM_CREATE_ORGANIZATIONS = 'create organization';

    public const PERM_EDIT_ORGANIZATIONS = 'edit organization';

    public const PERM_EDIT_OWN_ORGANIZATIONS = 'edit own organization';

    public const PERM_DELETE_ORGANIZATIONS = 'delete organization';

    public const PERM_VIEW_MODULE_RESELLERS = 'view module resellers';

    public const PERM_VIEW_ANY_RESELLERS = 'view any resellers';

    public const PERM_VIEW_RESELLERS = 'view resellers';

    public const PERM_CREATE_RESELLERS = 'create resellers';

    public const PERM_EDIT_RESELLERS = 'edit resellers';

    public const PERM_EDIT_OWN_RESELLERS = 'edit own resellers';

    public const PERM_DELETE_RESELLERS = 'delete resellers';

    public const PERM_VIEW_MODULE_USERS = 'view module users';

    public const PERM_VIEW_ANY_USERS = 'view any users';

    public const PERM_VIEW_USERS = 'view users';

    public const PERM_CREATE_USERS = 'create users';

    public const PERM_EDIT_USERS = 'edit users';

    public const PERM_DELETE_USERS = 'delete users';

    public const PERM_VIEW_MODULE_PRODUCTS = 'view module products';

    public const PERM_VIEW_ANY_PRODUCTS = 'view any products';

    public const PERM_VIEW_PRODUCTS = 'view products';

    public const PERM_CREATE_PRODUCTS = 'create products';

    public const PERM_EDIT_PRODUCTS = 'edit products';

    public const PERM_DELETE_PRODUCTS = 'delete products';

    public const PERM_VIEW_MODULE_RESELLER_PRODUCTS = 'view module reseller products';

    public const PERM_VIEW_ANY_RESELLER_PRODUCTS = 'view any reseller products';

    public const PERM_VIEW_RESELLER_PRODUCTS = 'view reseller products';

    public const PERM_CREATE_RESELLER_PRODUCTS = 'create reseller products';

    public const PERM_EDIT_RESELLER_PRODUCTS = 'edit reseller products';

    public const PERM_DELETE_RESELLER_PRODUCTS = 'delete reseller products';

    public const PERM_VIEW_MODULE_RESELLER_SERVICES = 'view module reseller services';

    public const PERM_VIEW_ANY_RESELLER_SERVICES = 'view any reseller services';

    public const PERM_VIEW_RESELLER_SERVICES = 'view reseller services';

    public const PERM_CREATE_RESELLER_SERVICES = 'create reseller services';

    public const PERM_EDIT_RESELLER_SERVICES = 'edit reseller services';

    public const PERM_DELETE_RESELLER_SERVICES = 'delete reseller services';

    public const PERM_VIEW_MODULE_ROLES = 'view module roles';

    public const PERM_VIEW_ANY_ROLES = 'view any roles';

    public const PERM_VIEW_ROLES = 'view roles';

    public const PERM_CREATE_ROLES = 'create roles';

    public const PERM_EDIT_ROLES = 'edit roles';

    public const PERM_DELETE_ROLES = 'delete roles';

    public const PERM_VIEW_ANY_PERMISSIONS = 'view any permissions';

    public const PERM_VIEW_PERMISSIONS = 'view permissions';

    public const PERM_VIEW_ANY_LOGS = 'view any logs';

    public const PERM_VIEW_LOGS = 'view logs';

    public const PERM_VIEW_MODULE_CONTACTS = 'view module contacts';

    public const PERM_VIEW_ANY_CONTACTS = 'view any contacts';

    public const PERM_VIEW_CONTACTS = 'view contacts';

    public const PERM_CREATE_CONTACTS = 'create contacts';

    public const PERM_EDIT_CONTACTS = 'edit contacts';

    public const PERM_DELETE_CONTACTS = 'delete contacts';

    public const PERM_VIEW_MODULE_SUPPLIERS = 'view module suppliers';

    public const PERM_VIEW_ANY_SUPPLIERS = 'view any suppliers';

    public const PERM_VIEW_SUPPLIERS = 'view suppliers';

    public const PERM_CREATE_SUPPLIERS = 'create suppliers';

    public const PERM_EDIT_SUPPLIERS = 'edit suppliers';

    public const PERM_DELETE_SUPPLIERS = 'delete suppliers';

    public const PERM_VIEW_MODULE_LOCATIONS = 'view module locations';

    public const PERM_VIEW_ANY_LOCATIONS = 'view any locations';

    public const PERM_VIEW_LOCATIONS = 'view locations';

    public const PERM_CREATE_LOCATIONS = 'create locations';

    public const PERM_EDIT_LOCATIONS = 'edit locations';

    public const PERM_DELETE_LOCATIONS = 'delete locations';

    public const PERM_VIEW_MODULE_FILES = 'view module files';

    public const PERM_VIEW_ANY_FILES = 'view any files';

    public const PERM_VIEW_FILES = 'view files';

    public const PERM_CREATE_FILES = 'create files';

    public const PERM_EDIT_FILES = 'edit files';

    public const PERM_DELETE_FILES = 'delete files';

    public const PERM_MANAGE_ACCESS_FILES = 'manage access files';

    public const PERM_VIEW_MODULE_FOLDERS = 'view module folders';

    public const PERM_VIEW_ANY_FOLDERS = 'view any folders';

    public const PERM_VIEW_FOLDERS = 'view folders';

    public const PERM_CREATE_FOLDERS = 'create folders';

    public const PERM_EDIT_FOLDERS = 'edit folders';

    public const PERM_DELETE_FOLDERS = 'delete folders';

    public const PERM_MANAGE_ACCESS_FOLDERS = 'manage access folders';

    public const PERM_MANAGE_LOCKED_FOLDERS = 'manage locked folders';

    public const PERM_VIEW_MODULE_WAREHOUSES = 'view module warehouses';

    public const PERM_VIEW_ANY_WAREHOUSES = 'view any warehouses';

    public const PERM_VIEW_WAREHOUSES = 'view warehouses';

    public const PERM_CREATE_WAREHOUSES = 'create warehouses';

    public const PERM_EDIT_WAREHOUSES = 'edit warehouses';

    public const PERM_DELETE_WAREHOUSES = 'delete warehouses';

    public const PERM_VIEW_MODULE_PURCHASES_ORDERS = 'view module purchases orders';

    public const PERM_VIEW_ANY_PURCHASES_ORDERS = 'view any purchases orders';

    public const PERM_VIEW_PURCHASES_ORDERS = 'view purchases orders';

    public const PERM_CREATE_PURCHASES_ORDERS = 'create purchases orders';

    public const PERM_EDIT_PURCHASES_ORDERS = 'edit purchases orders';

    public const PERM_DELETE_PURCHASES_ORDERS = 'delete purchases orders';

    public const PERM_VIEW_MODULE_PURCHASES_INVOICES = 'view module purchases invoices';

    public const PERM_VIEW_ANY_PURCHASES_INVOICES = 'view any purchases invoices';

    public const PERM_VIEW_PURCHASES_INVOICES = 'view purchases invoices';

    public const PERM_CREATE_PURCHASES_INVOICES = 'create purchases invoices';

    public const PERM_EDIT_PURCHASES_INVOICES = 'edit purchases invoices';

    public const PERM_DELETE_PURCHASES_INVOICES = 'delete purchases invoices';

    public const PERM_VIEW_MODULE_PURCHASES_DELIVERIES = 'view module purchases deliveries';

    public const PERM_VIEW_ANY_PURCHASES_DELIVERIES = 'view any purchases deliveries';

    public const PERM_VIEW_PURCHASES_DELIVERIES = 'view purchases deliveries';

    public const PERM_CREATE_PURCHASES_DELIVERIES = 'create purchases deliveries';

    public const PERM_EDIT_PURCHASES_DELIVERIES = 'edit purchases deliveries';

    public const PERM_DELETE_PURCHASES_DELIVERIES = 'delete purchases deliveries';

    public const PERM_VIEW_MODULE_PURCHASES_PAYMENTS = 'view module purchases payments';

    public const PERM_VIEW_ANY_PURCHASES_PAYMENTS = 'view any purchases payments';

    public const PERM_VIEW_PURCHASES_PAYMENTS = 'view purchases payments';

    public const PERM_CREATE_PURCHASES_PAYMENTS = 'create purchases payments';

    public const PERM_EDIT_PURCHASES_PAYMENTS = 'edit purchases payments';

    public const PERM_DELETE_PURCHASES_PAYMENTS = 'delete purchases payments';

    public const PERM_VIEW_MODULE_SALES_ORDERS = 'view module sales orders';

    public const PERM_VIEW_ANY_SALES_ORDERS = 'view any sales orders';

    public const PERM_VIEW_SALES_ORDERS = 'view sales orders';

    public const PERM_CREATE_SALES_ORDERS = 'create sales orders';

    public const PERM_EDIT_SALES_ORDERS = 'edit sales orders';

    public const PERM_DELETE_SALES_ORDERS = 'delete sales orders';

    public const PERM_VIEW_MODULE_SALES_INVOICES = 'view module sales invoices';

    public const PERM_VIEW_ANY_SALES_INVOICES = 'view any sales invoices';

    public const PERM_VIEW_SALES_INVOICES = 'view sales invoices';

    public const PERM_CREATE_SALES_INVOICES = 'create sales invoices';

    public const PERM_EDIT_SALES_INVOICES = 'edit sales invoices';

    public const PERM_DELETE_SALES_INVOICES = 'delete sales invoices';

    public const PERM_VIEW_MODULE_SALES_DELIVERIES = 'view module sales deliveries';

    public const PERM_VIEW_ANY_SALES_DELIVERIES = 'view any sales deliveries';

    public const PERM_VIEW_SALES_DELIVERIES = 'view sales deliveries';

    public const PERM_CREATE_SALES_DELIVERIES = 'create sales deliveries';

    public const PERM_EDIT_SALES_DELIVERIES = 'edit sales deliveries';

    public const PERM_DELETE_SALES_DELIVERIES = 'delete sales deliveries';

    public const PERM_VIEW_MODULE_SALES_PAYMENTS = 'view module sales payments';

    public const PERM_VIEW_ANY_SALES_PAYMENTS = 'view any sales payments';

    public const PERM_VIEW_SALES_PAYMENTS = 'view sales payments';

    public const PERM_CREATE_SALES_PAYMENTS = 'create sales payments';

    public const PERM_EDIT_SALES_PAYMENTS = 'edit sales payments';

    public const PERM_DELETE_SALES_PAYMENTS = 'delete sales payments';

    public const PERM_VIEW_MODULE_STOCK_MOVEMENTS = 'view module stock movements';

    public const PERM_VIEW_ANY_STOCK_MOVEMENTS = 'view any stock movements';

    public const PERM_VIEW_STOCK_MOVEMENTS = 'view stock movements';

    public const PERM_CREATE_STOCK_MOVEMENTS = 'create stock movements';

    public const PERM_EDIT_STOCK_MOVEMENTS = 'edit stock movements';

    public const PERM_DELETE_STOCK_MOVEMENTS = 'delete stock movements';

    public const PERM_VIEW_MODULE_CATEGORIES = 'view module categories';

    public const PERM_VIEW_ANY_CATEGORIES = 'view any categories';

    public const PERM_VIEW_CATEGORIES = 'view categories';

    public const PERM_CREATE_CATEGORIES = 'create categories';

    public const PERM_EDIT_CATEGORIES = 'edit categories';

    public const PERM_DELETE_CATEGORIES = 'delete categories';

    public const PERM_VIEW_MODULE_PROPERTIES = 'view module properties';

    public const PERM_VIEW_ANY_PROPERTIES = 'view any properties';

    public const PERM_VIEW_PROPERTIES = 'view properties';

    public const PERM_CREATE_PROPERTIES = 'create properties';

    public const PERM_EDIT_PROPERTIES = 'edit properties';

    public const PERM_DELETE_PROPERTIES = 'delete properties';

    public const PERM_TOGGLE_ACTIVATION_PROPERTIES = 'toggle activation properties';

    public const PERM_ACCESS_ALL_FIELDS_PROPERTIES = 'access all fields properties';

    public const PERM_EXPORTS_PROPERTIES = 'exports properties';

    public const PERM_CHANGE_APPROVED_STATUS_PROPERTIES = 'change approved status properties';

    public const PERM_VIEW_ANY_TRANSACTIONS_PROPERTIES = 'view any transactions properties';

    public const PERM_CREATE_TRANSACTIONS_PROPERTIES = 'create transactions properties';

    public const PERM_DELETE_TRANSACTIONS_PROPERTIES = 'delete transactions properties';


    public const PERM_VIEW_MODULE_AUCTIONS = 'view module auctions';

    public const PERM_VIEW_ANY_AUCTIONS = 'view any auctions';

    public const PERM_VIEW_AUCTIONS = 'view auctions';

    public const PERM_CREATE_AUCTIONS = 'create auctions';

    public const PERM_EDIT_AUCTIONS = 'edit auctions';

    public const PERM_DELETE_AUCTIONS = 'delete auctions';

    public const PERM_VIEW_MODULE_TAXES = 'view module taxes';

    public const PERM_VIEW_ANY_TAXES = 'view any taxes';

    public const PERM_VIEW_TAXES = 'view taxes';

    public const PERM_CREATE_TAXES = 'create taxes';

    public const PERM_EDIT_TAXES = 'edit taxes';

    public const PERM_DELETE_TAXES = 'delete taxes';

    public const PERM_VIEW_MODULE_OPTIONS = 'view module options';

    public const PERM_VIEW_ANY_OPTIONS = 'view any options';

    public const PERM_EDIT_OPTIONS = 'edit options';

    public const SCOPE_ADMIN = 0;

    public const SCOPE_RESELLER = 20;

    public const SCOPE_ORGANIZATION = 40;

    public const PERM_VIEW_MODULE_IMPORTS = 'view module imports';

    public const PERM_VIEW_ANY_IMPORTS = 'view any imports';

    public const PERM_VIEW_IMPORTS = 'view imports';

    public const PERM_CREATE_IMPORTS = 'create imports';

    public const PERM_EDIT_IMPORTS = 'edit imports';

    public const PERM_DELETE_IMPORTS = 'delete imports';

    public const PERMISSIONS__SCOPE = [
        self::PERM_VIEW_APP_DEFAULT => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_CRM => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_SALES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_PURCHASES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_INVENTORY => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_FILE_EXPLORER => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_RESELLER => self::SCOPE_RESELLER,
        self::PERM_VIEW_APP_ADMIN => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_APP_AUCTION => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_RESELLER_PAYMENTS => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_RESELLER_PAYMENTS => self::SCOPE_RESELLER,
        self::PERM_VIEW_RESELLER_PAYMENTS => self::SCOPE_RESELLER,
        self::PERM_CREATE_RESELLER_PAYMENTS => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_SUBSCRIPTIONS => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_SUBSCRIPTIONS => self::SCOPE_RESELLER,
        self::PERM_VIEW_SUBSCRIPTIONS => self::SCOPE_RESELLER,
        self::PERM_CREATE_SUBSCRIPTIONS => self::SCOPE_RESELLER,
        self::PERM_EDIT_SUBSCRIPTIONS => self::SCOPE_RESELLER,
        self::PERM_DELETE_SUBSCRIPTIONS => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_PACKAGES => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_PACKAGES => self::SCOPE_RESELLER,
        self::PERM_VIEW_PACKAGES => self::SCOPE_RESELLER,
        self::PERM_CREATE_PACKAGES => self::SCOPE_RESELLER,
        self::PERM_EDIT_PACKAGES => self::SCOPE_RESELLER,
        self::PERM_DELETE_PACKAGES => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_CUSTOMERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_CUSTOMERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_CUSTOMERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_CUSTOMERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_CUSTOMERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_CUSTOMERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_RESELLER_INVOICES => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_RESELLER_INVOICES => self::SCOPE_RESELLER,
        self::PERM_VIEW_RESELLER_INVOICES => self::SCOPE_RESELLER,
        self::PERM_CREATE_RESELLER_INVOICES => self::SCOPE_RESELLER,
        self::PERM_EDIT_RESELLER_INVOICES => self::SCOPE_RESELLER,
        self::PERM_DELETE_RESELLER_INVOICES => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_VIEW_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_CREATE_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_EDIT_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_DELETE_ORGANIZATIONS => self::SCOPE_RESELLER,
        self::PERM_EDIT_OWN_ORGANIZATIONS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_RESELLERS => self::SCOPE_ADMIN,
        self::PERM_VIEW_ANY_RESELLERS => self::SCOPE_ADMIN,
        self::PERM_VIEW_RESELLERS => self::SCOPE_ADMIN,
        self::PERM_CREATE_RESELLERS => self::SCOPE_ADMIN,
        self::PERM_EDIT_RESELLERS => self::SCOPE_ADMIN,
        self::PERM_EDIT_OWN_RESELLERS => self::SCOPE_RESELLER,
        self::PERM_DELETE_RESELLERS => self::SCOPE_ADMIN,

        self::PERM_VIEW_MODULE_USERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_USERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_USERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_USERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_USERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_USERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PRODUCTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PRODUCTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PRODUCTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PRODUCTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PRODUCTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PRODUCTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_RESELLER_PRODUCTS => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_RESELLER_PRODUCTS => self::SCOPE_RESELLER,
        self::PERM_VIEW_RESELLER_PRODUCTS => self::SCOPE_RESELLER,
        self::PERM_CREATE_RESELLER_PRODUCTS => self::SCOPE_RESELLER,
        self::PERM_EDIT_RESELLER_PRODUCTS => self::SCOPE_RESELLER,
        self::PERM_DELETE_RESELLER_PRODUCTS => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_RESELLER_SERVICES => self::SCOPE_RESELLER,
        self::PERM_VIEW_ANY_RESELLER_SERVICES => self::SCOPE_RESELLER,
        self::PERM_VIEW_RESELLER_SERVICES => self::SCOPE_RESELLER,
        self::PERM_CREATE_RESELLER_SERVICES => self::SCOPE_RESELLER,
        self::PERM_EDIT_RESELLER_SERVICES => self::SCOPE_RESELLER,
        self::PERM_DELETE_RESELLER_SERVICES => self::SCOPE_RESELLER,

        self::PERM_VIEW_MODULE_ROLES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_ROLES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ROLES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_ROLES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_ROLES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_ROLES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_ANY_PERMISSIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PERMISSIONS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_ANY_LOGS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_LOGS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_CONTACTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_CONTACTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_CONTACTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_CONTACTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_CONTACTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_CONTACTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_SUPPLIERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_SUPPLIERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_SUPPLIERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_SUPPLIERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_SUPPLIERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_SUPPLIERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_LOCATIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_LOCATIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_LOCATIONS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_LOCATIONS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_LOCATIONS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_LOCATIONS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_FILES => self::SCOPE_ORGANIZATION,
        self::PERM_MANAGE_ACCESS_FILES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_MANAGE_ACCESS_FOLDERS => self::SCOPE_ORGANIZATION,
        self::PERM_MANAGE_LOCKED_FOLDERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_WAREHOUSES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_WAREHOUSES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_WAREHOUSES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_WAREHOUSES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_WAREHOUSES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_WAREHOUSES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PURCHASES_ORDERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PURCHASES_INVOICES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PURCHASES_DELIVERIES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PURCHASES_PAYMENTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_SALES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_SALES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_SALES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_SALES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_SALES_ORDERS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_SALES_ORDERS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_SALES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_SALES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_SALES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_SALES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_SALES_INVOICES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_SALES_INVOICES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_SALES_DELIVERIES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_SALES_PAYMENTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_STOCK_MOVEMENTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_IMPORTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_IMPORTS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_IMPORTS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_IMPORTS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_IMPORTS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_IMPORTS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_CATEGORIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_CATEGORIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_CATEGORIES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_CATEGORIES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_CATEGORIES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_CATEGORIES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_TOGGLE_ACTIVATION_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_ACCESS_ALL_FIELDS_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_EXPORTS_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_CHANGE_APPROVED_STATUS_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_TRANSACTIONS_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_TRANSACTIONS_PROPERTIES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_TRANSACTIONS_PROPERTIES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_AUCTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_AUCTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_AUCTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_AUCTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_AUCTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_AUCTIONS => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_TAXES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_TAXES => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_TAXES => self::SCOPE_ORGANIZATION,
        self::PERM_CREATE_TAXES => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_TAXES => self::SCOPE_ORGANIZATION,
        self::PERM_DELETE_TAXES => self::SCOPE_ORGANIZATION,

        self::PERM_VIEW_MODULE_OPTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_VIEW_ANY_OPTIONS => self::SCOPE_ORGANIZATION,
        self::PERM_EDIT_OPTIONS => self::SCOPE_ORGANIZATION,
    ];

    public static function getAllScopePermissions(int $scope): array
    {
        $permissionsClassData = new ReflectionClass(Permissions::class);
        $permissions = array_filter($permissionsClassData->getConstants(), function ($key) {
            return str_starts_with($key, 'PERM_');
        }, ARRAY_FILTER_USE_KEY);
        $permissions = array_filter($permissions, function ($key) use ($scope) {
            return self::PERMISSIONS__SCOPE[$key] >= $scope;
        });

        return $permissions;
    }
}
