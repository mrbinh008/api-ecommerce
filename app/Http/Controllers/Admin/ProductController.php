<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductSku;
use App\Models\SkuValue;
use App\Models\Option;
use App\Models\OptionValue;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::query()->paginate(10);
        $data= $products->transform(function ($product) {
           return [
                'id' => $product->id,
                'product_name' => $product->product_name,
                'sku' => $product->sku,
                'brand_id' => $product->brand_id,
                'description' => $product->description,
                'short_description' => $product->short_description,
                'product_weight' => $product->product_weight,
                'is_published' => $product->is_published,
                'is_featured' => $product->is_featured,
                'item'=> $product->skus->transform(function ($sku) {
                    return [
                        'id' => $sku->id,
                        'sku' => $sku->sku,
                        'price' => $sku->price,
                        'quantity' => $sku->quantity,
                        'option' => $sku->values->transform(function ($value) {
                            return [
                                'label' => $value->option->option_name,
                                'value' => $value->value->value_name,
                            ];
                        }),
                    ];
                }),
           ];
        });
        return responsePaginate($products, $data, 200, 'Get list product success');
    }

    public function store(Request $request)
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

            $optionValues = [];

            $options = [];

            // Lưu các tùy chọn và giá trị của chúng
            foreach ($request->options as $optionData) {
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
//                $option->values()->saveMany($optionValues);
            }
            foreach ($request->skus as $skuData) {
                $sku = ProductSku::create([
                    'product_id' => $product->id,
                    'sku' => $skuData['sku'],
                    'price' => $skuData['price'],
                    'quantity' => $skuData['quantity'],
                ]);
                SkuValue::create([
                    'product_id' => $product->id,
                    'sku_id' => $sku->id,
                    'option_id' => $options[$skuData['values']['option_id']],
                    'value_id' => $optionValues[$skuData['values']['value_id']],
                ]);

            }
            DB::commit();
            return responseCustom([], 200, 'Data saved successfully');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Data saved failed', $e->getMessage());
        }
    }
}
