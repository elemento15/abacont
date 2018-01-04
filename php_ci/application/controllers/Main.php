<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct() {
		parent::__construct();
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

		if ($user == $this->config->item('login_user') && md5($pass) == $this->config->item('login_pass')) {
			// session_start();
			$_SESSION['user'] = $user;

			$response = array('success' => true);
		} else {
			$response = array('success' => false, 'msg' => 'User or password incorrect');
		}

		echo json_encode($response);
	}

	public function logout() {
		// session_start();
    	session_destroy();
    	echo json_encode(array('success' => true));
	}
}
