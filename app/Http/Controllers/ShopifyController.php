<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use GuzzleHttp\Client;

class ShopifyController extends Controller
{
    public function index()
    {
        // Set variables for our request
        $scopes = "read_orders,write_products,write_inventory";

        // Build install/approval URL to redirect to
        $install_url = "https://" . env('SHOPIFY_DOMAIN') . "/admin/oauth/authorize?client_id=" . env('SHOPIFY_KEY') . "&scope=" . $scopes . "&redirect_uri=" . urlencode(env('SHOPIFY_REDIRECT'));

        // Redirect
        header("Location: " . $install_url);
        die();
    }

    public function callback()
    {
        $params = $_GET; // Retrieve all request parameters
        $hmac = $_GET['hmac']; // Retrieve HMAC request parameter
        $params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
        ksort($params); // Sort params lexographically

        // Compute SHA256 digest
        $computed_hmac = hash_hmac('sha256', http_build_query($params), env('SHOPIFY_SECRET'));

        // Use hmac data to check that the response is from Shopify or not
        if (hash_equals($hmac, $computed_hmac)) {
            // Set variables for our request
            $query = array(
                "client_id" => env('SHOPIFY_KEY'), // Your API key
                "client_secret" => env('SHOPIFY_SECRET'), // Your app credentials (secret key)
                "code" => $params['code'] // Grab the access key from the URL
            );
            // Generate access token URL
            $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";
            // Configure curl client and execute request
            // Création d'une ressource cURL
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // Definition de l'url
            curl_setopt($ch, CURLOPT_URL, $access_token_url);
            // Définition des autres options appropriées
            curl_setopt($ch, CURLOPT_POST, count($query));
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
            // recuperation de l'url et passage au navigateur
            $result = curl_exec($ch);
            // fermeture de la ressource cURL et liberation des ressources systemes
            curl_close($ch);
            
            // Store the access token
            $result = json_decode($result, true);
            $access_token = $result['access_token'];
            //return view('shop.index');
           return redirect()->action('ShopifyController@getAllProduct', $access_token);
        } else {
            // NOT VALIDATED – Someone is trying to be shady!
            die('This request is NOT from Shopify!');
        }
    }

    public function getAllProduct($access_token)
    {

        // Set variables for our request
        $shop = env('SHOPIFY_DOMAIN');
        $token = $access_token;
        $query = array(
            "Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
        );

        // Run API call to get all products
        $products = shopify_call($token, $shop, "/admin/products.json?page=1", array(), 'GET');
        // Get response
        $products = $products['response'];      
        //$products = html_entity_decode($products);
        
        $product2ss = json_decode($products);

        return view('shop.index')->with('product2ss', $product2ss)->with('token', $token);       
    }

    public function edit($id, $access_token)
    {
        $shop = env('SHOPIFY_DOMAIN');
        $token = $access_token;
        $query = array(
            "Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
        );

        // Run API call to get one product
        $result = shopify_call($token, $shop, '/admin/products/'. $id .'.json', array(), 'GET');
        // Get response
        $product = $result['response'];      
        //$products = html_entity_decode($products);
        
        $product = json_decode($product);

        foreach($product as $productdet)
        {
            foreach($productdet->variants as $variant)
            {
                // GET https://{store}.myshopify.com/admin/products/{product_id}/variants/{variant_id}.json
                $result = shopify_call($token, $shop, '/admin/products/'. $id . '/variants/' . $variant->id . '.json', array(), 'GET');
                // Get response
                $variant = $result['response'];      
                //$products = html_entity_decode($products);
                
                $variant = json_decode($variant);

                foreach($variant as $variantdet)
                {
                    $inventoryItemId = $variantdet->inventory_item_id;

                    // GET https://{store}.myshopify.com/admin/inventory_levels.json?inventory_item_ids={inventory_item_id}
                    $result3 = shopify_call($token, $shop, '/admin/inventory_levels.json?inventory_item_ids=' . $inventoryItemId , array(), 'GET');
                    // Get response
                    $inventoryLevels = $result3['response'];
                    $inventoryLevels = json_decode($inventoryLevels);                        
                    return view('shop.edit')->with('product', $product)->with('inventoryLevels', $inventoryLevels)->with('access_token', $access_token);
                }                 
            };
        }        
    }

    public function update(Request $request, $id, $access_token)
    {  
        // PUT /admin/products/#{product_id}.json
        $shop = env('SHOPIFY_DOMAIN');
        $token = $access_token;
        $query = array(
            "Content-type" => "application/json" // Tell Shopify that we're expecting a response in JSON format
        );

        // Modify product data
        $modify_data = array(
            "product" => array(
                "title" => $request->title
            )
        );

        // Run API call to modify the product
        $modified_product = shopify_call($token, $shop, '/admin/products/'. $id .'.json', $modify_data, 'PUT');
        // Get response
        $product = $modified_product['response'];      
        //$products = html_entity_decode($products);
        
        $product = json_decode($product);

        foreach($product as $productdet)
        {
            foreach($productdet->variants as $variant)
            {
                // GET https://{store}.myshopify.com/admin/products/{product_id}/variants/{variant_id}.json
                $result = shopify_call($token, $shop, '/admin/products/'. $id . '/variants/' . $variant->id . '.json', array(), 'GET');
                // Get response
                $variant = $result['response'];      
                //$products = html_entity_decode($products);
                
                $variant = json_decode($variant);

                foreach($variant as $variantdet)
                {
                    $inventoryItemId = $variantdet->inventory_item_id;

                    // GET https://{store}.myshopify.com/admin/inventory_levels.json?inventory_item_ids={inventory_item_id}
                    $result3 = shopify_call($token, $shop, '/admin/inventory_levels.json?inventory_item_ids=' . $inventoryItemId , array(), 'GET');
                    // Get response
                    $inventoryLevels = $result3['response'];
                    $inventoryLevels = json_decode($inventoryLevels);
                    foreach ($inventoryLevels as $inventoryLevel)
                    {
                        foreach ($inventoryLevel as $inventoryLevel1)
                        {
                            $modify_level = array(
                                "inventory_item_id" => $inventoryLevel1->inventory_item_id,
                                "location_id" => $inventoryLevel1->location_id,                                    
                                "available" => $request->available
                            );
                            // POST https://{store}.myshopify.com/admin/inventory_levels/set.json
                            // POST https://{store}.myshopify.com/admin/inventory_levels/adjust.json
                            // Run API call to modify the product
                            $modified_level = shopify_call($token, $shop, '/admin/inventory_levels/set.json', $modify_level, 'POST');
                            // Get response
                            $modifiedLevel = $modified_level['response'];      
                            //$products = html_entity_decode($products);
                            
                            $modifiedLevel = json_decode($modifiedLevel);                           
                        }                      
                    }
                }                 
            };
            return redirect()->action('ShopifyController@index')->with('success', 'Product modified!');
        }           
    }
}