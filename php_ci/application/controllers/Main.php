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

	public function signin() {
		$this->load->view('register');
	}

	public function register() {
		$data = $_POST;
		$error = false;

		if (!$data['user'] || strlen($data['user']) < 6 || preg_match('/[^a-z|0-9]/i', $data['user'])) {
			$error = 'Usuario invalido';
		}

		if (!$data['name']) {
			$error = 'Nombre invalido';
		}

		if (!$data['email'] || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			$error = 'Email invalido';
		}

		if (!$data['pass'] || strlen($data['pass']) < 6) {
			$error = 'Password invalido';
		}

		if (!$data['confirm'] || $data['pass'] != $data['confirm']) {
			$error = 'Confirmacion invalida';
		}


		// avoid duplicated user|email
		if ($this->user->findUser($data['user']) || $this->user->findEmail($data['email'])) {
			$error = 'Usuario ó email existente';
		}

		if ($error) {
			echo json_encode(array('success' => false, 'msg' => $error));
			exit;
		}

		$data = array(
			'id' => 0,
			'usuario' => $data['user'],
			'nombre' => $data['name'],
			'email' => $data['email'],
			'pass' => md5($data['pass']),
			'fecha' => date('Y-m-d h:i:s'),
			'activo' => false,
			'dbase' => 'db_abacont_'.str_replace(' ', '', $data['user'])
		);

		// create the new user
		if ($this->user->save($data, true)) {
			echo json_encode(array('success' => true));
		} else {
			echo json_encode(array('success' => false, 'msg' => 'Error al registrarse'));
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

	public function all() {
		// main/all
		$this->load->view('all_users');
	}

	public function allUsers() {
		$data = $this->user->getAll();
		$response = [];

		foreach ($data as $item) {
			$response[] = [
				'usuario' => $item->usuario,
				'nombre'  => $item->nombre,
				'activo'  => $item->activo,
				'fecha'   => $item->fecha
			];
		}

		echo json_encode($response);
	}

	public function kpis() {
		// get dates for 30 days and 6 months
		$query = $this->db->query("
			select DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 30 DAY), '%Y-%m-%d') AS date30d, 
			       DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 6 MONTH), '%Y-%m-%d') AS date6m, 
			       DATEDIFF(NOW(), DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 6 MONTH), '%Y-%m-%d')) AS days6m,
			       DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 12 MONTH), '%Y-%m-%d') AS date12m;");

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
			              tipo = 'G' AND NOT cancelado) / ".$dates['days6m']." AS exp6m,
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date12m']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) / 365 AS ing12m,
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date12m']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) / 365 AS exp12m,
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date30d']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) AS ingtot30d, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date30d']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) AS exptot30d, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date6m']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) AS ingtot6m, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date6m']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) AS exptot6m,
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date12m']."' AND NOW() AND 
			              tipo = 'I' AND NOT cancelado) AS ingtot12m, 
			       (SELECT SUM(importe) 
			        FROM movimientos 
			        WHERE fecha BETWEEN '".$dates['date12m']."' AND NOW() AND 
			              tipo = 'G' AND NOT cancelado) AS exptot12m");

		$data = $query->result_array();
		echo json_encode($data[0]);
	}

	public function get_user() {
		$user = $this->getCurrentUser();
		echo json_encode(array('success' => true, 'user' => $user));
	}

	public function update_user() {
		$user = $this->getCurrentUser();

		$data = array(
			'id' => $user['id'],
			'nombre' => $_POST['name'],
			'email' => $_POST['email']
		);

		if (! filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			echo json_encode(array('success' => false, 'msg' => 'Email invalido'));
			exit;
		}

		if ($this->user->save($data, true)) {
			$_SESSION['user'] = $this->user->findUser($user['user']);
			$response = array('success' => true, 'user' => $this->getCurrentUser());
		} else {
			$response = array('success' => false, 'msg' => 'Error al actualizar el usuario actual');
		}

		echo json_encode($response);
	}

	public function change_pass() {
		$pass = $_POST['pass'];
		$confirm = $_POST['confirm'];
		$user = $this->getCurrentUser();
		
		if ($pass == '' || $confirm == '') {
			echo json_encode(array('success' => false, 'msg' => 'Password o confirmacion invalida'));
			exit;
		}

		if ($pass != $confirm) {
			echo json_encode(array('success' => false, 'msg' => 'Password y confirmacion no coinciden'));
			exit;
		}

		$data = array(
			'id' => $user['id'],
			'pass' => md5($pass)
		);

		// change password in user's table
		if (! $this->user->save($data, true)) {
			echo json_encode(array('success' => false, 'msg' => 'Error al cambiar el password'));
			exit;
		}

		echo json_encode(array('success' => true));
	}


	private function getCurrentUser() {
		$usr = $_SESSION['user'];

		// get the display name
		$name = explode(' ', $usr->nombre);
		$display = '';
		$i = 0;
		
		do {
			$text = $name[$i].' ';
			if (strlen($display . $text) < 16) {
				$display = $display . $text;
				$i++;
			} else {
				$i = 1000;
			}
		} while ($i < count($name));


		return array(
			'id'   => $usr->id,
			'user' => $usr->usuario,
			'name' => $usr->nombre,
			'email' => $usr->email,
			'display' => $display
		);
	}
}
