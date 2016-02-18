<?php

include_once('BaseModel.php');

class MovAccount_model extends BaseModel {
	
	protected $table_name    = 'movimientos_cuentas';
	protected $list_fields   = array('id','fecha','tipo','importe','cancelado','concepto','cuenta_id',
		                             'cuentas.nombre AS cuenta_nombre',
		                             'cuentas.tipo AS cuenta_tipo');
	protected $search_fields = array('fecha');
	protected $save_fields   = array('fecha','cuenta_id','tipo','importe','concepto','observaciones');
	protected $edit_fields   = array('concepto','observaciones');
	protected $avoid_delete  = true;

	protected $join_tables = array(
  	          array('table' => 'cuentas', 'type' => 'left', 'fk' => 'cuenta_id')
            );

}

?>