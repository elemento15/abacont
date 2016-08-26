<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include('BaseController.php');

class Movs_Accounts extends BaseController {

	protected $modelName = 'MovAccount_model';

	public function __construct() {
		parent::__construct();
		$this->load->model('Account_model','modelAccount',true);
	}

	public function save_mov_account () {
		$data = json_decode($_POST['model'], true);
		$is_new = !$data['id'];
		$this->model->beginTransaction();
		
		if ($is_new) {
			// generate movement_account
			$params = array(
				'id'            => 0,
				'fecha'         => $data['fecha'],
				'cuenta_id'     => $data['cuenta_id'],
				'tipo'          => $data['tipo'],
				'importe'       => $data['importe'],
				'concepto'      => $data['concepto'],
				'observaciones' => $data['observaciones'],
				'automatico'    => 0
			);
			if (!$id = $this->model->save($params)) {
				echo json_encode(array('success' => false, 'msg' => $this->model->getError()));
				exit;
			}

			// update account's balance
			if (!$rec = $this->modelAccount->updateBalance($data['cuenta_id'], $data['importe'], $data['tipo'] == 'A')) {
				echo json_encode(array('success' => false, 'msg' => $this->modelAccount->getError()));
				exit;
			}
		
		} else {
			$mov = $this->model->find( $data['id'] );

			$params = array(
				'id'            => $data['id'],
				'observaciones' => $data['observaciones']
			);

			// allows to edit "concepto" if the record is automatic
			if (! $mov->automatico) {
				$params['concepto'] = $data['concepto'];
			}

			$id = $this->model->save($params, true);
		}

		if ($id) {
	      $response = array('success' => true, 'id' => $id);
	    } else {
	      $response = array('success' => false, 'msg' => $this->model->getError());
	    }
    	
    	$this->model->commitTransaction();
    	echo json_encode($response);
	}

	public function save_transfer () {
		$data = json_decode($_POST['model'], true);
		$is_new = !$data['id'];
		$this->model->beginTransaction();
		
		if ($is_new) {
			
			// generate movement_account
			$params = array(
				'id'            => 0,
				'fecha'         => $data['fecha'],
				'cuenta_id'     => $data['cuenta_id'],
				'tipo'          => 'C',
				'importe'       => $data['importe'],
				'concepto'      => $data['concepto'],
				'observaciones' => 'Cargo por Transferencia. '.$data['observaciones'],
				'automatico'    => 0
			);
			if (!$id = $this->model->save($params)) {
				echo json_encode(array('success' => false, 'msg' => $this->model->getError()));
				exit;
			}

			// update account's balance
			if (!$rec = $this->modelAccount->updateBalance($data['cuenta_id'], $data['importe'], false)) {
				echo json_encode(array('success' => false, 'msg' => $this->modelAccount->getError()));
				exit;
			}


			// generate movement_account
			$params = array(
				'id'            => 0,
				'fecha'         => $data['fecha'],
				'cuenta_id'     => $data['cuenta_id_destino'],
				'tipo'          => 'A',
				'importe'       => $data['importe'],
				'concepto'      => $data['concepto'],
				'observaciones' => 'Abono por Transferencia. '.$data['observaciones'],
				'automatico'    => 0
			);
			if (!$id = $this->model->save($params)) {
				echo json_encode(array('success' => false, 'msg' => $this->model->getError()));
				exit;
			}

			// update account's balance
			if (!$rec = $this->modelAccount->updateBalance($data['cuenta_id_destino'], $data['importe'], true)) {
				echo json_encode(array('success' => false, 'msg' => $this->modelAccount->getError()));
				exit;
			}
		
		} else {
			echo json_encode(array('success' => false, 'msg' => 'No se puede editar una transferencia'));
			exit;
		}

		$this->model->commitTransaction();
		echo json_encode(array('success' => true));
	}

	public function cancel () {
		$id = intval($_POST['id']);
		if ($mov = $this->model->find($id)) {
			if ($mov->cancelado) {
				throw new Exception("Record already canceled");
			}

			if ($mov->automatico) {
				throw new Exception("Record generated automatically");
			}

			// cancel movement
			$params = array('id' => $id, 'cancelado' => 1);
			$this->model->save($params, true);
			
			// update account's balance
			$this->modelAccount->updateBalance($mov->cuenta_id, $mov->importe, $mov->tipo == 'C');
		}
	}

	public function rpt_movs_accounts() {
		$this->load->library('MovsAccountsPdf', array(), 'pdf');
		$params = array(
			'account' => $_REQUEST['account'],
			'date_ini' => $_REQUEST['date_ini'],
			'download' => $_REQUEST['download']
		);

		$this->pdf->setParams($params);
		$this->pdf->printing();
	}
}
