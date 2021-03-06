<?php

	require_once('../../api/Simpla.php');
	$simpla = new Simpla();

	$productid = $simpla->request->post('productid', 'integer');
	$product_s = $simpla->products->get_product(intval($productid));

	header("Content-type: application/json; charset=UTF-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	print json_encode($product_s);
