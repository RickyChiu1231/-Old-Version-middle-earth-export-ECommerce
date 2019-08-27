<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\OrderItem;
use App\Models\Category;

class ProductsController extends Controller
{
    public function index(Request $request)
    {
        // Create a query constructor
        $builder = Product::query()->where('on_sale', true);
        // Determine if the search parameter is submitted, and if so, assign it to the $search variable.
        // Search parameter is used to blur search products
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // Fuzzy search for product titles, product listings, SKU titles, SKU descriptions
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // Determine if there is a submit order parameter, if so, assign it to the $order variable
        // The order parameter is used to control the collation of the item.
        if ($order = $request->input('order', '')) {
            // Whether it is ending with _asc or _desc
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // If the beginning of the string is one of these 3 strings, it means a legal sort value
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // Construct a sorting parameter based on the passed sort value
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate(16);
        $category1 = Category::find(1);
        $category2 = Category::find(2);
        $category3 = Category::find(3);


        return view('products.index', [
            'products' => $products,
            'category1' => $category1,
            'category2' => $category2,
            'category3' => $category3,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }


    public function indexmilk(Request $request)
    {
        // Create a query constructor
        $builder = Product::query()->where('categoryid', 1);
        // Determine if the search parameter is submitted, and if so, assign it to the $search variable.
        // Search parameter is used to blur search products
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // Fuzzy search for product titles, product listings, SKU titles, SKU descriptions
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // Determine if there is a submit order parameter, if so, assign it to the $order variable
        // The order parameter is used to control the collation of the item.
        if ($order = $request->input('order', '')) {
            // Whether it is ending with _asc or _desc
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // If the beginning of the string is one of these 3 strings, it means a legal sort value
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // Construct a sorting parameter based on the passed sort value
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate();



        return view('products.indexmilk', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }


    public function indexhealth(Request $request)
    {
        // Create a query constructor
        $builder = Product::query()->where('categoryid', 2);
        // Determine if the search parameter is submitted, and if so, assign it to the $search variable.
        // Search parameter is used to blur search products
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // Fuzzy search for product titles, product listings, SKU titles, SKU descriptions
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // Determine if there is a submit order parameter, if so, assign it to the $order variable
        // The order parameter is used to control the collation of the item.
        if ($order = $request->input('order', '')) {
            // Whether it is ending with _asc or _desc
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // If the beginning of the string is one of these 3 strings, it means a legal sort value
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // Construct a sorting parameter based on the passed sort value
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate();



        return view('products.indexhealth', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }



    public function indexothers(Request $request)
    {
        // Create a query constructor
        $builder = Product::query()->where('categoryid', 3);
        // Determine if the search parameter is submitted, and if so, assign it to the $search variable.
        // Search parameter is used to blur search products
        if ($search = $request->input('search', '')) {
            $like = '%'.$search.'%';
            // Fuzzy search for product titles, product listings, SKU titles, SKU descriptions
            $builder->where(function ($query) use ($like) {
                $query->where('title', 'like', $like)
                    ->orWhere('description', 'like', $like)
                    ->orWhereHas('skus', function ($query) use ($like) {
                        $query->where('title', 'like', $like)
                            ->orWhere('description', 'like', $like);
                    });
            });
        }

        // Determine if there is a submit order parameter, if so, assign it to the $order variable
        // The order parameter is used to control the collation of the item.
        if ($order = $request->input('order', '')) {
            // Whether it is ending with _asc or _desc
            if (preg_match('/^(.+)_(asc|desc)$/', $order, $m)) {
                // If the beginning of the string is one of these 3 strings, it means a legal sort value
                if (in_array($m[1], ['price', 'sold_count', 'rating'])) {
                    // Construct a sorting parameter based on the passed sort value
                    $builder->orderBy($m[1], $m[2]);
                }
            }
        }

        $products = $builder->paginate();



        return view('products.indexothers', [
            'products' => $products,
            'filters'  => [
                'search' => $search,
                'order'  => $order,
            ],
        ]);
    }




    public function show(Product $product, Request $request)
    {
        if (!$product->on_sale) {
            throw new InvalidRequestException('Product no on sale');
        }

        $favored = false;
        // The user returns null when not logged in, and the corresponding user object is returned when logged in.
        if($user = $request->user()) {
            // Search for items with the current product id from the items that the user has already bookmarked
            // The boolval() function is used to convert a value to a boolean
            $favored = boolval($user->favoriteProducts()->find($product->id));
        }

        $reviews = OrderItem::query()
            ->with(['order.user', 'productSku']) // Preload association
            ->where('product_id', $product->id)
            ->whereNotNull('reviewed_at') // Filter out the evaluated
            ->orderBy('reviewed_at', 'desc') // Reversed by evaluation time
            ->limit(10) // get 10 of them
            ->get();

        return view('products.show', [
            'product' => $product,
            'favored' => $favored,
            'reviews' => $reviews
        ]);
    }


    public function favor(Product $product, Request $request)
    {
        $user = $request->user();
        if ($user->favoriteProducts()->find($product->id)) {
            return [];
        }

        $user->favoriteProducts()->attach($product);

        return [];
    }

    public function disfavor(Product $product, Request $request)
    {
        $user = $request->user();
        $user->favoriteProducts()->detach($product);

        return [];
    }

    public function favorites(Request $request)
    {
        $products = $request->user()->favoriteProducts()->paginate(16);

        return view('products.favorites', ['products' => $products]);
    }
}
