<?php

namespace App\Http\Controllers\Api\V1\Import;

use App\Constants\ImportsInformation;
use App\Constants\Permissions;
use App\Constants\ProductsInformation;
use App\Helpers\Import as HelpersImport;
use App\Http\Requests\Api\V1\Import\RunProductSynchronizeRequest;
use App\Models\Import;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\SupplierProduct;
use App\Models\User;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Exception;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RunProductSynchronizeController extends JsonApiController
{
    public function synchronize(Import $import, RunProductSynchronizeRequest $request): Response
    {
        /** @var User */
        $user = Auth::user();
        if (! (
            $user->can(Permissions::PERM_CREATE_PRODUCTS)
            && $user->can(Permissions::PERM_EDIT_PRODUCTS)
            && $user->can(Permissions::PERM_DELETE_PRODUCTS)
        )) {
            abort(403, __('notifications.unauthorized_sync_product', []));
        }

        try {
            $importData = HelpersImport::getImportData($import);
        } catch (Exception $e) {
            abort(400, $e->getMessage());
        }
        if ($import->model !== 'Product') {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Operation failed',
                    'detail' => 'Model field and csv field are not defined',
                    'status' => '500',
                ]),
            ]);
        }

        $supplier = Supplier::where('id', $import->linked_object_id)
            ->first();

        if (! $supplier) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Operation failed',
                    'detail' => 'Supplier does not exit',
                    'status' => '500',
                ]),
            ]);
        }

        if (! (isset($import->identifier['model_field']) && isset($import->identifier['csv_field']))) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Operation failed',
                    'detail' => 'Model field and csv field are not defined',
                    'status' => '500',
                ]),
            ]);
        }
        $organization = $import->organization_id;
        $results = [];

        DB::beginTransaction();
        try {
            $supplierProductIds = [];
            $index = 0;
            foreach ($importData as $key => $data) {
                $index = $key;
                $isNewProduct = true;
                $data['sku'] = isset($data['sku']) ? HelpersImport::escapeSpecialChars($data['sku']) : null;
                $data['name'] = isset($data['name']) ? HelpersImport::escapeSpecialChars($data['name']) : null;
                $data['code'] = isset($data['code']) ? HelpersImport::escapeSpecialChars($data['code']) : null;
                $data['excerpt'] = isset($data['excerpt']) ? HelpersImport::escapeSpecialChars($data['excerpt']) : null;
                $data['selling_price'] = isset($data['selling_price']) ? HelpersImport::extractFloatFromString($data['selling_price']) : null;
                $data['buying_price'] = isset($data['buying_price']) ? HelpersImport::extractFloatFromString($data['buying_price']) : null;
                try {
                    $product = Product::where($import->identifier['model_field'], $data[$import->identifier['model_field']])->first();
                    if ($product) {
                        $isNewProduct = false;
                        $product->fill([
                            'organization_id' => $organization,
                            'status' => ProductsInformation::STATUS_ACTIVE,
                            'code' => $data['code'] ?? $product->code,
                            'sku' => $data['sku'] ?? $product->sku,
                            'name' => $data['name'] ?? $product->name,
                            'excerpt' => $data['excerpt'] ?? $product->excerpt,
                            'selling_price' => $data['selling_price'] ?? $product->selling_price,
                            'buying_price' => $data['buying_price'] ?? $product->buying_price,
                            'selling_taxes' => $data['selling_taxes'] ?? $product['selling_taxes'],
                            'buying_taxes' => $data['buying_taxes'] ?? $product['buying_taxes'],
                            'picture' => $data['picture'] ?? $product->picture,
                            'gallery' => $data['gallery'] ?? $product->gallery,
                        ]);
                        $product->save();
                    } else {
                        $product = Product::create([
                            'organization_id' => $organization,
                            'status' => ProductsInformation::STATUS_ACTIVE,
                            ...$data,
                        ]);
                    }
                    $productId = $product->id;

                    $supplierProduct = SupplierProduct::where('product_id', $productId)
                        ->where('organization_id', $organization)
                        ->where('supplier_id', $import->linked_object_id)
                        ->first();

                    if ($supplierProduct) {
                        $supplierProduct->fill([
                            'organization_id' => $organization,
                            'product_id' => $productId,
                            'supplier_id' => $import->linked_object_id,
                            'sku' => $product->sku,
                            'selling_price' => $product->selling_price,
                            'buying_price' => $product->buying_price,
                            'selling_taxes' => $product['selling_taxes'],
                            'buying_taxes' => $product['buying_taxes'],
                            'excerpt' => $product->excerpt,
                        ]);
                        $supplierProduct->save();
                    } else {
                        $supplierProduct = SupplierProduct::create([
                            'organization_id' => $organization,
                            'product_id' => $productId,
                            'supplier_id' => $import->linked_object_id,
                            'sku' => $product->sku,
                            'selling_price' => $product->selling_price,
                            'selling_taxes' => $product['selling_taxes'],
                            'buying_taxes' => $product['buying_taxes'],
                            'buying_price' => $product->buying_price,
                            'excerpt' => $product->excerpt,
                        ]);
                    }
                    array_push($supplierProductIds, $supplierProduct->id);

                    DB::table('importables')->insert(
                        [
                            'import_id' => $import->id,
                            'importable_id' => $productId,
                            'importable_type' => "App\Models\Product",
                        ]
                    );
                    $results[$key + 1] = [
                        'success' => true,
                        'status' => $isNewProduct ? ImportsInformation::STATUS_CREATED : ImportsInformation::STATUS_UPDATED,
                        'line' => $key + 1,
                        'data' => $data,
                        'id' => $productId,
                    ];
                } catch (Exception $e) {
                    $results[$key + 1] = [
                        'success' => false,
                        'line' => $key + 1,
                        'data' => $data,
                        'error' => $e->getMessage(),
                    ];
                }
            }

            $productIds = SupplierProduct::where('supplier_id', $import->linked_object_id)
                ->whereNotIn('id', $supplierProductIds)
                ->pluck('product_id');
            $productsToDelete = Product::whereIn('id', $productIds)->get();

            if (count($supplierProductIds)) {
                $index += 1;
                foreach ($productsToDelete as $product) {
                    $index += 1;
                    try {
                        $product->update(['status' => ProductsInformation::STATUS_DELETED]);
                        $results[$index] = [
                            'success' => true,
                            'status' => ImportsInformation::STATUS_DELETED,
                            'line' => $index,
                            'data' => $product,
                            'id' => $product->id,
                        ];
                    } catch (Exception $e) {
                        $results[$index] = [
                            'success' => false,
                            'line' => $index,
                            'data' => $product,
                            'error' => $e->getMessage(),
                        ];
                    }
                }
            }

            $import->results = $results;
            $import->status = Import::STATUS_COMPLETED;
            $import->save();
            DB::commit();

            return $this->reply()->content($import);
        } catch (Exception $e) {
            DB::rollback();

            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Transaction failed',
                    'detail' => $e->getMessage(),
                    'status' => '500',
                ]),
            ]);
        }
    }
}
