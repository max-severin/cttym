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

}