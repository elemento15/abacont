<?php

include_once('BaseModel.php');

class User_model extends BaseModel {

	protected $table_name    = 'usuarios';
	protected $list_fields   = array('id','usuario','nombre','email','activo','dbase');
	protected $search_fields = array('usuario','nombre','email');
	protected $save_fields   = array('usuario','nombre','email','activo','dbase');
	// protected $edit_fields   = array();
	protected $avoid_delete  = true;

	public function __construct() {
    	$this->db = $this->load->database('master', true);
    	parent::__construct();
	}

	public function findUser($user) {
	    $query = $this->db->get_where($this->table_name, array('usuario' => $user));
	    $row = $query->row();
	    if (!$row) {
	      return false;
	    }
	    return $row;
	}

	public function findEmail($email) {
	    $query = $this->db->get_where($this->table_name, array('email' => $email));
	    $row = $query->row();
	    if (!$row) {
	      return false;
	    }
	    return $row;
	}
}

?>