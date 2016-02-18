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
				'id'        => 0,
				'fecha'     => $data['fecha'],
				'cuenta_id' => $data['cuenta_id'],
				'tipo'      => ($data['tipo'] == 'I') ? 'A' : 'C',
				'importe'   => $data['importe'],
				'concepto'  => 'Movimiento generado por Ingreso/Egreso'
			);
			$id_mov_acc = $this->modelMovAcc->save($params);

			// update account's balance
			$this->modelAccount->updateBalance($data['cuenta_id'], $params['tipo'], $data['importe']);
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
		if ($row = $this->model->find($id)) {

		}
	}
}
