<?php

/*
 * @author Max Severin <makc.severin@gmail.com>
 */
return array(
	'shop_cttym_product' => array(
		'id' => array('int', 11, 'null' => 0, 'autoincrement' => 1),
		'product_id' => array('int', 11),
		'ym_url' => array('text'),
		'id_in_html' => array('text'),
		'price_diff' => array('int', 11),
		':keys' => array(
			'PRIMARY' => 'id',
		),
	),
);