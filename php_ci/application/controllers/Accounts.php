<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Accounts extends BaseController {

	protected $modelName = 'Account_model';

	public function actives() {
		$params = array(
	      'order'    => array('field' => 'nombre', 'type' => 'ASC'),
	      'order_id' => false,
	      'start'    => 0,
	      'length'   => 0,
	      'search'   => null,
	      'filter'   => array(array('field' => 'activo', 'value' => true))
	    );
	    $recs = $this->model->findAll($params);
	    
	    echo json_encode($recs['data']);
	}

	public function print_list() {
		$this->load->library('AccountPdf', array(), 'pdf');
		$this->pdf->printing();
	}

	public function read() {
	    $params = array(
	      'order'    => isset($_POST['order'])  ? $_POST['order'] : null,
	      'order_id' => (isset($_POST['order_by_id']) && $_POST['order_by_id']) ? true : false,
	      'start'    => isset($_POST['start'])  ? $_POST['start']  : 0,
	      'length'   => isset($_POST['length']) ? $_POST['length'] : 0,
	      'search'   => isset($_POST['search']) ? $_POST['search'] : null,
	      'filter'   => isset($_POST['filter']) ? $_POST['filter'] : array()
	    );

	    $recs = $this->model->findAll($params);
	    $recs['total_balance'] = $this->model->getAllBalances();
	    
	    echo json_encode($recs);
	  }
}
