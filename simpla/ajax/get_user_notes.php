<?php

	require_once('../../api/Simpla.php');
	$simpla = new Simpla();

	$id = $simpla->request->post('id', 'integer');
	$row_fillter = 'note';

	//$user = $simpla->users->get_user_ajax(intval($id));

	$user_note = $simpla->users->get_uprice(array('userid'=>$id, 'rowtype'=>$row_fillter));

	header("Content-type: application/json; charset=UTF-8");
	header("Cache-Control: must-revalidate");
	header("Pragma: no-cache");
	header("Expires: -1");
	print json_encode($user_note);
