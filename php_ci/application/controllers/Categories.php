<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Categories extends BaseController {

	protected $modelName = 'Category_model';

	public function actives() {
		$params = array(
	      'order'  => array('field' => 'nombre', 'type' => 'ASC'),
	      'start'  => 0,
	      'length' => 0,
	      'search' => null,
	      'filter' => array(array('field' => 'activo', 'value' => true))
	    );
	    $recs = $this->model->findAll($params);
	    
	    echo json_encode($recs['data']);
	}
}
