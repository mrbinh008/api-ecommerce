<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\ProductGallery;
use App\Traits\ImageUploadTrait;
use http\Env;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\SkuValue;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    use ImageUploadTrait;

    /**
     * Get list product
     *
     * @param \Illuminate\Http\Request $request : page,limit,sort_by,sort_direction
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $products = Product::query()->paginate($request->input('limit', 10));
        $data = $products->transform(function ($product) {
            return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'brand_name' => $product->brand->name,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'product_weight' => $product->product_weight,
                'is_published' => $product->is_published,
                'is_featured' => $product->is_featured,
                'total_quantity' => $product->skus->count(),
                'price' => $product->skus->min('price') !== $product->skus->max('price') ? $product->skus->min('price') . ' - ' . $product->skus->max('price') : $product->skus->min('price'),
            ];
        });
        return responsePaginate($products, $data, 200, 'Get list product success');
    }

    /**
     * Add new product
     *
     * @param ProductRequest $request : product_name,sku,brand_id,description,short_description,product_weight,is_published,is_featured,options,skus
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(ProductRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::create([
                'product_name' => $request->input('product_name'),
                'sku' => $request->input('sku'),
                'slug' => \Str::slug($request->input('product_name')),
                'brand_id' => $request->input('brand_id'),
                'description' => $request->input('description'),
                'short_description' => $request->input('short_description'),
                'product_weight' => $request->input('product_weight'),
                'is_published' => $request->input('is_published'),
                'is_featured' => $request->input('is_featured'),
            ]);
            $optionData = $this->createOption($product, $request->options);
            $this->createSku($product, $request->skus, $optionData['options'], $optionData['optionValues']);
            DB::commit();
            return responseCustom([], 201, 'Create product success');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Data saved failed', $e->getMessage());
        }
    }

    /**
     * Get product by id
     *
     * @param $id : product id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = Product::query()->whereId($id)->first();
        if (!$product) {
            return responseCustom([], 404, 'Product not found');
        }
        $data = [
            'id' => $product->id,
            'product_name' => $product->product_name,
            'sku' => $product->sku,
            'brand_id' => $product->brand_id,
            'description' => $product->description,
            'short_description' => $product->short_description,
            'product_weight' => $product->product_weight,
            'is_published' => $product->is_published,
            'is_featured' => $product->is_featured,
            'images' => $product->images->transform(function ($image) {
                return [
                    'path' => \Illuminate\Support\Env::get('APP_URL') . '/storage/' . $image->image,
                ];
            }),
            'options' => $product->options->transform(function ($option) {
                return [
                    'id' => $option->id,
                    'option_name' => $option->option_name,
                    'option_values' => $option->values->transform(function ($value) {
                        return [
                            'id' => $value->id,
                            'value' => $value->value_name,
                        ];
                    }),
                ];
            }),
            'skus' => $product->skus->transform(function ($sku) {
                return [
                    'id' => $sku->id,
                    'sku' => $sku->sku,
                    'price' => $sku->price,
                    'quantity' => $sku->quantity,
                    'values' => $sku->values->transform(function ($value) {
                        return [
                            'option_id' => $value->option->id,
                            'option_name' => $value->option->option_name,
                            'value_id' => $value->value->id,
                            'value' => $value->value->value_name,
                        ];
                    }),
                ];
            }),
            'category' => $product->categories->pluck('id'),
        ];
        return responseCustom($data, 200, 'Get product success');
    }

    /**
     * Update product
     *
     * @param ProductRequest $request : id,product_name,sku,brand_id,description,short_description,product_weight,is_published,is_featured,options,skus
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(ProductRequest $request): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            $product = Product::query()->whereId($request->id)->first();
            if (!$product) {
                return responseCustom([], 404, 'Product not found');
            }
            $product->update([
                'product_name' => $request->input('product_name'),
                'sku' => $request->input('sku'),
                'slug' => \Str::slug($request->input('product_name')),
                'brand_id' => $request->input('brand_id'),
                'description' => $request->input('description'),
                'short_description' => $request->input('short_description'),
                'product_weight' => $request->input('product_weight'),
                'is_published' => $request->input('is_published'),
                'is_featured' => $request->input('is_featured'),
            ]);

            $optionData = $this->updateOption($product, $request->options);
            $this->updateSku($product, $request->skus, $optionData['options'], $optionData['optionValues']);

            DB::commit();
            return responseCustom([], 200, 'Data updated successfully');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Data update failed', $e->getMessage());
        }
    }

    /**
     * Delete product
     *
     * @param $id : product id
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @throws \Exception
     */
    public function destroy($id): \Illuminate\Http\JsonResponse
    {
        DB::beginTransaction();

        try {
            OptionValue::query()->whereProductId($id)->delete();
            Option::query()->whereProductId($id)->delete();
            SkuValue::query()->whereProductId($id)->delete();
            ProductSku::query()->whereProductId($id)->delete();
            Product::query()->whereId($id)->delete();
            DB::commit();
            return responseCustom([], 200, 'Delete product success');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Delete product failed');
        }
    }

    /**
     * Change status product
     *
     * @param $id : product id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function changeStatus($id)
    {
        try {
            $product = Product::query()->whereId($id)->first();
            if (!$product) {
                return responseCustom([], 404, 'Product not found');
            }
            $product->is_published = !$product->is_published;
            $product->save();
            return responseCustom([], 200, 'Change status success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Change status failed');
        }
    }

    /**
     * Search product
     *
     * @param \Illuminate\Http\Request $request : q
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $products = Product::query()
            ->search($request->input('q'))
            ->paginate(10);
        $data = $products->getCollection()
            ->transform(function ($product) {
                return [
                    'id' => $product->id,
                    'product_name' => $product->product_name,
                    'sku' => $product->sku,
                    'brand_name' => $product->brand->name,
                    'description' => $product->description,
                    'short_description' => $product->short_description,
                    'product_weight' => $product->product_weight,
                    'is_published' => $product->is_published,
                    'is_featured' => $product->is_featured,
                    'total_quantity' => $product->skus->count(),
                    'price' => $product->skus->min('price') !== $product->skus->max('price') ?
                        $product->skus->min('price') . ' - ' . $product->skus->max('price') :
                        $product->skus->min('price'),
                ];
            });
        return responsePaginate($products, $data, 200, 'Search product success');
    }

    /**
     * Create option and option value
     *
     * @param $product : product object
     * @param $optionArray : array of option
     * @return array[]
     */
    private function createOption($product, $optionArray)
    {
        $optionValues = [];
        $options = [];
        foreach ($optionArray as $optionData) {
            $option = Option::create([
                'product_id' => $product->id,
                'option_name' => $optionData['option_name'],
            ]);
            $options[$optionData['id']] = $option->id;
            foreach ($optionData['option_values'] as $optionValueData) {
                $optionValue = OptionValue::create([
                    'product_id' => $product->id,
                    'option_id' => $option->id,
                    'value_name' => $optionValueData['value'],
                ]);
                $optionValues[$optionValueData['id']] = $optionValue->id;
            }
        }
        return ['options' => $options, 'optionValues' => $optionValues];
    }

    /**
     * Create sku and sku value
     *
     * @param $product : product object
     * @param $skuArray : array of sku
     * @param $options : array of option
     * @param $optionValues : array of option value
     */
    private function createSku($product, $skuArray, $options, $optionValues)
    {
        foreach ($skuArray as $skuData) {
            $sku = ProductSku::create([
                'product_id' => $product->id,
                'sku' => $skuData['sku'],
                'price' => $skuData['price'],
                'quantity' => $skuData['quantity'],
            ]);
            foreach ($skuData['values'] as $valueData) {
                SkuValue::create([
                    'product_id' => $product->id,
                    'sku_id' => $sku->id,
                    'option_id' => $options[$valueData['option_id']],
                    'value_id' => $optionValues[$valueData['value_id']],
                ]);
            }
        }
    }

    /**
     * Update option and option value
     *
     * @param $product : product object
     * @param $optionArray : array of option
     * @return array[]
     */
    private function updateOption($product, $optionArray)
    {
        $optionValues = [];
        $options = [];

        $existingOptionIds = $product->options->pluck('id')->toArray();

        foreach ($optionArray as $optionData) {
            $option = Option::updateOrCreate([
                'id' => $optionData['id'],
                'product_id' => $product->id,
            ], [
                'option_name' => $optionData['option_name'],
            ]);

            $options[$optionData['id']] = $option->id;
            unset($existingOptionIds[array_search($option->id, $existingOptionIds)]);

            $existingOptionValueIds = $option->values->pluck('id')->toArray();
            foreach ($optionData['option_values'] as $optionValueData) {
                $optionValue = OptionValue::updateOrCreate([
                    'id' => $optionValueData['id'],
                    'option_id' => $option->id,
                ], [
                    'product_id' => $product->id,
                    'value_name' => $optionValueData['value'],
                ]);

                $optionValues[$optionValueData['id']] = $optionValue->id;
                unset($existingOptionValueIds[array_search($optionValue->id, $existingOptionValueIds)]);
            }

            // Delete any option values that were not in the update.
            OptionValue::whereIn('id', $existingOptionValueIds)->delete();
        }

        // Delete any options that were not in the update.
        Option::whereIn('id', $existingOptionIds)->delete();

        return ['options' => $options, 'optionValues' => $optionValues];
    }

    /**
     * Update sku and sku value
     *
     * @param $product : product object
     * @param $skuArray : array of sku
     * @param $options : array id of option
     * @param $optionValues : array id of option value
     */
    private function updateSku($product, $skuArray, $options, $optionValues)
    {
        $existingSkuIds = $product->skus->pluck('id')->toArray();

        foreach ($skuArray as $skuData) {
            $sku = ProductSku::updateOrCreate([
                'id' => $skuData['id'],
                'product_id' => $product->id,
            ], [
                'sku' => $skuData['sku'],
                'price' => $skuData['price'],
                'quantity' => $skuData['quantity'],
            ]);

            unset($existingSkuIds[array_search($sku->id, $existingSkuIds)]);

            // Prepare an array to store the new SkuValues
            $newSkuValues = [];

            foreach ($skuData['values'] as $valueData) {
                $newSkuValues[] = [
                    'product_id' => $product->id,
                    'sku_id' => $sku->id,
                    'option_id' => isset($valueData['option_id']) ? $options[$valueData['option_id']] : null,
                    'value_id' => isset($valueData['value_id']) ? $optionValues[$valueData['value_id']] : null,
                ];
            }

            // Delete existing SkuValues for this SKU
            SkuValue::where('sku_id', $sku->id)->delete();

            // Insert the new SkuValues
            SkuValue::insert($newSkuValues);
        }

        // Delete any SKUs that were not in the update.
        ProductSku::whereIn('id', $existingSkuIds)->delete();
    }

    private function uploadImageProduct($product, $image)
    {
        try {
            ProductGallery::query()->create([
                'product_id' => $product->id,
                'gallery_id' => $image
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    private function updateImageProduct($product, $image)
    {
        try {
            ProductGallery::query()->whereProdutId($product->id)->update(['gallery_id' => $image]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    private function uploadImageSku($product, $sku, $image)
    {
        try {
            ProductGallery::query()->create([
                'product_id' => $product->id,
                'product_sku_id' => $sku->id,
                'gallery_id' => $image,
            ]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }

    private function updateImageSku($product, $sku, $image)
    {
        try {
            ProductGallery::query()
                ->whereProductId($product->id)
                ->whereProductSkuId($sku->id)
                ->update(['gallery_id' => $image]);
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
        }
    }
}
