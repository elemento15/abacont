<?php

include('BaseModel.php');

class Movement_model extends BaseModel {
	
	protected $table_name    = 'movimientos';
	protected $list_fields   = array('id','fecha','tipo','cancelado','movimiento_cuenta_id','subcategoria_id');
	protected $search_fields = array('fecha');
	protected $save_fields   = array('fecha','tipo','movimiento_cuenta_id','subcategoria_id','concepto','observaciones');
	protected $edit_fields   = array('observaciones');
	protected $avoid_delete  = true;

	protected $join_tables = array(
  	          array('table' => 'movimientos_cuentas', 'type' => 'left', 'fk' => 'movimiento_cuenta_id'),
  	          array('table' => 'subcategorias', 'type' => 'left', 'fk' => 'subcategoria_id')
            );

}

?>