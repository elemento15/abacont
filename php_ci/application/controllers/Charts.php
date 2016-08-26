<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Charts extends CI_Controller {

	public function __construct() {
		parent::__construct();

	    $session = $this->session->userdata;
	    if (!(isset($session['user']) && $session['user'])) {
	      throw new Exception("Session inactive");
	    }

	    $this->load->model('Movement_model','modelMov',true);
	}

	public function expenses_day() {
		$month = $_POST['month'];
		$year = intval($_POST['year']);
		
		$data = $this->modelMov->movs_grouped('D', 'G', $month, $year);
		echo json_encode($data);
	}

	public function expenses_months() {
		$query = $this->db->query("
			select DATE_FORMAT(fecha, '%Y-%m') AS fecha, SUM(importe) AS total 
			FROM movimientos 
			WHERE tipo = 'G' AND NOT cancelado 
			GROUP BY DATE_FORMAT(fecha, '%Y-%m') 
			ORDER BY fecha DESC 
			LIMIT 12;");

		$data = $query->result_array();
		echo json_encode($data);
	}
}
