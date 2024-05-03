<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Package;
use App\Models\Product;
use App\Models\Reseller;
use App\Models\ResellerInvoice;
use App\Models\ResellerInvoiceItem;
use App\Models\ResellerPayment;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class Reseller2StructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /** @var User */
        $resellerOwner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Reseller 2',
            'is_staff' => false,
            'email' => 'owner@reseller2.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $reseller = Reseller::create([
            'name' => 'Reseller 2',
            'excerpt' => 'Reseller 2 excerpt',
            'email' => 'reseller2@test.test',
            'address' => 'Reseller 2 address',
            'phone' => 'Reseller 2 phone',
            'logo' => env('APP_URL').'/images/admin.jpg',
            'owner_id' => $resellerOwner->id,
        ]);

        /** organization 1 */

        /** @var User */
        $organization1Owner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Organization 1 R2',
            'is_staff' => false,
            'email' => 'owner@organization1.R2.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $organization1 = Organization::create([
            'name' => 'Organization 1 R2',
            'excerpt' => 'Organization 1 Reseller 2',
            'email' => 'organization1@reseller2.test',
            'address' => 'Organization 1 R2 address',
            'phone' => 'Organization 1 R2 phone',
            'logo' => env('APP_URL').'/images/admin.jpg',
            'owner_id' => $organization1Owner->id,
            'reseller_id' => $reseller->id,
        ]);
        $product1 = Product::create([
            'name' => 'Product 1 O1 R2',
            'excerpt' => 'Product 1 Organization 1 Reseller 2',
            'selling_price' => 170,
            'buying_price' => 100,
            'picture' => env('APP_URL').'/images/admin.jpg',
            'gallery' => [env('APP_URL').'/images/admin.jpg'],
            'organization_id' => $organization1->id,
        ]);
        $product2 = Product::create([
            'name' => 'Product 2 O1 R2',
            'excerpt' => 'Product 2 Organization 1 Reseller 2',
            'selling_price' => 155,
            'buying_price' => 100,
            'picture' => null,
            'gallery' => [],
            'organization_id' => $organization1->id,
        ]);

        $package1Role = Role::create([
            'name' => 'Package Users Role O1 R2',
            'reseller_id' => $reseller->id,
        ]);
        $package1Role->syncPermissions([
            Permissions::PERM_VIEW_MODULE_USERS,
            Permissions::PERM_CREATE_USERS,
            Permissions::PERM_EDIT_USERS,
            Permissions::PERM_VIEW_ANY_USERS,
            Permissions::PERM_VIEW_USERS,
        ]);
        $package1 = Package::create([
            'name' => 'Package Invoices O1 R2',
            'excerpt' => 'Package Invoices Organization 1 Reseller 2',
            'price' => 360,
            'picture' => env('APP_URL').'/images/admin.jpg',
            'gallery' => [env('APP_URL').'/images/admin.jpg'],
            'reseller_id' => $reseller->id,
            'default_role_id' => $package1Role->id,
        ]);

        $package2Role = Role::create([
            'name' => 'Package Customers Role O1 R2',
            'reseller_id' => $reseller->id,
        ]);
        $package2Role->syncPermissions([
            Permissions::PERM_VIEW_MODULE_CUSTOMERS,
            Permissions::PERM_CREATE_CUSTOMERS,
            Permissions::PERM_EDIT_CUSTOMERS,
            Permissions::PERM_VIEW_ANY_CUSTOMERS,
            Permissions::PERM_VIEW_CUSTOMERS,
        ]);
        $package2 = Package::create([
            'name' => 'Package Customers Invoices O1 R2',
            'excerpt' => 'Package Customers Invoices Organization 1 Reseller 2',
            'price' => 820,
            'picture' => env('APP_URL').'/images/admin.jpg',
            'gallery' => [env('APP_URL').'/images/admin.jpg'],
            'reseller_id' => $reseller->id,
            'default_role_id' => $package2Role->id,
        ]);

        $invoice = ResellerInvoice::create([
            'expiration_time' => Carbon::now()->addMonths(1),
            'billing_firstname' => 'billing_firstname',
            'billing_lastname' => 'billing_lastname',
            'billing_country' => 'Canada',
            'billing_state' => 'Quebec',
            'billing_city' => 'billing_city',
            'billing_zipcode' => 'billing_zipcode',
            'billing_address' => 'billing_address',
            'billing_email' => 'billing_email@test.test',
            'discounts' => [],
            'recipient_id' => $organization1->id,
            'recipient_type' => Organization::class,
            'reseller_id' => $reseller->id,
        ]);

        $invoiceItem = ResellerInvoiceItem::create([
            'code' => $package1->code,
            'quantity' => 1,
            'unit_price' => $package1->price,
            'reseller_invoice_id' => $invoice->id,
            'reseller_invoiceable_id' => $package1->id,
            'reseller_invoiceable_type' => Package::class,
        ]);
        $invoiceItem = ResellerInvoiceItem::create([
            'code' => $package2->code,
            'quantity' => 1,
            'unit_price' => $package2->price,
            'reseller_invoice_id' => $invoice->id,
            'reseller_invoiceable_id' => $package2->id,
            'reseller_invoiceable_type' => Package::class,
        ]);

        $invoice->status = ResellerInvoice::STATUS_VALIDATED;
        $invoice->save();

        $payment = ResellerPayment::create([
            'source' => ResellerPayment::SOURCE_MANUAL,
            'status' => ResellerPayment::STATUS_COMPLETED,
            'amount' => $invoice->getInvoiceTotalAmount(),
            'reseller_invoice_id' => $invoice->id,
        ]);

        Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_INDIVIDUAL,
            'firstname' => 'Customer 1',
            'lastname' => 'Organization 1 Reseller 2',
            'email' => 'customer1@organization1.reseller2',
            'phone' => '+ 1 819 555 5585',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O1 R2 Zipcode',
            'address' => 'C1 O1 R2 Address',
            'organization_id' => $organization1->id,
        ]);

        Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_COMPANY,
            'company_name' => 'Customer 2 Organization 2 Reseller 2',
            'email' => 'customer2@organization1.reseller2',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R2 Zipcode',
            'address' => 'C2 O1 R2 Address',
            'organization_id' => $organization1->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 1 Organization 1 Reseller 2',
            'fiscal_number' => '123456789',
            'email' => 'supplier1@organization1.reseller2',
            'phone' => '+ 1 819 555 5585',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O1 R2 Zipcode',
            'address' => 'S1 O1 R2 Address',
            'organization_id' => $organization1->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 2 Organization 2 Reseller 2',
            'fiscal_number' => '123456789',
            'email' => 'supplier2@organization1.reseller2',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S2 O1 R2 Zipcode',
            'address' => 'S2 O1 R2 Address',
            'organization_id' => $organization1->id,
        ]);

        Tag::create([
            'name' => 'O1R2 Tag 1',
            'organization_id' => $organization1->id,
        ]);

        Tag::create([
            'name' => 'O1R2 Tag 2',
            'organization_id' => $organization1->id,
        ]);

        /** organization 2 */
        $organization2Owner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Organization 2 R2',
            'is_staff' => false,
            'email' => 'owner@organization2.R2.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $organization2 = Organization::create([
            'name' => 'Organization 2 R2',
            'excerpt' => 'Organization 2 Reseller 2',
            'email' => 'organization2.R2@reseller2.test',
            'address' => 'Organization 2 R2 address',
            'phone' => 'Organization 2 R2 phone',
            'logo' => env('APP_URL').'/images/admin.jpg',
            'owner_id' => $organization2Owner->id,
            'reseller_id' => $reseller->id,
        ]);

        $product1 = Product::create([
            'name' => 'Product 1 Organization 2 R2',
            'excerpt' => 'Product 1 Organization 2 Reseller 2',
            'selling_price' => 250,
            'picture' => env('APP_URL').'/images/admin.jpg',
            'gallery' => [env('APP_URL').'/images/admin.jpg'],
            'organization_id' => $organization2->id,
        ]);
        $product2 = Product::create([
            'name' => 'Product 2 Organization 2 R2',
            'excerpt' => 'Product 2 Organization 2 Reseller 2',
            'selling_price' => 150,
            'picture' => null,
            'gallery' => [],
            'organization_id' => $organization2->id,
        ]);

        $package1Role = Role::create([
            'name' => 'Package Invoicing full Role O2 R2',
            'reseller_id' => $reseller->id,
        ]);
        $package1Role->syncPermissions([
            Permissions::PERM_VIEW_MODULE_USERS,
            Permissions::PERM_CREATE_USERS,
            Permissions::PERM_EDIT_USERS,
            Permissions::PERM_VIEW_ANY_USERS,
            Permissions::PERM_VIEW_USERS,
            Permissions::PERM_VIEW_PRODUCTS,
            Permissions::PERM_VIEW_MODULE_PRODUCTS,
            Permissions::PERM_CREATE_PRODUCTS,
            Permissions::PERM_EDIT_PRODUCTS,
            Permissions::PERM_VIEW_ANY_PRODUCTS,
            Permissions::PERM_VIEW_PRODUCTS,
        ]);
        $package1 = Package::create([
            'name' => 'Package Invoicing O2 R2',
            'excerpt' => 'Package Invoicing Organization 2 Reseller 2',
            'price' => 1250,
            'picture' => env('APP_URL').'/images/creator.jpg',
            'gallery' => [env('APP_URL').'/images/creator.jpg'],
            'reseller_id' => $reseller->id,
            'default_role_id' => $package1Role->id,
        ]);

        $invoice = ResellerInvoice::create([
            'expiration_time' => Carbon::now()->addMonths(1),
            'billing_firstname' => 'billing_firstname',
            'billing_lastname' => 'billing_lastname',
            'billing_country' => 'Canada',
            'billing_state' => 'Quebec',
            'billing_city' => 'billing_city',
            'billing_zipcode' => 'billing_zipcode',
            'billing_address' => 'billing_address',
            'billing_email' => 'billing_email@test.test',
            'discounts' => [],
            'recipient_id' => $organization2->id,
            'recipient_type' => Organization::class,
            'reseller_id' => $reseller->id,
        ]);

        $invoiceItem = ResellerInvoiceItem::create([
            'code' => $package1->code,
            'quantity' => 1,
            'unit_price' => $package1->price,
            'reseller_invoice_id' => $invoice->id,
            'reseller_invoiceable_id' => $package1->id,
            'reseller_invoiceable_type' => Package::class,
        ]);

        $invoice->status = ResellerInvoice::STATUS_VALIDATED;
        $invoice->save();

        $payment = ResellerPayment::create([
            'source' => ResellerPayment::SOURCE_MANUAL,
            'status' => ResellerPayment::STATUS_COMPLETED,
            'amount' => $invoice->getInvoiceTotalAmount(),
            'reseller_invoice_id' => $invoice->id,
        ]);

        Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_INDIVIDUAL,
            'firstname' => 'Customer 1',
            'lastname' => 'Organization 2 Reseller 2',
            'email' => 'customer1@organization2.reseller2',
            'phone' => '+ 1 819 555 5575',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O2 R2 Zipcode',
            'address' => 'C1 O2 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_COMPANY,
            'company_name' => 'Customer 2 Organization 2 Reseller 2',
            'email' => 'customer2@organization2.reseller2',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R2 Zipcode',
            'address' => 'C2 O1 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 1 Organization 2 Reseller 2',
            'fiscal_number' => '123456789',
            'email' => 'supplier1@organization2.reseller2',
            'phone' => '+ 1 819 555 5575',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O2 R2 Zipcode',
            'address' => 'S1 O2 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 2 Organization 2 Reseller 2',
            'fiscal_number' => '123456789',
            'email' => 'supplier2@organization2.reseller2',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S2 O1 R2 Zipcode',
            'address' => 'S2 O1 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Tag::create([
            'name' => 'O2R2 Tag 1',
            'organization_id' => $organization2->id,
        ]);

        Tag::create([
            'name' => 'O2R2 Tag 2',
            'organization_id' => $organization2->id,
        ]);
    }
}
