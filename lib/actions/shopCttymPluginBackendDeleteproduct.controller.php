<?php

/*
 * Class shopCttymPluginBackendDeleteproductController
 * @author Max Severin <makc.severin@gmail.com>
 */
class shopCttymPluginBackendDeleteproductController extends waJsonController {

    public function execute() {
        $id = waRequest::post('id', 0, 'int');  
        
        $model = new shopCttymPluginProductModel();

        if ( $id ) {
            $model->deleteById($id);
            
            $this->response = true;
        } else {
            $this->response = false;
        }            
    }
    
}