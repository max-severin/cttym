<?php

/*
 * Class shopCttymPluginSettingsAction
 * @author Max Severin <makc.severin@gmail.com>
 */
class shopCttymPluginSettingsAction extends waViewAction {

    protected $cttym_products_limit = 2;

    public function execute() {
        $model = new waModel();

        try {

            $model->query('SELECT * FROM shop_cttym_product WHERE 0');

        } catch (waDbException $e) {

            $file_db = realpath(dirname(__FILE__)).'/../config/db.php';

            if (file_exists($file_db)) {
                $schema = include($file_db);
                $model->createSchema($schema);
            }

        }

        $plugin = wa('shop')->getPlugin('cttym');
        $settings = $plugin->getSettings();

        $limit = $this->cttym_products_limit;
        $page = waRequest::get('page', 1, 'int');
        if ($page < 1) {
            $page = 1;
        }
        $offset = ($page - 1) * $limit;

        $cttym_model = new shopCttymPluginProductModel();        
        $cttym_products = $cttym_model->getCttymProducts($offset, $limit);
        $count = $cttym_model->countAll();

        $pages_count = ceil((float)$count / $limit);
        $this->view->assign('pages_count', $pages_count);

        $this->view->assign('cttym_settings', $settings);
        $this->view->assign('cttym_products', $cttym_products);
        $this->view->assign('cttym_products_count', $count);
    }

}