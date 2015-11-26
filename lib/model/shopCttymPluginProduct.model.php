<?php

/*
 * Class shopCttymPluginProductModel
 * @author Max Severin <makc.severin@gmail.com>
 */
class shopCttymPluginProductModel extends waModel {

	protected $table = 'shop_cttym_product';

	public function getCttymProducts($offset = 0, $limit = null) {
		$sql = '';

		$sql .= "SELECT * FROM `{$this->table}`";
		$sql .= " ORDER BY `id` DESC";
		$sql .= " LIMIT ".($offset ? $offset.',' : '').(int)$limit;

		$cttym_products = $this->query($sql)->fetchAll('id');

		foreach ($cttym_products as $id => $product) {

			$p = new shopProduct($product['product_id']);  
			$route_params = array('product_url' => $p['url']);
			if (isset($p['category_url'])) {
				$route_params['category_url'] = $p['category_url'];
			}

			$p['frontend_url'] = wa()->getRouteUrl('shop/frontend/product', $route_params);                 
			$cttym_products[$id]['product'] = $p;

		}

		return $cttym_products;
	}

}