<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Accounts extends BaseController {

	protected $modelName = 'Account_model';

	public function actives() {
		$incomes = (isset($_POST['incomes']) && $_POST['incomes']) ? true : false;
		$expenses = (isset($_POST['expenses']) && $_POST['expenses']) ? true : false;
		$type = (isset($_POST['type'])) ? $_POST['type'] : false;
		$omit_inversions = (isset($_POST['omitInversions'])) ? $_POST['omitInversions'] : false;

		$params = array(
	      'order'    => array('field' => 'nombre', 'type' => 'ASC'),
	      'order_id' => false,
	      'start'    => 0,
	      'length'   => 0,
	      'search'   => null,
	      'filter'   => array(array('field' => 'activo', 'value' => true))
	    );

		if ($incomes) {
			$params['filter'][] = array('field' => 'usa_ingresos', 'value' => true);
		}

		if ($expenses) {
			$params['filter'][] = array('field' => 'usa_gastos', 'value' => true);
		}

		if ($type) {
			$params['filter'][] = array('field' => 'tipo', 'value' => $type);
		}

	    $recs = $this->model->findAll($params);
		
		if (! $omit_inversions) {
			$data = $recs['data'];
		} else {
			$data = [];

			foreach ($recs['data'] as $item) {
				if ($item->tipo != 'I') {
					$data[] = $item;
				}
			}
		}

	    echo json_encode($data);
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
	    $recs['total_balance'] = $this->model->getAllBalances($params['search'], $params['filter']);
	    
	    echo json_encode($recs);
	  }
}
