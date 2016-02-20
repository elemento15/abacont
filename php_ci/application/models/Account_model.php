<?php

include_once('BaseModel.php');

class Account_model extends BaseModel {
	
	protected $table_name    = 'cuentas';
	protected $list_fields   = array('id','nombre','tipo','activo','num_tarjeta','num_cuenta','saldo');
	protected $search_fields = array('nombre','num_tarjeta','num_cuenta');
	protected $save_fields   = array('nombre','tipo','activo','num_tarjeta','num_cuenta','observaciones');
	// protected $edit_fields   = array();
	protected $avoid_delete  = false;


	public function getBalance($id) {
		$row = $this->find($id);
		return $row->saldo;
	}

	public function updateBalance($id, $amount, $add) {
		$balance = $this->getBalance($id);
		$balance += ($add) ? $amount : -$amount;

		$data = array('id' => $id, 'saldo' => $balance);
		return $this->save($data, true);
	}
}

?>