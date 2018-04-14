<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Movements extends BaseController {

	protected $modelName = 'Movement_model';

	public function __construct() {
	    parent::__construct();
	    $this->load->model('MovAccount_model','modelMovAcc',true);
	    $this->load->model('Account_model','modelAccount',true);
	}

	public function save_movement () {
		$data = json_decode($_POST['model'], true);
		$is_new = !$data['id'];
		
		if ($is_new) {
			// generate movement_account
			$params = array(
				'id'         => 0,
				'fecha'      => $data['fecha'],
				'cuenta_id'  => $data['cuenta_id'],
				'tipo'       => ($data['tipo'] == 'I') ? 'A' : 'C',
				'importe'    => $data['importe'],
				'concepto'   => 'Movimiento generado por '.(($data['tipo'] == 'I') ? 'Ingreso' : 'Gasto' ),
				'automatico' => 1
			);
			$id_mov_acc = $this->modelMovAcc->save($params);

			// update account's balance
			$this->modelAccount->updateBalance($data['cuenta_id'], $data['importe'], $params['tipo'] == 'A');
		} else {
			$id_mov_acc = 0;
		}

		// generate movement
		$params = array(
			'id'    => $data['id'],
			'fecha' => $data['fecha'],
			'tipo'  => $data['tipo'],
			'movimiento_cuenta_id' => $id_mov_acc,
			'subcategoria_id' => $data['subcategoria_id'],
			'importe'         => $data['importe'],
			'observaciones'   => $data['observaciones']
		);
		$id = $this->model->save($params);

		if ($id) {
	      $response = array('success' => true, 'id' => $id);
	    } else {
	      $response = array('success' => false, 'msg' => $this->model->getError());
	    }
    	
    	echo json_encode($response);
	}

	public function cancel () {
		$id = intval($_POST['id']);
		if ($mov = $this->model->find($id)) {
			if ($mov->cancelado) {
				throw new Exception("Record already canceled");
			}

			// cancel movement
			$params = array('id' => $id, 'cancelado' => 1);
			$this->model->save($params, true);

			// get account
			$mov_acc = $this->modelMovAcc->find( $mov->movimiento_cuenta_id );

			// cancel movement account
			$params = array('id' => $mov_acc->id, 'cancelado' => 1);
			$this->modelMovAcc->save($params, true);
			
			// update account's balance
			$this->modelAccount->updateBalance($mov_acc->cuenta_id, $mov_acc->importe, $mov_acc->tipo == 'C');
		}
	}

	public function find_by_mov_account() {
		$id_mov_acc = intval($_POST['id']);

		if ($mov = $this->model->find_by_mov_acc($id_mov_acc) ) {
			$response = array('success' => true, 'data' => $mov);
		} else {
			$response = array('success' => false, 'msg' => $this->model->getError());
		}

		echo json_encode($response);
	}

	public function rpt_movements() {
		$this->load->library('MovementsPdf', array(), 'pdf');
		$params = array(
			'rpt' => $_REQUEST['rpt'],
			'type' => $_REQUEST['type'],
			'account' => $_REQUEST['account'],
			'category' => $_REQUEST['category'],
			'subcategory' => $_REQUEST['subcategory'],
			'date_ini' => $_REQUEST['date_ini'],
			'date_end' => $_REQUEST['date_end'],
			'comments' => $_REQUEST['comments'],
			'download' => $_REQUEST['download']
		);

		$this->pdf->setParams($params);
		$this->pdf->printing();
	}

	public function rpt_incomes_expenses() {
		$this->load->library('RptIncomesExpensesPdf', array(), 'pdf');
		
		$params = array(
			'months' => $_REQUEST['months'],
			'current' => $_REQUEST['current']
		);

		$this->pdf->setParams($params);
		$this->pdf->printing();
	}
}
