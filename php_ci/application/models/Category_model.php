<?php

include('BaseModel.php');

class Category_model extends BaseModel {
	
	protected $table_name    = 'categorias';
	protected $list_fields   = array('id','nombre','tipo','activo');
	protected $search_fields = array('nombre');
	protected $save_fields   = array('nombre','tipo','activo');
	protected $edit_fields   = array('nombre','activo');
	protected $avoid_delete  = false;

}

?>