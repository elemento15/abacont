<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->model('User_model','user',true);

		date_default_timezone_set('America/Mazatlan');
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

	public function account_balances() {
		$data = [
			[
				'label' => 'Crédito (-)',
				'saldo' => $this->getBalanceByType('C', false),
				'color' => '#e45d5d',
			],[
				'label' => 'Ahorro',
				'saldo' => $this->getBalanceByType('D', 'A'),
				'color' => '#6694bb',
			],[
				'label' => 'Inversión',
				'saldo' => $this->getBalanceByType('D', 'I'),
				'color' => '#6694bb',
			],[
				'label' => 'Débito',
				'saldo' => $this->getBalanceByType('D', false),
				'color' => '#37658e',
			],[
				'label' => 'Efectivo',
				'saldo' => $this->getBalanceByType('E', false),
				'color' => '#5cb85c',
			]
		];

		$data2 = [];
		foreach ($data as $item) {
			if ($item['saldo'] != 0) {
				$data2[] = $item;
			}
		}

		echo json_encode($data2);
	}

	public function income_expense_month() {
		$start_of_month = date('Y-m-01');
		$query = "SELECT tipo, SUM(importe) AS total, 
		                 IF(tipo = 'I', 'Ingresos', 'Gastos') AS label
			FROM movimientos
			WHERE fecha >= '$start_of_month' 
			  AND NOT cancelado
			GROUP BY tipo
			ORDER BY tipo DESC; ";
		$data = $this->db->query($query)->result_array();
		echo json_encode($data);
	}

	public function income_expense_year() {
		$query = "SELECT tipo, SUM(importe) AS total, 
		                 IF(tipo = 'I', 'Ingresos', 'Gastos') AS label
			FROM movimientos
			WHERE fecha >= DATE_FORMAT(NOW(), '%Y-01-01') 
			  AND NOT cancelado
			GROUP BY tipo
			ORDER BY tipo DESC; ";
		$data = $this->db->query($query)->result_array();
		echo json_encode($data);
	}

	public function daily_balance_month() {
		$start_of_month = date('Y-m-01');
		$dates = $this->getListDaysInMonth(date('Y'), date('m'));
		$start_amount = $this->getBalanceAtStartMonth();
		$balance = $start_amount;

		// get current's month movements 
		$query = "SELECT fecha,
				SUM(IF(tipo = 'A', importe, 0)) AS abonos,
				SUM(IF(tipo = 'C', importe, 0)) AS cargos
			FROM movimientos_cuentas 
			WHERE fecha >= '$start_of_month' 
			  AND NOT cancelado
			GROUP BY fecha; ";
		$data = $this->db->query($query)->result_array();

		// parse data using 'date' as key
		$movs = [];
		foreach ($data as $key => $item) {
			$movs[$item['fecha']] = [
				'abonos' => $item['abonos'],
				'cargos' => $item['cargos'],
			];
		}

		foreach ($dates as $key => $date) {
			if ($date['fecha'] <= date('Y-m-d')) {
				// search for movements by date
				if (array_key_exists($date['fecha'], $movs)) {
					$balance = $balance + $movs[$date['fecha']]['abonos'] - $movs[$date['fecha']]['cargos'];
				}
				$dates[$key]['saldo'] = $balance;
			} else {
				$dates[$key]['saldo'] = 0;
			}
		}

		echo json_encode([
			'dates' => $dates,
			'initial_amount' => $start_amount
		]);
	}

	public function annual_summary_history() {
		$query = "SELECT 
				DATE_FORMAT(fecha, '%Y') AS annio,
				SUM(IF(tipo = 'I', importe, 0)) AS income, 
				SUM(IF(tipo = 'G', importe, 0)) AS expense
			FROM movimientos AS m
			WHERE NOT cancelado
			GROUP BY annio
			ORDER BY annio; ";
		$data = $this->db->query($query)->result_array();

		foreach ($data as $key => $item) {
			$diff = $item['income'] -$item['expense'];
			$data[$key]['diff'] = $diff;
			$data[$key]['color'] = ($diff >= 0) ? '#5cb85c' : '#F7DC6F';
		}

		echo json_encode($data);
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

	private function getBalanceByType($type, $class) {
		$query = "SELECT SUM(ABS(saldo)) AS saldo FROM cuentas ";
		
		if ($type == 'D') {
			if ($class == 'A') {
				$query .= " WHERE es_ahorro ";
			} else if ($class == 'I') {
				$query .= " WHERE es_inversion AND NOT es_ahorro ";
			} else {
				$query .= " WHERE tipo = 'D' AND (NOT es_ahorro AND NOT es_inversion) ";
			}
		} else {
			$query .= " WHERE tipo = '$type' ";
		}
		$result = $this->db->query($query)->result_array();
		return $result[0]['saldo'];
	}

	private function getListDaysInMonth($year, $month) {
		$list = array();

		if ($month) {
			$days = cal_days_in_month(CAL_GREGORIAN, $month, $year);
			
			for ($i=1; $i <= $days ; $i++) {
				$txt_month = str_pad($month, 2, '0', STR_PAD_LEFT);
				$txt_day = str_pad($i, 2, '0', STR_PAD_LEFT);
				$list[] = array('fecha' => $year.'-'.$txt_month.'-'.$txt_day);
			}

		} else {
			for ($i=1; $i <= 12 ; $i++) { 
				$arr_days = $this->getListDaysInMonth($year, $i);
				$list = array_merge($list, $arr_days);
			}
		}

		return $list;
	}

	private function getBalanceAtStartMonth() {
		$month = date('m');

		$query = "SELECT SUM(IF(tipo = 'A', importe, importe * -1)) AS saldo_inicial 
			FROM movimientos_cuentas 
			WHERE fecha < DATE_FORMAT(NOW(), '%Y-$month-01') 
			AND NOT cancelado; ";
		$result = $this->db->query($query)->result_array();
		return $result[0]['saldo_inicial'];
	}
}
