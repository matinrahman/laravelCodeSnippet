<?php

/**
 * Created by PhpStorm.
 * Project: themallbd
 * File: ProductModel.php
 * User: rajib
 * Date: 1/14/16
 * Time: 3:29 PM
 */

namespace App\Model;

use App\Model\DataModel\Price;
use App\Model\DataModel\Product;
use App\Model\DataModel\ProductMobile;
use App\Model\DataModel\ProductCategories;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mockery\CountValidator\Exception;

class ProductModel extends BaseMallBDModel {

    protected $primaryKey = 'id';
    protected $table = 'products';

    public function productShop() {
        return $this->hasOne("App\Model\ShopModel", "id", "shop_id"); //(modelName,foreignKey,LocalKey)
    }

    public function productSupplier() {
        return $this->hasOne("App\Model\SupplierModel", "id", "supplier_id");
    }

    public function productManufacturer() {
        return $this->hasOne("App\Model\ManufacturerModel", "id", "manufacture_id");
    }

    public function productWareHouse() {
        return $this->hasOne("App\Model\WareHouseModel", "id", "warehouse_id");
    }

    public function productPrices() {
        return $this->hasMany("App\Model\ProductPriceModel", "product_id", "id");
    }

    public function productImages() {
        return $this->hasMany("App\Model\ProductPictureModel", "product_id", "id");
    }

    public function productDiscount() {
        return $this->belongsToMany("App\Model\DiscountModel", "product_discounts", "product_id", "discount_id");
    }

    public function productTags() {
        return $this->belongsToMany("App\Model\TagModel", "product_tags", "product_id", "tag_id");
    }

    public function productQuantity() {
        return $this->hasMany("App\Model\ProductQuantityModel", "product_id", "id");
    }

    public function productAttributes() {
        return $this->belongsToMany("App\Model\AttributesModel", "product_attributes", "product_id", "attribute_id");
    }

    public function productAttributesCombination() {
        return $this->hasMany("App\Model\ProductAttributesCombinationModel", "product_id", "id");
    }

    public function productCategories() {
        return $this->belongsToMany("App\Model\CategoryModel", "product_categories", "product_id", "category_id");
    }

    public function createdBy() {
        return $this->hasOne("App\Model\UserModel", "id", "created_by");
    }

    public function setId($id) {
        $this->setObj($id);

        if (!$this->basicValidation()) {
            $errorObj = new ErrorObj();

            $errorObj->params = "id";
            $errorObj->msg = "id is empty";

            array_push($this->errorManager->errorObj, $errorObj);
            return false;
        }
        if (!is_numeric($this->getObj())) {
            $errorObj = new ErrorObj();

            $errorObj->params = "id";
            $errorObj->msg = "id int/float required";

            array_push($this->errorManager->errorObj, $errorObj);
            return false;
        }
        $this->id = (int) $this->getObj();
        return true;
    }

    public function getById() {
        $dataRow = $this->with("createdBy", "productShop")->where("id", "=", $this->id)->get()->first();
        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $product = new Product();
        //$product->setCurrency(self::$currency);
        $product->castMeFromObj($dataRow);
        if (in_array($product->id, $wishListedProducts)) {
            $product->isWished = true;
        }

//        if($products==null)
//        {
//            return null;
//        }
//        $productList = [];
//        foreach($products as $p)
//        {
//            $product = new Product();
//            $product->castMeFromObj($p);
//            array_push($productList,$product);
//        }
        return $product;
    }

    public function getAllProducts($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")->where("shop_id", "=", $shop_id)->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $productList = [];

        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);


            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllProductsMobile($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")->where("shop_id", "=", $shop_id)->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }

        $productList = [];

        foreach ($products as $p) {
            $product = new ProductMobile();
            $product->castMeFromObj($p);


            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllProductsForCompare($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")->where("shop_id", "=", $shop_id)->where("status", "=", "Active")
                        ->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }

        $productList = [];

        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);


            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllProductsBySearch($shop_id, $keyword) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                        ->where("shop_id", "=", $shop_id)
                        ->where("product_title", "LIKE", '%' . $keyword . '%')
                        ->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }

