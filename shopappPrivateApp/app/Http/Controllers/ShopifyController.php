<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ShopifyController extends Controller
{
    public function index()
    {
        // Code put here will run when you navigate to /show_products 
        // This creates an instance of the Shopify API wrapper and 
        // authenticates our app.      
        $shopify = \App::make('ShopifyAPI', [ 
            'API_KEY'       => env('SHOPIFY_KEY'), 
            'API_SECRET'    => env('SHOPIFY_SECRET'), 
            'SHOP_DOMAIN'   => env('SHOPIFY_DOMAIN'), 
            'ACCESS_TOKEN'  => env('SHOPIFY_TOKEN') 
        ]);

        // Gets a list of products 
        $result = $shopify->call([ 
            'METHOD'    => 'GET', 
            'URL'       => '/admin/products.json?page=1' 
        ]); 
        $products = $result->products;

        return view('shop.index')->with('products', $products);       
    }

    public function edit($id)
    {
        /*
        // Code put here will run when you navigate to /show_products 
        // This creates an instance of the Shopify API wrapper and 
        // authenticates our app.
        */ 
        $shopify = \App::make('ShopifyAPI', [ 
            'API_KEY'       => env('SHOPIFY_KEY'), 
            'API_SECRET'    => env('SHOPIFY_SECRET'), 
            'SHOP_DOMAIN'   => env('SHOPIFY_DOMAIN'), 
            'ACCESS_TOKEN'  => env('SHOPIFY_TOKEN') 
        ]);
    
        // Gets a list of products 
        $result = $shopify->call([ 
            'METHOD'    => 'GET', 
            'URL'       => '/admin/products/'. $id .'.json',            
        ]);  
     
        $product = $result->product;    
        //$productId = $id;
        //$productTitle = $product->title;

        foreach($product->variants as $variant)
        {
            // GET https://{store}.myshopify.com/admin/products/{product_id}/variants/{variant_id}.json
            $result2 = $shopify->call([ 
                'METHOD'    => 'GET', 
                'URL'       => '/admin/products/'. $id . '/variants/' . $variant->id . '.json',            
            ]);

            $variant = $result2->variant;
            $inventoryItemId = $variant->inventory_item_id;

            // GET https://{store}.myshopify.com/admin/inventory_levels.json?inventory_item_ids={inventory_item_id}
            $result3 = $shopify->call([ 
                'METHOD'    => 'GET', 
                'URL'       => '/admin/inventory_levels.json?inventory_item_ids=' . $inventoryItemId,            
            ]);
            $inventoryLevels = $result3->inventory_levels;       
            return view('shop.edit')->with('product', $product)->with('inventoryLevels', $inventoryLevels);  
        };
    }

    public function update(Request $request, $id)
    {  
        // PUT /admin/products/#{product_id}.json
        $shopify = \App::make('ShopifyAPI', [ 
            'API_KEY'       => env('SHOPIFY_KEY'), 
            'API_SECRET'    => env('SHOPIFY_SECRET'), 
            'SHOP_DOMAIN'   => env('SHOPIFY_DOMAIN'), 
            'ACCESS_TOKEN'  => env('SHOPIFY_TOKEN') 
        ]);
        $result = $shopify->call([ 
            'METHOD'    => 'PUT', 
            'URL'       => '/admin/products/'. $id .'.json' ,
            'DATA'      => [ 
                'product' => [ 
                    'title' => $request->title
                ]
            ]
        ]);
        
        $product = $result->product;      
        foreach($product->variants as $variant)
        {
            // GET https://{store}.myshopify.com/admin/products/{product_id}/variants/{variant_id}.json
            $result2 = $shopify->call([ 
                'METHOD'    => 'GET', 
                'URL'       => '/admin/products/'. $id . '/variants/' . $variant->id . '.json',            
            ]);

            $variant = $result2->variant;
            $inventoryItemId = $variant->inventory_item_id;

            // GET https://{store}.myshopify.com/admin/inventory_levels.json?inventory_item_ids={inventory_item_id}
            $result3 = $shopify->call([ 
                'METHOD'    => 'GET', 
                'URL'       => '/admin/inventory_levels.json?inventory_item_ids=' . $inventoryItemId,            
            ]);
            $inventoryLevels = $result3->inventory_levels;
            
            foreach ($inventoryLevels as $inventoryLevel)
            {
                // POST https://{store}.myshopify.com/admin/inventory_levels/set.json
                // POST https://{store}.myshopify.com/admin/inventory_levels/adjust.json
                $result4 = $shopify->call([ 
                'METHOD'    => 'POST', 
                'URL'       => '/admin/inventory_levels/set.json',
                'DATA'      => [
                    "location_id" => $inventoryLevel->location_id,
                    'inventory_item_id' => $inventoryItemId,
                    'available' => $request->available 
                    ]            
                ]);
               $product4 = $result4->inventory_level;
            }    
        };                         
        //return view('shop.edit')->with('product', $product)->with('inventoryLevels', $inventoryLevels)->with('success', 'Product modified!');
        //return back()->withInput()->with('success', 'Product modified!');
        return redirect()->action('ShopifyController@index')->with('success', 'Product modified!');         
    }
}
