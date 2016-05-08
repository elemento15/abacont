<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Categories extends BaseController {

	protected $modelName = 'Category_model';

	public function actives() {
		$filter = array(array('field' => 'activo', 'value' => true));
		if (isset($_POST['type'])) {
			$filter[] = array('field' => 'tipo', 'value' => $_POST['type']);
		}

		$params = array(
	      'order'    => array('field' => 'nombre', 'type' => 'ASC'),
	      'order_id' => false,
	      'start'    => 0,
	      'length'   => 0,
	      'search'   => null,
	      'filter'   => $filter
	    );
	    $recs = $this->model->findAll($params);
	    
	    echo json_encode($recs['data']);
	}
}
