<?php

include('BaseModel.php');

class Subcategory_model extends BaseModel {
	
	protected $table_name    = 'subcategorias';
	protected $list_fields   = array('id','nombre','activo','categoria_id',
		                             'categorias.nombre AS categoria_nombre',
		                             'categorias.tipo AS categoria_tipo');
	protected $search_fields = array('nombre','categorias.nombre');
	protected $save_fields   = array('nombre','categoria_id','activo');
	// protected $edit_fields   = array('nombre','activo');
	protected $avoid_delete  = false;

	protected $join_tables = array(
  	          array('table' => 'categorias', 'type' => 'left', 'fk' => 'categoria_id')
            );

}

?>