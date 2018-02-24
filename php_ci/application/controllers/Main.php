<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User_model','user',true);
	}

	public function index() {
		$session = $this->session->userdata;
		if (isset($session['user']) && $session['user']) {
			$this->load->view('main');
		} else {
			$this->load->view('login');
		}
	}

	public function login() {
		$user = $_POST['user'];
		$pass = $_POST['pass'];
		$success = false;

		if ($user = $this->user->findUser($user)) {
			if ($user->pass == md5($pass) && $user->activo) {
				// session_start();
				$_SESSION['user'] = $user;
				$success = true;
			}
		}

		if ($success) {
			$response = array('success' => true);
		} else {
			$response = array('success' => false, 'msg' => 'Usuario incorrecto');
		}
		
		echo json_encode($response);
	}

	public function logout() {
		// session_start();
    	session_destroy();
    	echo json_encode(array('success' => true));
	}

	public function kpis() {
		// get dates for 30 days and 6 months
		$query = $this->db->query("
			select DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 30 DAY), '%Y-%m-%d') AS date30d, 
			       DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 6 MONTH), '%Y-%m-%d') AS date6m, 
			       DATEDIFF(NOW(), DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 6 MONTH), '%Y-%m-%d')) AS days6m;");

		$res = $query->result_array();
		$dates = $res[0];

		// get data
		$query = $this->db->query("
			select (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date30d']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) / 30 AS ing30d, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date30d']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) / 30 AS exp30d, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date6m']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) / ".$dates['days6m']." AS ing6m, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date6m']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) / ".$dates['days6m']." AS exp6m;");

		$data = $query->result_array();
		echo json_encode($data[0]);
	}
}
