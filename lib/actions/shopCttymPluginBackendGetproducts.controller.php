<?php

/*
 * Class shopCttymPluginBackendGetproductsController
 * @author Max Severin <makc.severin@gmail.com>
 */
class shopCttymPluginBackendGetproductsController extends waJsonController {

    public function execute() {

        $query = waRequest::post('query', '', waRequest::TYPE_STRING_TRIM);
        $page = waRequest::post('page', 1, 'int');

        $result = array();
        $result['products'] = array();
        $result['product_count'] = 0;

        $collection = new shopProductsCollection('search/query=' . $query);

        $product_limit = 10;

        $products = $collection->getProducts('*', ($page-1)*$product_limit, $product_limit);
        
        if ($products) {

            $brands = array();
            $categories = array();
            $feature_model = new shopFeatureModel();

            foreach ($products as $p) {         
                $brand_feature = $feature_model->getByCode('brand');
                $brand = '';
                if ($brand_feature) {
                    $feature_value_model = $feature_model->getValuesModel($brand_feature['type']);
                    $product_brands = $feature_value_model->getProductValues($p['id'], $brand_feature['id']);

                    $brands = array();

                    foreach ($product_brands as $k => $v) {
                        $brand_id = $feature_value_model->getValueId($brand_feature['id'], $v);
                        $brands[] = array(
                            'id' => $brand_id,
                            'brand' => $v,
                        );
                    }   
                }

                $category_model = new shopCategoryModel();
                $category = $category_model->getById($p['category_id']);
                $res_category = '';
                if ($category) {
                    $res_category = $category['name'];
                }              

                $result['products'][] = array(
                    "id" => $p['id'],
                    "name" => $p['name'],
                    "image" => ($p['image_id'] ? "<img src='" . shopImage::getUrl(array("product_id" => $p['id'], "id" => $p['image_id'], "ext" => $p['ext']), "48x48") . "' />" : ""),
                    "price" => shop_currency_html($p['price'], true),
                    "brands" => $brands,
                    "category" => $res_category,
                );
            }

            $product_model = new shopProductModel();
            $product_count = $collection->count();

            $result['product_count'] = $product_count;

            if ( $product_count > (($page-1)*$product_limit + $product_limit) ) {
                $result['next_page'] = $page+1;
            } else {
                $result['next_page'] = false;
            }

        }

        $this->response = $result;

    }

}