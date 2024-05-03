<?php

namespace Database\Seeders;

use App\Constants\Permissions;
use App\Models\Contact;
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

class Reseller1StructureSeeder extends Seeder
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
            'lastname' => 'Reseller 1',
            'is_staff' => false,
            'email' => 'owner@reseller1.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $reseller = Reseller::create([
            'name' => 'Reseller 1',
            'excerpt' => 'Reseller 1 excerpt',
            'email' => 'reseller1@test.test',
            'address' => 'Reseller 1 address',
            'phone' => 'Reseller 1 phone',
            'logo' => env('APP_URL').'/images/member.jpg',
            'owner_id' => $resellerOwner->id,
        ]);

        /** organization 1 */

        /** @var User */
        $organization1Owner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Organization 1 R1',
            'is_staff' => false,
            'email' => 'owner@organization1.R1.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $organization1 = Organization::create([
            'name' => 'Organization 1 R1',
            'excerpt' => 'Organization 1 Reseller 1',
            'email' => 'organization1@reseller1.test',
            'address' => 'Organization 1 R1 address',
            'phone' => 'Organization 1 R1 phone',
            'logo' => env('APP_URL').'/images/member.jpg',
            'owner_id' => $organization1Owner->id,
            'reseller_id' => $reseller->id,
        ]);

        $product1 = Product::create([
            'name' => 'Product 1 O1 R1',
            'excerpt' => 'Product 1 Organization 1 Reseller 1',
            'selling_price' => 100,
            'buying_price' => 100,
            'picture' => env('APP_URL').'/images/creator.jpg',
            'gallery' => [env('APP_URL').'/images/creator.jpg'],
            'organization_id' => $organization1->id,
        ]);
        $product2 = Product::create([
            'name' => 'Product 2 O1 R1',
            'excerpt' => 'Product 2 Organization 1 Reseller 1',
            'selling_price' => 150,
            'buying_price' => 150,
            'picture' => null,
            'gallery' => [],
            'organization_id' => $organization1->id,
        ]);

        $package1Role = Role::create([
            'name' => 'Package FULLAPP Role O1 R1',
            'reseller_id' => $reseller->id,
        ]);
        $package1Role->syncPermissions(array_values(Permissions::getAllScopePermissions(Permissions::SCOPE_ORGANIZATION)));

        $package1 = Package::create([
            'name' => 'Package FULLAPP O1 R1',
            'excerpt' => 'Package FULLAPP Organization 1 Reseller 1',
            'price' => 500,
            'picture' => env('APP_URL').'/images/creator.jpg',
            'gallery' => [env('APP_URL').'/images/creator.jpg'],
            'reseller_id' => $reseller->id,
            'default_role_id' => $package1Role->id,
        ]);

        $package2Role = Role::create([
            'name' => 'Package Customers Role O1 R1',
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
            'name' => 'Package Customers O1 R1',
            'excerpt' => 'Package Customers Organization 1 Reseller 1',
            'price' => 50,
            'picture' => env('APP_URL').'/images/creator.jpg',
            'gallery' => [env('APP_URL').'/images/creator.jpg'],
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

        $customer1 = Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_INDIVIDUAL,
            'firstname' => 'Customer 1',
            'lastname' => 'Organization 1 Reseller 1',
            'email' => 'customer1@organization1.reseller1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O1 R1 Zipcode',
            'address' => 'C1 O1 R1 Address',
            'organization_id' => $organization1->id,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 1',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'C1 O1 R1',
            'email' => 'contact1@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O1 R1 Zipcode',
            'address' => 'C1 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $customer1->id,
            'contactable_type' => Customer::class,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 2',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'C1 O1 R1',
            'email' => 'contact2@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O1 R1 Zipcode',
            'address' => 'C1 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $customer1->id,
            'contactable_type' => Customer::class,
        ]);

        $customer2 = Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_COMPANY,
            'company_name' => 'Customer 2 Organization 1 Reseller 1',
            'email' => 'customer2@organization1.reseller1',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R1 Zipcode',
            'address' => 'C2 O1 R1 Address',
            'organization_id' => $organization1->id,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 3',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'C2 O1 R1',
            'email' => 'contact3@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R1 Zipcode',
            'address' => 'C2 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $customer2->id,
            'contactable_type' => Customer::class,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 4',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'C2 O1 R1',
            'email' => 'contact4@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R1 Zipcode',
            'address' => 'C2 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $customer2->id,
            'contactable_type' => Customer::class,
        ]);

        $supplier1 = Supplier::create([
            'company_name' => 'Supplier 1 Organization 1 Reseller 1',
            'fiscal_number' => '123456789',
            'email' => 'supplier1@organization1.reseller1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O1 R1 Zipcode',
            'address' => 'S1 O1 R1 Address',
            'organization_id' => $organization1->id,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 5',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'S1 O1 R1',
            'email' => 'contact3@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O1 R1 Zipcode',
            'address' => 'S1 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $supplier1->id,
            'contactable_type' => Supplier::class,
        ]);

        Contact::create([
            'company_name' => 'Contact Company 4',
            'title' => 'Mr',
            'firstname' => 'Firstname',
            'lastname' => 'S1 O1 R1',
            'email' => 'contact4@c1o1r1.customer1',
            'phone' => '+ 1 819 555 5555',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O1 R1 Zipcode',
            'address' => 'S1 O1 R1 Address',
            'organization_id' => $organization1->id,
            'contactable_id' => $supplier1->id,
            'contactable_type' => Supplier::class,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 2 Organization 1 Reseller 1',
            'fiscal_number' => '123456789',
            'email' => 'supplier2@organization1.reseller1',
            'phone' => '+ 1 819 555 5556',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S2 O1 R1 Zipcode',
            'address' => 'S2 O1 R1 Address',
            'organization_id' => $organization1->id,
        ]);

        Tag::create([
            'name' => 'O1R1 Tag 1',
            'organization_id' => $organization1->id,
        ]);

        Tag::create([
            'name' => 'O1R1 Tag 2',
            'organization_id' => $organization1->id,
        ]);

        /** organization 2 */
        $organization2Owner = User::create([
            'firstname' => 'Owner',
            'lastname' => 'Organization 2 R1',
            'is_staff' => false,
            'email' => 'owner@organization2.R1.test',
            'password' => '123456789',
            'locale' => 'fr',
        ]);

        $organization2 = Organization::create([
            'name' => 'Organization 2 R1',
            'excerpt' => 'Organization 2 Reseller 1',
            'email' => 'organization2.R1@reseller1.test',
            'address' => 'Organization 2 R1 address',
            'phone' => 'Organization 2 R1 phone',
            'logo' => env('APP_URL').'/images/member.jpg',
            'owner_id' => $organization2Owner->id,
            'reseller_id' => $reseller->id,
        ]);

        $product1 = Product::create([
            'name' => 'Product 1 Organization 2 R1',
            'excerpt' => 'Product 1 Organization 2 Reseller 1',
            'selling_price' => 250,
            'picture' => env('APP_URL').'/images/creator.jpg',
            'gallery' => [env('APP_URL').'/images/creator.jpg'],
            'organization_id' => $organization2->id,
        ]);
        $product2 = Product::create([
            'name' => 'Product 2 Organization 2 R1',
            'excerpt' => 'Product 2 Organization 2 Reseller 1',
            'selling_price' => 150,
            'picture' => null,
            'gallery' => [],
            'organization_id' => $organization2->id,
        ]);

        $package1Role = Role::create([
            'name' => 'Package Invoicing Role O2 R1',
            'reseller_id' => $reseller->id,
        ]);
        $package1Role->syncPermissions([
            Permissions::PERM_VIEW_PRODUCTS,
            Permissions::PERM_VIEW_MODULE_PRODUCTS,
            Permissions::PERM_CREATE_PRODUCTS,
            Permissions::PERM_EDIT_PRODUCTS,
            Permissions::PERM_VIEW_ANY_PRODUCTS,
            Permissions::PERM_VIEW_PRODUCTS,
        ]);
        $package1 = Package::create([
            'name' => 'Package Invoicing O2 R1',
            'excerpt' => 'Package Invoicing Organization 2 Reseller 1',
            'price' => 1500,
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
            'lastname' => 'Organization 2 Reseller 1',
            'email' => 'customer1@organization2.reseller1',
            'phone' => '+ 1 819 555 5558',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C1 O2 R1 Zipcode',
            'address' => 'C1 O2 R1 Address',
            'organization_id' => $organization2->id,
        ]);

        Customer::create([
            'customer_type' => Customer::CUSTOMER_TYPE_COMPANY,
            'company_name' => 'Customer 2 Organization 2 Reseller 1',
            'email' => 'customer2@organization2.reseller1',
            'phone' => '+ 1 819 555 5557',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'C2 O1 R2 Zipcode',
            'address' => 'C2 O1 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 1 Organization 2 Reseller 1',
            'fiscal_number' => '123456789',
            'email' => 'supplier1@organization2.reseller1',
            'phone' => '+ 1 819 555 5558',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S1 O2 R1 Zipcode',
            'address' => 'S1 O2 R1 Address',
            'organization_id' => $organization2->id,
        ]);

        Supplier::create([
            'company_name' => 'Supplier 2 Organization 2 Reseller 1',
            'fiscal_number' => '123456789',
            'email' => 'supplier2@organization2.reseller1',
            'phone' => '+ 1 819 555 5557',
            'country' => 'Canada',
            'state' => 'Quebec',
            'city' => 'Canada',
            'zipcode' => 'S2 O1 R2 Zipcode',
            'address' => 'S2 O1 R2 Address',
            'organization_id' => $organization2->id,
        ]);

        Tag::create([
            'name' => 'O2R1 Tag 1',
            'organization_id' => $organization2->id,
        ]);

        Tag::create([
            'name' => 'O2R1 Tag 2',
            'organization_id' => $organization2->id,
        ]);
    }
}
