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
}
