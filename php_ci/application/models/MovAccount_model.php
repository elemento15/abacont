<?php

include_once('BaseModel.php');

class MovAccount_model extends BaseModel {
	
	protected $table_name    = 'movimientos_cuentas';
	protected $list_fields   = array('id','fecha','tipo','importe','cancelado','concepto','automatico','traspaso','traspaso_id','cuenta_id',
		                             'cuentas.nombre AS cuenta_nombre', 'cuentas.tipo AS cuenta_tipo',
		                             'c2.nombre AS cuenta_traspaso');
	protected $search_fields = array('fecha','concepto');
	protected $save_fields   = array('fecha','cuenta_id','tipo','importe','concepto','automatico','traspaso','traspaso_id','observaciones');
	protected $edit_fields   = array('observaciones');
	protected $avoid_delete  = true;

	protected $join_tables = array(
  	          array('table' => 'cuentas', 'type' => 'left', 'fk' => 'cuenta_id'),
  	          array('table' => 'movimientos_cuentas AS mc2', 'condition' => 'mc2.id = movimientos_cuentas.traspaso_id'),
  	          array('table' => 'cuentas AS c2', 'condition' => 'c2.id = mc2.cuenta_id')
            );

	public function firstMovDate() {
		$this->db->select('fecha');
		$this->db->from('movimientos_cuentas');
		$this->db->where('NOT cancelado');
		$this->db->order_by('fecha');
		$this->db->limit('1');

		$query = $this->db->get();
		return ($query->row()) ? $query->row()->fecha : false;
	}

}

?>