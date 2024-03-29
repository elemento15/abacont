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
				'automatico'    => 0,
				'traspaso'      => 1
			);

			if (!$id_01 = $this->model->save($params)) {
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
				'automatico'    => 0,
				'traspaso'      => 1,
				'traspaso_id'   => $id_01 // save the reference
			);

			if (!$id_02 = $this->model->save($params)) {
				echo json_encode(array('success' => false, 'msg' => $this->model->getError()));
				exit;
			}

			// update account's balance
			if (!$rec = $this->modelAccount->updateBalance($data['cuenta_id_destino'], $data['importe'], true)) {
				echo json_encode(array('success' => false, 'msg' => $this->modelAccount->getError()));
				exit;
			}

			// update reference in first movement
			$params = array(
				'id'            => $id_01,
				'traspaso_id'   => $id_02
			);

			if (!$id_01 = $this->model->save($params, true)) {
				echo json_encode(array('success' => false, 'msg' => $this->model->getError()));
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
				throw new Exception("No puede cancelar registros cancelados");
			}

			if ($mov->automatico) {
				throw new Exception("No puede cancelar registros automaticos");
			}

			if ($mov->traspaso && !$mov->traspaso_id) {
				throw new Exception("Movimiento de traspaso sin referencia");
			}

			// cancel movement
			$params = array('id' => $id, 'cancelado' => 1);
			if (!$this->model->save($params, true)) {
				throw new Exception("Error al cancelar movimiento");
			}

			// update account's balance
			$this->modelAccount->updateBalance($mov->cuenta_id, $mov->importe, $mov->tipo == 'C');
			

			// if transfer, cancel second movement
			if ($mov->traspaso) {
				$mov_trasp = $this->model->find($mov->traspaso_id);

				$params = array('id' => $mov_trasp->id, 'cancelado' => 1);
				if (!$this->model->save($params, true)) {
					throw new Exception("Error al cancelar movimiento de traspaso");
				}
				
				$this->modelAccount->updateBalance($mov_trasp->cuenta_id, $mov_trasp->importe, $mov_trasp->tipo == 'C');
			}
		}
	}

	public function rpt_movs_accounts() {
		if ($_REQUEST['option'] == 'CSV') {
			$this->load->library('MovsAccountsCsv', array(), 'csv');
			$params = array(
				'account' => $_REQUEST['account'],
				'date_ini' => $_REQUEST['date_ini']
			);
			$this->csv->setParams($params);
			$this->csv->printing();

		} else {
			$this->load->library('MovsAccountsPdf', array(), 'pdf');
			$params = array(
				'account' => $_REQUEST['account'],
				'date_ini' => $_REQUEST['date_ini']
			);

			$this->pdf->setParams($params);
			$this->pdf->printing();
		}
	}
}
