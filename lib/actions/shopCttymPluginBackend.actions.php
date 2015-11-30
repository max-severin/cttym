<?php

/*
 * Class shopCttymPluginBackendActions
 * @author Max Severin <makc.severin@gmail.com>
 */
class shopCttymPluginBackendActions extends waViewActions {

    public function saveproductAction () {
        if (waRequest::method() == 'post') {
            
            $model = new shopCttymPluginProductModel();

            $data = array(
                'product_id' => waRequest::post('product_id', '', 'Int'),          
                'ym_url'     => waRequest::post('ym_url', '', 'String'),
                'id_in_html' => waRequest::post('id_in_html', '', 'String'),  
                'price_diff' => waRequest::post('price_diff', '', 'Int'),
            );

            $model->insert($data);

        }
        
        $this->redirect('?action=plugins#/cttym/');
    }

    public function updateproductAction () {
        if (waRequest::method() == 'post') {
            
            $model = new shopCttymPluginProductModel();

            $id = waRequest::post('id', 0, 'int');

            $data = array(
                'product_id' => waRequest::post('product_id', '', 'Int'),          
                'ym_url'     => waRequest::post('ym_url', '', 'String'),
                'id_in_html' => waRequest::post('id_in_html', '', 'String'),  
                'price_diff' => waRequest::post('price_diff', '', 'Int'),
            );

            $model->updateById($id, $data);

        }
        
        $this->redirect('?action=plugins#/cttym/');
    }

    public function changepriceAction () {
        $id = waRequest::get('id', 0, 'int');
        $model = new shopCttymPluginProductModel();

        $product = $model->getById($id);

        $p = new shopProduct($product['product_id']);
        // $p['price']

        $ym_html = file_get_contents($product['ym_url']);

        preg_match_all('/<div class=\"snippet-card__price\">(.*)<\/div>/siU', $ym_html, $matches);
        print_r($ym_html);
        
        for($i=(int)$start; $i < (int)$end; $i++) {
            $item = $matches[0][$i];
            echo '<pre>'; print_r($item); echo '</pre>';
        }
        echo '</br>';
        exit();
        
        $this->redirect('?action=plugins#/cttym/');
    }

}