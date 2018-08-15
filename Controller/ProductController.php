<?php

/**
 * Created by PhpStorm.
 * Project: MallBDFront
 * File: ProductController.php
 * User: Matin
 * Date: 1/27/16
 * Time: 11:23 AM
 */

namespace App\Http\Controllers\Web;

use App\Http\Controllers\BaseMallBDController;
use App\Model\AttributesModel;
use App\Model\CategoryModel;
use App\Model\ManufacturerModel;
use App\Model\ProductCategoriesModel;
use App\Model\ProductModel;
use App\Model\PackageModel;
use App\Helper\Pagination;
use Illuminate\Http\Request;

class ProductController extends BaseMallBDController {

    public function getProductByCategory(Request $request, $title, $category, $category2 = null, $category3 = null) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = $category;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }

            try {

                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();

                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        }
        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


        $mainCategoryForReturningProduct = $category;
        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
        if (isset($category)) {
            $categoryObj = new CategoryModel();
            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
        }
        //check if 2nd category, as child of first category is defined
        if ($category2 != null) {
            $mainCategoryForReturningProduct = $category2;
        }

        //check if 3rd category, as child of second category is defined
        if ($category3 != null) {
            $mainCategoryForReturningProduct = $category3;
        }

        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }
        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 40;
        }

        $categoryObj = new CategoryModel();
        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);

        foreach ($finalCategoryForProducts as $cat) {
            array_push($categoryIdList, $cat->id);
            if (!empty($cat->childrens)) {
                foreach ($cat->childrens as $child) {
                    array_push($categoryIdList, $child->id);
                    if (!empty($child->childrens)) {
                        foreach ($child->childrens as $child2) {
                            array_push($categoryIdList, $child2->id);
                        }
                    }
                }
            }
        }

        if (empty($categoryIdList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data for this category";
            $this->pageData['subcategoryList'] = $subcategoryList;
            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
        if ($searchProduct) {
            // Attribute selection search  Wise product

            if ($priceValidation) {
                $productList = $productModel->getProductBySearch($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
                $totalProduct = $productModel->getProductCountBySearch($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList);
            } else {
                $productList = $productModel->getProductBySearch($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
                $totalProduct = $productModel->getProductCountBySearch($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList);
            }
        } else {
            // Category Wise product
            $productList = $productModel->getAllProductByCategory($categoryIdList, $orderBy);
            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
        }


//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
            $this->pageData['subcategoryList'] = $subcategoryList;
            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
        $this->pageData['subcategoryList'] = $subcategoryList;
        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

        $currentCategory = 0;
        if ($category3 != null) {
            $currentCategory = $category3;
        } elseif ($category2 != null) {
            $currentCategory = $category2;
        } else {
            $currentCategory = $category;
        }

        /* For Search selection  */

        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureByCategoryIdList();

        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

        if (($category != null) && ($category2 == null) && ($category3 == null)) {
            $result1 = CategoryModel::where("id", "=", $category)->first();
            array_push($categoryNameList, $result1);
            $this->pageData['categoryNameList'] = $categoryNameList;
        }

        if (($category != null) && ($category2 != null) && ($category3 == null)) {
            $categoryModel = new CategoryModel();
            $results = $categoryModel->getAllParentWithChildList($category2);
            foreach ($results as $result) {
                array_push($categoryNameList, $result);
                $this->pageData['categoryNameList'] = $categoryNameList;
            }
        }

        if (($category != null) && ($category2 != null) && ($category3 != null)) {
            $categoryModel = new CategoryModel();
            $results = $categoryModel->getAllParentWithChildList($category3);
            foreach ($results as $result) {
                array_push($categoryNameList, $result);
                $this->pageData['categoryNameList'] = $categoryNameList;
            }
        }

//        if (count($categoryNameList == 0)) {
//            $result = array();
//            array_push($categoryNameList, $result);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }

        return view("web.home.home2", $this->pageData);
    }

    public function getProductById($id) {

        $productModel = new ProductModel();
        $productModel->setId($id);
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $product = $productModel->getById();
        $this->pageData['product'] = $product;

        $categoryNameList = [];
        if (isset($product->categories[0]) && $product->categories[0] != NULL) {
            $categoryModel = new CategoryModel();
            $res = $categoryModel->getAllCategoryByProductId($id);
            $cat_id = 0;
            foreach ($res as $r) {
                if ($cat_id < $r->category_id) {
                    $cat_id = $r->category_id;
                }
            }
            if ($cat_id > 0) {
                $results = $categoryModel->getAllParentWithChildList($cat_id);
                if (sizeof($results) > 0) {
                    foreach ($results as $result) {
                        array_push($categoryNameList, $result);
                        $this->pageData['categoryNameList'] = $categoryNameList;
                    }
                }
            }
        }

        $productModel->setCurrentUserId($this->appCredential->id);
        $productModel->setCustomOffset(0);
        $productModel->setCustomLimit(6);

        $relatedProducts = array();
        if (count($product->categories) > 0) {
            $relatedProducts = $productModel->getRelatedProducts($product->categories[0]->id, $product->id);
        }

        //return $relatedProducts;
        //return $relatedProducts[0]->product->title;
        $this->pageData['relatedProducts'] = $relatedProducts;
        //return $relatedProducts;


        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        return view("web.product.details", $this->pageData);
    }

    public function compareProducts(Request $request, $product_id) {
        $productModel = new ProductModel();
        $selectedProductCategories = ProductCategoriesModel::where('product_id', $product_id)->get();
        $selectedCatagoryArray = array();
        foreach ($selectedProductCategories as $cat):
            array_push($selectedCatagoryArray, $cat['category_id']);
        endforeach;

        $similarProducts = ProductCategoriesModel::whereIn('category_id', $selectedCatagoryArray)
                ->where('product_id', '!=', $product_id)
                ->groupBy('product_id')
                ->get();
        $similarProductsArray = array();
        foreach ($similarProducts as $prod):
            array_push($similarProductsArray, $prod['product_id']);
        endforeach;
//        
        $productsFiltered = ProductModel::with('productManufacturer', 'productPrices', 'productImages')
                ->whereIn('id', $similarProductsArray)
                ->where('status', 'Active')
                ->where("shop_id", "=", $this->shopId)
                ->orderBy('avg_rating', 'asc')
                ->get();

        $selectedProductDetails = ProductModel::with('productManufacturer', 'productPrices', 'productImages')
                ->where('id', $product_id)
                ->where("shop_id", "=", $this->shopId)
                ->get();

        $allProducts = ProductModel::with('productManufacturer', 'productPrices', 'productImages')
                ->where('status', 'Active')
                ->where("shop_id", "=", $this->shopId)
                ->get();
        $limit = 5;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = 0;
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $productList = $productModel->getAllProductsForCompare($this->shopId);
        $this->pageData['selectedProductDetails'] = $selectedProductDetails;
        $this->pageData['similarProducts'] = $productsFiltered;
        $this->pageData['allProducts'] = $allProducts;
        $this->pageData['productList'] = $productList;
        return view("web.compare.compareProduct", $this->pageData);
    }

    public function getProductByManufacturer(Request $request) {
//        die('pagol');

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = TRUE;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }

            try {

                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();

                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $selectedManufacturersIdList = [];
            $searchProduct = true;
            $priceValidation = false;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);


        $productList = [];
        if ($searchProduct) {
            // Attribute selection search  Wise product

            if ($priceValidation) {
                $productList = $productModel->getManufacturerProductBySearch($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
                $totalProduct = $productModel->getManufacturerProductCountBySearch($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
            } else {
                $productList = $productModel->getManufacturerProductBySearch($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
                $totalProduct = $productModel->getManufacturerProductCountBySearch($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
            }
        }
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPrice();

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        return view("web.home.manufacturer", $this->pageData);
    }

    public function getNewProduct(Request $request) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {
                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();
                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $priceValidation = false;
            $selectedManufacturersIdList = [];
            $searchProduct = true;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
//        if ($searchProduct) {
        // Attribute selection search  Wise product

        if ($priceValidation) {
            $productList = $productModel->getAllNewProducts($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllNewProductsCount($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
        } else {
            $productList = $productModel->getAllNewProducts($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllNewProductsCount($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
        }
//        } 
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

//        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);
        $price = [];
        $price['min'] = 0;
        $price['max'] = 5000;
        $this->pageData['minMaxPrice'] = $price;

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        $this->pageData['categoryNameList'] = array();
        $this->pageData['subcategoryList'] = array();
        $this->pageData['selectedCategory'] = array();

        $this->pageData['pagename'] = 'new';
        return view("web.home.newproducts", $this->pageData);
    }

    public function getFeaturedProduct(Request $request) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {
                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();
                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $priceValidation = false;
            $selectedManufacturersIdList = [];
            $searchProduct = true;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
//        if ($searchProduct) {
        // Attribute selection search  Wise product

        if ($priceValidation) {
            $productList = $productModel->getAllFeaturedProducts($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllFeaturedProductsCount($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
        } else {
            $productList = $productModel->getAllFeaturedProducts($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllFeaturedProductsCount($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
        }
//        } 
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }
        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

//        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);
        $price = [];
        $price['min'] = 0;
        $price['max'] = 5000;
        $this->pageData['minMaxPrice'] = $price;

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        $this->pageData['categoryNameList'] = array();
        $this->pageData['subcategoryList'] = array();
        $this->pageData['selectedCategory'] = array();

        $this->pageData['pagename'] = 'featured';
        return view("web.home.featuredproducts", $this->pageData);
    }

    public function getSpecialProduct(Request $request) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {
                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();
                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $priceValidation = false;
            $selectedManufacturersIdList = [];
            $searchProduct = true;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
//        if ($searchProduct) {
        // Attribute selection search  Wise product

        if ($priceValidation) {
            $productList = $productModel->getAllSpecialProducts($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllSpecialProductsCount($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $orderBy);
        } else {
            $productList = $productModel->getAllSpecialProducts($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
            $totalProduct = $productModel->getAllSpecialProductsCount($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $orderBy);
        }
//        } 
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

//        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);
        $price = [];
        $price['min'] = 0;
        $price['max'] = 5000;
        $this->pageData['minMaxPrice'] = $price;

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        $this->pageData['categoryNameList'] = array();
        $this->pageData['subcategoryList'] = array();
        $this->pageData['selectedCategory'] = array();

        $this->pageData['pagename'] = 'special';
        return view("web.home.specialproducts", $this->pageData);
    }

    public function getBestSellerProduct(Request $request) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {
                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();
                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $priceValidation = false;
            $selectedManufacturersIdList = [];
            $searchProduct = true;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
//        if ($searchProduct) {
        // Attribute selection search  Wise product
        $bestSellerList = $productModel->getAllBestSellerProductId();
        $bestSellerArray = array();
        foreach ($bestSellerList as $best):
            array_push($bestSellerArray, $best->product_id);
        endforeach;

        if ($priceValidation) {
            $productList = $productModel->getAllBestSellerProducts($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $bestSellerArray, $orderBy);
            $totalProduct = $productModel->getAllBestSellerProductsCount($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $bestSellerArray, $orderBy);
        } else {
            $productList = $productModel->getAllBestSellerProducts($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $bestSellerArray, $orderBy);
            $totalProduct = $productModel->getAllBestSellerProductsCount($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $bestSellerArray, $orderBy);
        }
//        } 
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);
//            echo '<pre>';
//            echo count($productList);
//            print_r($productList);
//            die;

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

//        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);
        $price = [];
        $price['min'] = 0;
        $price['max'] = 5000;
        $this->pageData['minMaxPrice'] = $price;

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        $this->pageData['categoryNameList'] = array();
        $this->pageData['subcategoryList'] = array();
        $this->pageData['selectedCategory'] = array();
        $this->pageData['pagename'] = 'bestseller';
        return view("web.home.bestsellerproducts", $this->pageData);
    }

    public function getProductByProductCode($product_url, $product_code) {
        $productModel = new ProductModel();
//        $productModel->setId($id);
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $product = $productModel->getByProductCode($product_url, $product_code);
        $product_current_id = $product->id;
        $this->pageData['product'] = $product;

        $categoryNameList = [];
        if (isset($product->categories[0]) && $product->categories[0] != NULL) {
            $categoryModel = new CategoryModel();
            $res = $categoryModel->getAllCategoryByProductId($product->id);
            $cat_id = 0;
            foreach ($res as $r) {
                if ($cat_id < $r->category_id) {
                    $cat_id = $r->category_id;
                }
            }
            if ($cat_id > 0) {
                $results = $categoryModel->getAllParentWithChildList($cat_id);
                if (sizeof($results) > 0) {
                    foreach ($results as $result) {
                        array_push($categoryNameList, $result);
                        $this->pageData['categoryNameList'] = $categoryNameList;
                    }
                }
            }
        }

        $productModel->setCurrentUserId($this->appCredential->id);
        $productModel->setCustomOffset(0);
        $productModel->setCustomLimit(6);

        $relatedProducts = array();
        if (count($product->categories) > 0) {
            $relatedProducts = $productModel->getRelatedProducts($product->categories[0]->id, $product->id);
        }

        //return $relatedProducts;
        //return $relatedProducts[0]->product->title;
        $this->pageData['relatedProducts'] = $relatedProducts;
        //return $relatedProducts;


        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;


        $recentProductList = [];

//        foreach ($results as $result) {
//            $product = $productModel->getProductById($result->product_id);
//            array_push($recentProductList, $product);
//        }



        $cookie_name = "recentProduct";
//        $cookie_value = "";
//       setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day

        if ($product_current_id != 0) {
            if (!isset($_COOKIE[$cookie_name])) {
                $recentArray = array();
                array_push($recentArray, $product_current_id);
                $recentArray = serialize($recentArray);
                $cookie_value = $recentArray;
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
            } else {
                $recentArray = unserialize($_COOKIE[$cookie_name]);

                $i = 0;
                foreach ($recentArray as $recent):
                    if ($recent != $product_current_id) {
                        if ($i < 3) {
                            $product = $productModel->getProductById($recent);
                            array_push($recentProductList, $product);
                        }
                        $i++;
                    }
                endforeach;

                if (!in_array($product_current_id, $recentArray)) {
                    array_push($recentArray, $product_current_id);
                }
                $recentArray = serialize($recentArray);
                $cookie_value = $recentArray;
                setcookie($cookie_name, $cookie_value, time() + (86400 * 30), "/"); // 86400 = 1 day
            }
        }

        $this->pageData['recentProductList'] = $recentProductList;

        return view("web.product.details", $this->pageData);
    }

    public function getrecentViewProduct(Request $request) {

        /* Searching params for product by category and Attribute */
        /* Starts Here */

        $q = $request->input("q");
        $selectedProductAttribute = [];
        $selectedPrice = new \stdClass();
        $searchProduct = false;
        $priceValidation = true;
        $selectedManufacturersIdList = [];
        $parentCategoryId = 0;
        if ($q != null && $q != "") {
            $pa = [];
            $tmpQstar = json_decode($q);

            try {
                $selectedProductAttribute = (isset($tmpQstar->pa)) ? $tmpQstar->pa : [];
                $searchProduct = true;
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {
                $selectedPrice = (isset($tmpQstar->price)) ? $tmpQstar->price : new \stdClass();
                if (!isset($selectedPrice->min) || !isset($selectedPrice->max)) {
                    $priceValidation = false;
                } else {
                    if ($selectedPrice->min <= 0) {
                        $priceValidation = false;
                    }
                    if ($selectedPrice->max <= 0) {
                        $priceValidation = false;
                    }
                }


                if (!$priceValidation) {
                    $selectedPrice = new \stdClass();
                } else {
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedProductAttribute = [];
            }
            try {

                $selectedManufacturersIdList = (isset($tmpQstar->manufacturers)) ? $tmpQstar->manufacturers : new \stdClass();

                if (!is_array($selectedManufacturersIdList)) {
                    $selectedManufacturersIdList = [];
                    $searchProduct = true;
                }
            } catch (Exception $ex) {
                $selectedManufacturersIdList = [];
            }
        } else {
            $priceValidation = false;
            $selectedManufacturersIdList = [];
            $searchProduct = true;
        }

        $this->pageData['selectedManufacturersIdList'] = $selectedManufacturersIdList;
        $this->pageData['selectedProductAttribute'] = $selectedProductAttribute;
        $this->pageData['selectPrice'] = $selectedPrice;
        /* End Here */


//        $mainCategoryForReturningProduct = $category;
//        $subcategoryList = [];
        $categoryIdList = [];

        //get all category with first category id for menu generation.
//        if (isset($category)) {
//            $categoryObj = new CategoryModel();
//            $subcategoryList = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $category);
//        }
        //check if 2nd category, as child of first category is defined
//        if ($category2 != null) {
//            $mainCategoryForReturningProduct = $category2;
//        }
        //check if 3rd category, as child of second category is defined
//        if ($category3 != null) {
//            $mainCategoryForReturningProduct = $category3;
//        }
        $orderBy = 0;
        if ($request->input("orderBy") != null) {
            $orderBy = (is_numeric($request->input("orderBy"))) ? intval($request->input("orderBy")) : 0;
        }

        $limit = 0;
        if ($request->input("limit") != null) {
            $limit = (is_numeric($request->input("limit"))) ? intval($request->input("limit")) : 10;
        }

//        $categoryObj = new CategoryModel();
//        $finalCategoryForProducts = $categoryObj->allCategoryWithParentsAndItsChildrens($this->shopId, $mainCategoryForReturningProduct);
//        foreach ($finalCategoryForProducts as $cat) {
//            array_push($categoryIdList, $cat->id);
//            if (!empty($cat->childrens)) {
//                foreach ($cat->childrens as $child) {
//                    array_push($categoryIdList, $child->id);
//                    if (!empty($child->childrens)) {
//                        foreach ($child->childrens as $child2) {
//                            array_push($categoryIdList, $child2->id);
//                        }
//                    }
//                }
//            }
//        }
//        if (empty($selectedManufacturersIdList)) {
//            $this->pageData['status'] = false;
//            $this->pageData['msg'] = "No Data for this category";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
//        }

        $productModel = new ProductModel();
        $productModel->setCurrentUserId($this->appCredential->user->id);
        $limit = ($limit == (0 || "")) ? 40 : $limit;
        $productModel->setCustomLimit($limit);
        $productModel->customOffset = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");
        $currentPage = ($request->input("page") == (0 || "")) ? 0 : $request->input("page");

        $startProductNumber = $limit * $currentPage + 1;
        $endProductNumber = $limit * ($currentPage + 1);

        $productList = [];
//        if ($searchProduct) {
        // Attribute selection search  Wise product
        $cookie_name = "recentProduct";
        $recentProductArray = array();

        if (isset($_COOKIE[$cookie_name])) {
            $recentProductArray = unserialize($_COOKIE[$cookie_name]);
        }

//        $bestSellerList = $productModel->getAllBestSellerProductId();
//        $bestSellerArray = array();
//        foreach ($bestSellerList as $best):
//            array_push($bestSellerArray, $best->product_id);
//        endforeach;

        if ($priceValidation) {
            $productList = $productModel->getAllRecentViewProducts($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $recentProductArray, $orderBy);
            $totalProduct = $productModel->getAllRecentViewProductsCount($categoryIdList, $selectedProductAttribute, $selectedPrice->min, $selectedPrice->max, $selectedManufacturersIdList, $recentProductArray, $orderBy);
        } else {
            $productList = $productModel->getAllRecentViewProducts($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $recentProductArray, $orderBy);
            $totalProduct = $productModel->getAllRecentViewProductsCount($categoryIdList, $selectedProductAttribute, -1, -1, $selectedManufacturersIdList, $recentProductArray, $orderBy);
        }
//        } 
//        else {
//            // Category Wise product
//            $productList = $productModel->getAllProductByCategory($categoryIdList);
//            $totalProduct = $productModel->getAllProductCountByCategory($categoryIdList);
//        }
//        $totalProduct = $productModel->countAllProductOfThisCategory($categoryIdList);
//            echo '<pre>';
//            echo count($productList);
//            print_r($productList);
//            die;

        if ($endProductNumber > $totalProduct) {
            $endProductNumber = $totalProduct;
        }
        if ($endProductNumber < 0) {
            $endProductNumber = $totalProduct;
        }
        if ($startProductNumber < 0) {
            $startProductNumber = 1;
        }

        if (empty($productList)) {
            $this->pageData['status'] = false;
            $this->pageData['msg'] = "No Data received";
//            $this->pageData['subcategoryList'] = $subcategoryList;
//            $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        }

        $this->pageData['status'] = true;
        $this->pageData['selected'] = true;
        $this->pageData['selectedAction'] = $orderBy;
        $this->pageData['msg'] = "No Data received";
        $this->pageData['productList'] = $productList;
//        $this->pageData['subcategoryList'] = $subcategoryList;
//        $this->pageData['selectedCategory'] = $finalCategoryForProducts;
        $this->pageData['totalProduct'] = $totalProduct;

//        $currentCategory = 0;
//        if ($category3 != null) {
//            $currentCategory = $category3;
//        } elseif ($category2 != null) {
//            $currentCategory = $category2;
//        } else {
//            $currentCategory = $category;
//        }

        /* For Search selection  */

//        $this->pageData['categoryIdList'] = $categoryIdList;
        $manufacturerModel = new ManufacturerModel();
//        $manufacturerModel->setCategoryIdList($categoryIdList);

        $this->pageData['manufacturers'] = $manufacturerModel->getManufactureIdList();

//        $this->pageData['minMaxPrice'] = $productModel->getMinMaxPriceByCategoryIdList($categoryIdList);
        $price = [];
        $price['min'] = 0;
        $price['max'] = 5000;
        $this->pageData['minMaxPrice'] = $price;

        $attributesModel = new AttributesModel();
        $attributesModel->setCategoryIdList($categoryIdList);

        /* Ends Here */
        $this->pageData['parentCategoryId'] = $parentCategoryId;
        $this->pageData['currentPage'] = $currentPage;
        $this->pageData['limit'] = $limit;
        $this->pageData['startProductNumber'] = $startProductNumber;
        $this->pageData['endProductNumber'] = $endProductNumber;
        $this->pageData['productsAttributes'] = $attributesModel->getByProductList();

        // For later use
        // Source found http://stackoverflow.com/questions/163809/smart-pagination-algorithm
        // https://code.google.com/archive/p/spaceshipcollaborative/wikis/PHPagination.wiki
        // $pagination = new Pagination();

        $results = $productModel->getBestSellerProductIdList();

        $bestSellerProductList = [];

        foreach ($results as $result) {
            $product = $productModel->getProductById($result->product_id);
            array_push($bestSellerProductList, $product);
        }

        $this->pageData['bestSellerProductList'] = $bestSellerProductList;

        $categoryNameList = [];

//        if (($category != null) && ($category2 == null) && ($category3 == null)) {
//            $result1 = CategoryModel::where("id", "=", $category)->first();
//            array_push($categoryNameList, $result1);
//            $this->pageData['categoryNameList'] = $categoryNameList;
//        }
//        if (($category != null) && ($category2 != null) && ($category3 == null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category2);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }
//        if (($category != null) && ($category2 != null) && ($category3 != null)) {
//            $categoryModel = new CategoryModel();
//            $results = $categoryModel->getAllParentWithChildList($category3);
//            foreach ($results as $result) {
//                array_push($categoryNameList, $result);
//                $this->pageData['categoryNameList'] = $categoryNameList;
//            }
//        }

        $this->pageData['categoryNameList'] = array();
        $this->pageData['subcategoryList'] = array();
        $this->pageData['selectedCategory'] = array();
        return view("web.home.recentviewproducts", $this->pageData);
    }

}
