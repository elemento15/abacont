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
}
