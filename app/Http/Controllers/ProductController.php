<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\User;
use App\Services\Shopify\REST\ProductService;
use App\Services\Shopify\REST\ShopService;
use App\Models\Product;






class ProductController extends Controller
{

    public function index()
    {
        $user = User::find(1);
        $productService = new ProductService($user);
        $response = $productService->getAllProducts();
        return $response;
    }

    public function show($productId)
    {
        $user = User::find(1);
        $productService = new ProductService($user);
        $response = $productService->getProductById($productId);
        return $response;
    }

    public function getProductsFromShopify()
    {
        $user = User::find(1);
        $productService = new ProductService($user);
        $response = $productService->getAllProductsFromShopify();
        return $response;
    }

    public function saveAll()
    {
        $products = $this->getProductsFromShopify();
        $user = User::find(1);
        $userId = $user['id'];
        foreach ($products as $product) {
            Product::updateOrCreate([
                'product_id' => $product['id'],
                'user_id' => $userId,
                'image_src' => isset($product['image']['src']) ? $product['image']['src'] : 'no_image',
                'title' => $product['title']
            ]);
        }
    }

    public function getActiveProducts(Request $request) //lay ra nhung san pham co the pre-order
    {
        $page = $request->input('page');
        $perPage = 10;
        $offset = ($page - 1) * $perPage;
        $products = Product::where('status', 1)->offset($offset)->limit($perPage)->get();
        return $products;
        // $products = Product::where('status', 1)->paginate(10); //status = 1 la active = 0 la inactive
        // return $products;
    }
}