        $productList = [];

        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);


            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllProductsBySearchForSuggestion($shop_id, $keyword) {
        $products = ProductModel::leftJoin('product_images', 'products.id', '=', 'product_images.product_id')
                        ->where("product_title", "LIKE", '%' . $keyword . '%')
//                        ->where("product_images.cover", "=", 1)
                        ->where("status", "=", "Active")
                        ->select('products.id', 'products.product_title', 'product_images.image_name')
                        ->limit($this->customLimit)
                        ->groupBy('id')
                        ->orderBy('id', 'DESC')->get();
        return $products;
    }

    public function getFeaturedProducts($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)
                        ->where("is_featured", "=", "yes")
                        ->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getFeaturedProductsMobile($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)
                        ->where("is_featured", "=", "yes")
                        ->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new ProductMobile();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getNewProducts($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)
                        ->where("status", "=", "Active")
                        ->where('products.created_on', '>', $this->NewProductOldDate)
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);


        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getNewProductsMobile($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)
                        ->where("status", "=", "Active")
                        ->where('products.created_on', '>', $this->NewProductOldDate)
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);


        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new ProductMobile();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getSpecialProducts($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)->where("discount", ">", 0)
                        ->whereDate("discount_start_date", "<=", date("Y-m-d H:i:s"))
                        ->whereDate("discount_end_date", ">=", date("Y-m-d H:i:s"))
                        ->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getSpecialProductsMobile($shop_id) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")->where("shop_id", "=", $shop_id)->where("discount", ">", 0)
                        ->whereDate("discount_start_date", "<=", date("Y-m-d H:i:s"))
                        ->whereDate("discount_end_date", ">=", date("Y-m-d H:i:s"))
                        ->where("status", "=", "Active")
                        ->limit($this->customLimit)
                        ->offset($this->customLimit * $this->customOffset)->orderBy('id', 'DESC')->get();



        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $productList = [];
        foreach ($products as $p) {
            $product = new ProductMobile();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllProductByCategory($categoryList, $orderBy = 0) {
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->whereHas('productCategories', function($q) use($categoryList) {
                    $q->whereIn('category_id', $categoryList);
                })
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId)
                ->groupBy("products.id");
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $products = $queryObj->get();

        if (empty($products)) {
            return null;
        }


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $productDiscountModel = new ProductDiscountModel();

        $discountData = $productDiscountModel->getProductDiscountByBetweenStartAndEndDate();


        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMe($p);

            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            $this->initiateDiscount($product, $discountData);
            array_push($productList, $product);
        }

        return $productList;
    }

    public function getAllProductCountByCategory($categoryList) {

        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->whereHas('productCategories', function($q) use($categoryList) {
                    $q->whereIn('category_id', $categoryList);
                })
                ->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)
                ->get();



        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $productDiscountModel = new ProductDiscountModel();

        $discountData = $productDiscountModel->getProductDiscountByBetweenStartAndEndDate();


        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMe($p);

            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }

            $this->initiateDiscount($product, $discountData);
            array_push($productList, $product);
        }

        return count($productList);
    }

    public function getAllProductByMatchString($matchString, $orderBy = 0) {
        $matchString = trim($matchString);
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where('product_title', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId)
                ->groupBy("products.id");
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $products = $queryObj->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if (empty($products)) {
            return null;
        }

        $productList = [];
        foreach ($products as $p) {
            $prod = new Product();
            //$prod->setCurrency(self::$currency);
            $prod->castMe($p);
            if (in_array($prod->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($productList, $prod);
        }

        return $productList;
    }

    public function getSearchedProductCount($matchString) {
        $matchString = trim($matchString);
        $productCount = ProductModel::where('product_title', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)
                ->count();


        return $productCount;
    }

    public function getAllProductByTags($matchString, $orderBy = 0) {
        $matchString = trim($matchString);
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('product_tags', 'products.id', '=', 'product_tags.product_id')
                ->leftJoin('tags', 'tags.id', '=', 'product_tags.tag_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where('tags.tag_name', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId)
                ->groupBy("products.id");
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $products = $queryObj->get();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if (empty($products)) {
            return null;
        }

        $productList = [];
        foreach ($products as $p) {
            $prod = new Product();
            $prod->castMe($p);
            if (in_array($prod->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($productList, $prod);
        }

        return $productList;
    }

    public function getAllProductByTagsCount($matchString, $orderBy = 0) {
        $matchString = trim($matchString);
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('product_tags', 'products.id', '=', 'product_tags.product_id')
                ->leftJoin('tags', 'tags.id', '=', 'product_tags.tag_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where('tags.tag_name', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId)
                ->groupBy("products.id");
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $products = $queryObj->get();

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if (empty($products)) {
            return null;
        }

        $productList = [];
        foreach ($products as $p) {
            $prod = new Product();
            $prod->castMe($p);
            if (in_array($prod->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($productList, $prod);
        }

        return count($productList);
    }

    public function getAllProductsByTitle($keywords, $shop_id) {
        $products = ProductModel::query();
        $keywords = explode(',', $keywords);

        if (is_array($keywords) || is_object($keywords))
            foreach ($keywords as $word) {
                $products->orWhere('product_title', 'LIKE', '%' . $word . '%');
            }
        $products = $products->with("productShop", "productSupplier", "productManufacturer", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories")
                        ->where('shop_id', '=', $shop_id)->where("status", "=", "Active")->distinct()->get();
        return $products;
    }

    public function countAllProductOfThisCategory($categoryList) {
        $products = ProductModel::whereHas('productCategories', function($q) use($categoryList) {
                    $q->whereIn('category_id', $categoryList);
                })->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)->get();

        return count($products);
    }

    public function getMinMaxPriceByCategoryIdList($categoryIdList) {
        $manufacturers = [];
        $data = DB::table('product_prices')
                        ->select(DB::raw('max(retail_price) as maxPrice,min(retail_price) as minPrice'))
                        ->join('product_categories', 'product_categories.product_id', '=', 'product_prices.product_id')
                        ->whereIn('product_categories.category_id', $categoryIdList)->get();

        $price = [];
        $price['min'] = 0;
        $price['max'] = 0;
        foreach ($data as $rowData) {
            $price['min'] = doubleval($rowData->minPrice);
            $price['max'] = doubleval($rowData->maxPrice);
        }

        return $price;
    }

    public function getProductBySearch($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {

        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id")
                ->whereHas('productCategories', function($q) use($productCategoryList) {

            $q->whereIn('category_id', $productCategoryList);
        });
        if (count($productAttrValueList) > 0) {
            $queryObj->whereHas('productAttributesCombination', function($q) use($productAttrValueList) {

                $q->whereIn('product_attributes_combinations.attribute_value_id', $productAttrValueList);
            });
        }
        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {
            $prod = new Product();
            $prod->castMeFromObj($sp);
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getProductCountBySearch($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {

        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id")
                ->whereHas('productCategories', function($q) use($productCategoryList) {

            $q->whereIn('category_id', $productCategoryList);
        });
        if (count($productAttrValueList) > 0) {
            $queryObj->whereHas('productAttributesCombination', function($q) use($productAttrValueList) {

                $q->whereIn('product_attributes_combinations.attribute_value_id', $productAttrValueList);
            });
        }
        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();


        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {
            $prod = new Product();
            $prod->castMeFromObj($sp);
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function old_getProductBySearch($decodedCategory, $decodedAttributes) {
        $productCategory = $decodedCategory;
        $productAttrValue = $decodedAttributes;

        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->distinct()
                ->join('product_categories', 'product_categories.product_id', '=', 'products.id')
                ->join('product_attributes_combinations', 'product_attributes_combinations.product_id', '=', 'product_categories.product_id')
                ->whereIn('product_categories.category_id', $productCategory);

        if (count($productAttrValue) > 0) {
            $queryObj = $queryObj->whereIn('product_attributes_combinations.attribute_value_id', $productAttrValue);
        }

        $searchedProducts = $queryObj->get(['products.id', 'products.product_code']);

        if (empty($searchedProducts)) {
            return null;
        }

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {
            $prod = new Product();
            $prod->castMeFromObj($sp);
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getProductByManufacturerId($id) {
        $products = $this->where('manufacture_id', '=', $id)->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)
                        ->limit($this->customLimit)->offset($this->customLimit * $this->customOffset)->get();
        $productList = [];
        foreach ($products as $p) {
            $product = new ProductMobile();
            $product->castMeFromObj($p);
            array_push($productList, $product);
        }
        return $productList;

    }

    public function getCategoryById($productId) {
        $productCategoryId = $this->join('product_categories', 'products.id', '=', 'product_categories.product_id')
                        ->select('product_categories.category_id')
                        ->where("products.status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId)->first();
        return $productCategoryId;
    }

    public function getRelatedProducts($category_id, $id = null) {

        $products = ProductModel::with("productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")->whereHas('productCategories', function($q) use($category_id) {
                    $q->where('category_id', $category_id);
                })->where('id', '!=', $id)->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)->limit($this->customLimit)->offset($this->customLimit * $this->customOffset)->get();

        $customerWishListModel = new CustomerWishListModel();
        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $productList = [];
        foreach ($products as $p) {
            $product = new Product();
            $product->castMeFromObj($p);
            if (in_array($product->id, $wishListedProducts)) {
                $product->isWished = true;
            }
            array_push($productList, $product);
        }
        return $productList;
    }

   

    public function findDistinctCategory($keyword) {

        $productId = ProductModel::where("product_title", "LIKE", '%' . $keyword . '%')
                        ->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)
                        ->select("id")->get();

        $categories = ProductCategoriesModel::whereIn("product_id", $productId)
                        ->distinct()->get();

        $categoryList = [];
        foreach ($categories as $category) {
            $productCategory = new ProductCategories();
            $productCategory->castMe($category);
            array_push($categoryList, $productCategory);
        }
        return $categoryList;
    }

    public function findCategoryIdFromKeyWord($keyword) {
        
    }

    public function initiateDiscount(&$product, $discountData) {
        $discountPrice = 0;

        try {
            switch ($discountData['category_id']) {
                case 'ALL':
                    $product->previousPrice = (float) $product->prices[0]->retailPrice;
                    $discountPrice = ($discountData['productDiscountDiscount']['discount_type'] == "Fixed") ?
                            (float) $discountData['productDiscountDiscount']['discount_amount'] : ($product->prices[0]->retailPrice * (float) $discountData['productDiscountDiscount']['discount_amount']) / 100;
                    $product->prices[0]->retailPrice = $product->prices[0]->retailPrice - $discountPrice;
            }
        } catch (Exception $ex) {
            
        }
    }

    public function decreaseQuantity($quantity) {

        $product = $this->where('id', '=', $this->id)->first();

        $prevQty = $product->product_quantity;
        $prevQty = $prevQty - $quantity;
        if ($prevQty < 0) {
            return FALSE;
        }
        $product->product_quantity = $prevQty;

        if ($product->save()) {
            $this->updateParentAndChildQuantity($this->id, $prevQty);
            return TRUE;
        } else {
            return FALSE;
        }
    }

    public function increaseQuantity($quantity) {

        $product = $this->where('id', '=', $this->id)->first();

        $prevQty = $product->product_quantity;
        $prevQty = $prevQty + $quantity;
        if ($prevQty < 0) {
            return FALSE;
        }
        $product->product_quantity = $prevQty;

        if ($product->save()) {
            $this->updateParentAndChildQuantity($this->id, $prevQty);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    
    public function updateParentAndChildQuantity($product_id, $quantity) {
        $currentProductData = ProductModel::find($product_id);
        if ($currentProductData->parent_id != 0) {
            $parentProductData = ProductModel::find($currentProductData['parent_id']);
            $parentProductData->product_quantity = $quantity;
            $parentProductData->save();
        }

        $childProduct = ProductModel::where('parent_id', $product_id)->get();
        if (count($childProduct) != 0) {
            $childProductData = ProductModel::find($childProduct[0]['id']);
            $childProductData->product_quantity = $quantity;
            $childProductData->save();
        }
    }

    public function productQuantityCheck($quantity) {

        $product = $this->where('id', '=', $this->id)->first();

        $prevQty = $product->product_quantity;
        $prevQty = $prevQty - $quantity;
        if ($prevQty < 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function getProductById($productId) {
        $products = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                        ->where("id", "=", $productId)
                        ->where("status", "=", "Active")
                        ->limit(3)
                        ->offset(0)->orderBy('id', 'DESC')->first();


        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if ($products == null) {
            return null;
        }
        $product = new Product();
        $product->castMeFromObj($products);


        if (in_array($product->id, $wishListedProducts)) {
            $product->isWished = true;
        }


        return $product;
    }

    public function getBestSellerProductIdList() {

        $results = DB::table('order_products')
                ->select('product_id', DB::raw('SUM(product_quantity*package_quantity) AS c'))
                ->havingRaw(DB::raw('c > 1'))
                ->groupBy('product_id')
                ->orderByRaw('c desc')
                ->limit(3)
                ->offset(0)
                ->get();
        return $results;
    }

    public function getManufacturerProductBySearch($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy) {

        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where("products.shop_id", "=", $this->shopId);

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        $queryObj = $queryObj->groupBy("products.id");

        if ($manufacturer != NULL) {
            $queryObj = $queryObj->whereIn('manufacture_id', $manufacturer);
        }
        if ($manufacturer != NULL) {
            $queryObj = $queryObj->where('status', 'Active');
        }
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();


        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            //$prod->setCurrency(self::$currency);
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getManufacturerProductCountBySearch($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy) {
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where("products.shop_id", "=", $this->shopId);
        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        $queryObj = $queryObj->groupBy("products.id");

        if ($manufacturer != NULL) {
            $queryObj = $queryObj->whereIn('manufacture_id', $manufacturer);
        }
        if ($manufacturer != NULL) {
            $queryObj = $queryObj->where('status', 'Active');
        }
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getProductByKeyword($keyword) {
        $result = $this->where("status", "=", "Active")
                ->where("shop_id", "=", $this->shopId)
                ->where("product_title", "LIKE", '%' . $keyword . '%')
                ->get();

        $productList = [];

        foreach ($result as $r) {
            $product = new \stdClass();
            $product->id = $r->id;
            $product->code = $r->product_code;
            $product->url = $r->product_url;
            $product->title = $r->product_title;
            $product->product_title = strlen($r->product_description_mobile) > 20 ? substr($r->product_description_mobile, 0, 19) . '...' : $r->product_description_mobile;
            $product->product = TRUE;
            array_push($productList, $product);
        }
        return $productList;
    }

    public function getAllNewProducts($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {


        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->where('products.created_on', '>', $this->NewProductOldDate)
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {


            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getAllNewProductsCount($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {


        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->where('products.created_on', '>', $this->NewProductOldDate)
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getAllFeaturedProducts($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->where('products.is_featured', 'Yes')
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getAllFeaturedProductsCount($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->where('products.is_featured', 'Yes')
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));
//            });
        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getAllSpecialProducts($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->whereDate("discount_start_date", "<=", date("Y-m-d H:i:s"))
                ->whereDate("discount_end_date", ">=", date("Y-m-d H:i:s"))
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getAllSpecialProductsCount($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->distinct()
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->whereDate("discount_start_date", "<=", date("Y-m-d H:i:s"))
                ->whereDate("discount_end_date", ">=", date("Y-m-d H:i:s"))
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));
//            });
        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getAllBestSellerCheckByProductId($product_id) {
        $results = DB::table('order_products')
                ->select('product_id', DB::raw('SUM(product_quantity*package_quantity) AS c'))
                ->havingRaw(DB::raw('c > 10'))
                ->where('product_id', '=', $product_id)
                ->orderByRaw('c desc')
                ->get();
        if ($results == NULL)
            return FALSE;
        else
            return TRUE;
    }

    public function getAllBestSellerProductId() {

        $results = DB::table('order_products')
                ->select('product_id', DB::raw('SUM(product_quantity*package_quantity) AS c'))
                ->havingRaw(DB::raw('c > 10'))
                ->groupBy('product_id')
                ->orderByRaw('c desc')
                ->get();
        return $results;
    }

    public function getAllBestSellerProducts($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $bestSellerProductArray, $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->whereIn('products.id', $bestSellerProductArray)
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getAllBestSellerProductsCount($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $bestSellerProductArray, $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->whereIn('products.id', $bestSellerProductArray)
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getByProductCode($product_url, $product_code) {
        $dataRow = $this->with("createdBy", "productShop")->where("product_url", "=", $product_url)->where("product_code", "=", $product_code)->get()->first();
        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $product = new Product();
        $product->castMeFromObj($dataRow);
        if (in_array($product->id, $wishListedProducts)) {
            $product->isWished = true;
        }
        return $product;
    }

    public function getAllRecentViewProducts($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $recentProductArray, $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->whereIn('products.id', $recentProductArray)
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return $searchedProductList;
    }

    public function getAllRecentViewProductsCount($productCategoryList, $productAttrValueList, $minPrice = -1, $maxPrice = -1, $manufacturer = [], $recentProductArray, $orderBy = 0) {
        $queryObj = $this->with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity", "productAttributesCombination")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->whereIn('products.id', $recentProductArray)
                ->where('products.status', 'Active')
                ->where("products.shop_id", "=", $this->shopId)
                ->distinct()
                ->groupBy("products.id");
        if ($minPrice > -1 || $maxPrice > 0) {

            $queryObj->where(DB::raw('product_prices.retail_price - products.discount'), '>=', doubleval($minPrice))->where(DB::raw('product_prices.retail_price - products.discount'), '<=', doubleval($maxPrice));

        }
        if (count($productCategoryList) > 0) {
            $queryObj->whereHas('productCategories', function($q) use($productCategoryList) {

                $q->whereIn('category_id', $productCategoryList);
            });
        }

        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        $searchedProducts = $queryObj->get();

        if (empty($searchedProducts)) {
            return null;
        }

        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        $searchedProductList = [];
        foreach ($searchedProducts as $sp) {

            $prod = new Product();
            $prod->castMeFromObj($sp);
            if (in_array($sp->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($searchedProductList, $prod);
        }


        return count($searchedProductList);
    }

    public function getMinMaxPrice() {
        $manufacturers = [];
        $data = DB::table('product_prices')
                ->select(DB::raw('max(retail_price) as maxPrice,min(retail_price) as minPrice'))
                ->get();

        $price = [];
        $price['min'] = 0;
        $price['max'] = 0;
        foreach ($data as $rowData) {
            if (doubleval($rowData->minPrice) >= 0) {
                $price['min'] = doubleval($rowData->minPrice);
            }
            $price['max'] = doubleval($rowData->maxPrice);
        }

        return $price;
    }

    public function getAllProductByMatchStringManufacturerAndPrice($matchString, $minPrice, $maxPrice, $manufacturer, $orderBy = 0) {
        $matchString = trim($matchString);
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where('product_title', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId);


        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        $queryObj = $queryObj->groupBy("products.id");
        if ($orderBy == 1) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'asc');
        }
        if ($orderBy == 2) {
            $queryObj = $queryObj->orderBy(DB::raw('product_prices.retail_price - products.discount'), 'desc');
        }
        if ($orderBy == 3) {
            $queryObj = $queryObj->orderBy('product_title', 'asc');
        }
        if ($orderBy == 4) {
            $queryObj = $queryObj->orderBy('product_title', 'desc');
        }
        if ($orderBy == 5) {
            $queryObj = $queryObj->orderBy('product_quantity', 'desc');
        }
        if ($orderBy == 6) {
            $queryObj = $queryObj->orderBy('avg_rating', 'asc');
        }
        if ($orderBy == 7) {
            $queryObj = $queryObj->orderBy('avg_rating', 'desc');
        }
        if ($orderBy == 8) {
            $queryObj = $queryObj->orderByRaw('c desc');
        }
        if ($this->customLimit != -1) {
            $queryObj = $queryObj->limit($this->customLimit)->offset($this->customLimit * $this->customOffset);
        }
        $products = $queryObj->get();




        $customerWishListModel = new CustomerWishListModel();

        $wishListedProducts = $customerWishListModel->getWishListProductArray($this->currentUserId);

        if (empty($products)) {
            return null;
        }

        $productList = [];
        foreach ($products as $p) {
            $prod = new Product();
            $prod->castMe($p);
            if (in_array($prod->id, $wishListedProducts)) {
                $prod->isWished = true;
            }
            array_push($productList, $prod);
        }

        return $productList;
    }

    public function getAllProductByMatchStringManufacturerAndPriceCount($matchString, $minPrice, $maxPrice, $manufacturer) {
        $matchString = trim($matchString);
        $queryObj = ProductModel::with("productShop", "productSupplier", "productManufacturer", "productWareHouse", "productPrices", "productImages", "productDiscount", "productTags", "productAttributes", "productCategories", "productQuantity")
                ->select('products.*', 'product_prices.retail_price', DB::raw('SUM(order_products.product_quantity*order_products.package_quantity) AS c'))
                ->leftJoin('product_prices', 'products.id', '=', 'product_prices.product_id')
                ->leftJoin('order_products', 'products.id', '=', 'order_products.product_id')
                ->where('product_title', 'like', '%' . $matchString . '%')
                ->where("status", "=", "Active")
                ->where("products.shop_id", "=", $this->shopId);


        if (count($manufacturer) > 0) {
            $queryObj->whereHas('productManufacturer', function($q) use($manufacturer) {

                $q->whereIn('id', $manufacturer);
            });
        }

        if ($minPrice > -1 || $maxPrice > 0) {
            $queryObj->whereHas('productPrices', function ($q) use ($minPrice, $maxPrice) {

                $q->where('retail_price', '>=', doubleval($minPrice))->where('retail_price', '<=', doubleval($maxPrice));
            });
        }

        $queryObj = $queryObj->groupBy("products.id");

        $products = $queryObj->get();

        return count($products);
    }

}
