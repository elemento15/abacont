<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Subcategories extends BaseController {

	protected $modelName = 'Subcategory_model';

	public function actives() {
		$filter = array(array('field' => 'activo', 'value' => true));
		if (isset($_POST['category_id'])) {
			$filter[] = array('field' => 'categoria_id', 'value' => intval($_POST['category_id']));
		}

		$params = array(
	      'order'  => array('field' => 'nombre', 'type' => 'ASC'),
	      'start'  => 0,
	      'length' => 0,
	      'search' => null,
	      'filter' => $filter
	    );
	    $recs = $this->model->findAll($params);
	    
	    echo json_encode($recs['data']);
	}
}
