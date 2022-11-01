<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Charts extends CI_Controller {

	public function __construct() {
		parent::__construct();

		ini_set('date.timezone', 'America/Mazatlan');

	    $session = $this->session->userdata;
	    if (!(isset($session['user']) && $session['user'])) {
	      throw new Exception("Session inactive");
	    }

	    $this->load->model('Movement_model','modelMov',true);
	    $this->load->model('Account_model','account',true);
	    $this->load->model('MovAccount_model','modelMovAcc',true);
	}

	public function expenses_day() {
		$data          = [];
		$month         = $_POST['month'];
		$year          = intval($_POST['year']);
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		
		$records = $this->modelMov->movs_grouped('D', 'G', $month, $year, $category, $subcategory);
		

		// create list of days in the period
		$data = $this->getListDaysInMonth($year, $month);

		foreach ($data as $index => $item) {
			$data[$index]['total'] = 0;

			foreach ($records as $rec) {
				if ($rec->fecha == $item['fecha']) {
					$data[$index]['total'] = $rec->total;
				}
			}
		}

		echo json_encode($data);
	}

	public function expenses_months() {
		$data = [];

		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		$months        = intval($_POST['months']) ?: $this->getMonthsSinceFirstMov();
		$msi           = intval($_POST['msi']);

		$filters = '';
		$filters.= ($category)    ? " AND cat.id = $category " : "";
		$filters.= ($subcategory) ? " AND subcat.id = $subcategory " : "";
		$filters.= " AND mov.es_meses_sin_intereses = $msi ";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, SUM(mov.importe) AS total 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT $months ;");

		$records = $query->result_array();

		// create list of months in the period
		$data = $this->getListPastMonths($months);

		foreach ($data as $index => $item) {
			$data[$index]['total'] = 0;

			foreach ($records as $rec) {
				if ($rec['mov_fecha'] == $item['mov_fecha']) {
					$data[$index]['total'] = $rec['total'];
				}
			}
		}

		echo json_encode( array_reverse($data) );
	}

	public function incomes_months() {
		$data = [];

		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		$months        = intval($_POST['months']) ?: $this->getMonthsSinceFirstMov();

		$filters = '';
		$filters.= ($category)        ? " AND cat.id = $category " : "";
		$filters.= ($subcategory)     ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, SUM(mov.importe) AS total 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'I' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT $months ;");

		$records = $query->result_array();

		// create list of months in the period
		$data = $this->getListPastMonths($months);

		foreach ($data as $index => $item) {
			$data[$index]['total'] = 0;

			foreach ($records as $rec) {
				if ($rec['mov_fecha'] == $item['mov_fecha']) {
					$data[$index]['total'] = $rec['total'];
				}
			}
		}

		echo json_encode( array_reverse($data) );
	}

	public function expenses_months_avg() {
		$data = [];

		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		$months        = intval($_POST['months']) ?: $this->getMonthsSinceFirstMov();;
		$msi           = intval($_POST['msi']);

		$filters = '';
		$filters.= ($category)    ? " AND cat.id = $category " : "";
		$filters.= ($subcategory) ? " AND subcat.id = $subcategory " : "";
		$filters.= " AND mov.es_meses_sin_intereses = $msi ";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, 
			       SUM(mov.importe) AS sum_importe 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT $months ;");

		$records = $query->result_array();


		// create list of months in the period
		$data = $this->getListPastMonths($months);

		foreach ($data as $index => $item) {
			$data[$index]['total'] = 0;

			foreach ($records as $rec) {
				if ($rec['mov_fecha'] == $item['mov_fecha']) {
					
					$date = explode('-', $rec['mov_fecha']);

					// on current month, set current days
					if (date('Y') == $date[0] && date('m') == $date[1]) {
						$days = date('d');
					} else {
						$days = cal_days_in_month(CAL_GREGORIAN, $date[1], $date[0]);
					}

					$data[$index]['total'] = round($rec['sum_importe'] / $days, 2);
				}
			}
		}

		echo json_encode( array_reverse($data) );
	}

	public function incomes_months_avg() {
		$data = [];

		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		$months        = intval($_POST['months']) ?: $this->getMonthsSinceFirstMov();;

		$filters = '';
		$filters.= ($category)    ? " AND cat.id = $category " : "";
		$filters.= ($subcategory) ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, 
			       SUM(mov.importe) AS sum_importe 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'I' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT $months ;");

		$records = $query->result_array();


		// create list of months in the period
		$data = $this->getListPastMonths($months);

		foreach ($data as $index => $item) {
			$data[$index]['total'] = 0;

			foreach ($records as $rec) {
				if ($rec['mov_fecha'] == $item['mov_fecha']) {
					
					$date = explode('-', $rec['mov_fecha']);

					// on current month, set current days
					if (date('Y') == $date[0] && date('m') == $date[1]) {
						$days = date('d');
					} else {
						$days = cal_days_in_month(CAL_GREGORIAN, $date[1], $date[0]);
					}

					$data[$index]['total'] = round($rec['sum_importe'] / $days, 2);
				}
			}
		}

		echo json_encode( array_reverse($data) );
	}

	public function balance_months() {
		$debit = [];
		$credit = [];

		$periods = $this->getPeriodsList($this->modelMovAcc->firstMovDate());

		if ($type = $_POST['type']) {
			$account = intval($_POST['account']);
			
			if ($account) {
				$model_account = $this->account->find($account);
				$type = $model_account->tipo;
			}

			if ($type == 'E' || $type == 'D' || $type == 'I') {
				$debit = $this->getPeriodsBalance($type, $account, $periods);
				$credit = [];
			} else if ($type == 'C') {
				$debit = [];
				$credit = $this->getPeriodsBalance($type, $account, $periods);
			} else {
				$debit = [];
				$credit = [];
			}

		} else {
			$omitInversions = $_POST['omitInversions'];
			$types = (! $_POST['omitInversions']) ? ["'E'","'D'","'I'"] : ["'E'","'D'"];

			$debit = $this->getPeriodsBalance($types, false, $periods);
			$credit = $this->getPeriodsBalance('C', false, $periods);
		}

		echo json_encode(array('debit' => $debit, 'credit' => $credit));
	}

	public function movements_percent() {
		$type = $_POST['type'];
		$category = intval($_POST['category']);
		$date_ini = $_POST['date_ini'];
		$date_end = $_POST['date_end'];

		if ($type != 'I' && $type != 'G') {
			echo json_encode([]);
			return false;
		}

		if ($category) {
			$query = $this->db->query("
				select s.id AS sID, s.nombre, SUM(m.importe) AS total 
				FROM movimientos AS m 
				LEFT JOIN subcategorias AS s ON s.id = m.subcategoria_id 
				WHERE m.fecha BETWEEN '$date_ini' AND '$date_end' 
				  AND m.tipo = '$type'
				  AND s.categoria_id = '$category'
				  AND NOT m.cancelado 
				GROUP BY s.id 
				ORDER BY total DESC ;");
		} else {
			$query = $this->db->query("
				select c.id AS cID, c.nombre, SUM(m.importe) AS total 
				FROM movimientos AS m 
				LEFT JOIN subcategorias AS s ON s.id = m.subcategoria_id 
				LEFT JOIN categorias AS c ON c.id = s.categoria_id 
				WHERE m.fecha BETWEEN '$date_ini' AND '$date_end' 
				  AND m.tipo = '$type' 
				  AND NOT m.cancelado 
				GROUP BY c.id 
				ORDER BY total DESC ;");
		}

		$data = $query->result_array();
		echo json_encode($data);
	}


	protected function getListPastMonths($months) {
		$date = new DateTime();
		$date->setDate($date->format('Y'), $date->format('m'), 1); // set to first day of current month

		$list = array( array('mov_fecha' => $date->format('Y-m')) );

		for ($i=0; $i < $months - 1; $i++) { 
			$list[] = array('mov_fecha' => $date->sub(date_interval_create_from_date_string('1 month'))->format('Y-m'));
		}

		return $list;
	}

	protected function getListDaysInMonth($year, $month) {
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

	protected function getMonthsSinceFirstMov() {
		$sql = "select TIMESTAMPDIFF(
		                  MONTH, 
		                  (SELECT DATE_FORMAT(fecha, '%Y-%m-01') FROM movimientos WHERE NOT cancelado LIMIT 1), 
		                  LAST_DAY(NOW())
		               ) AS total_months ";
		$query = $this->db->query($sql);
        return $query->row()->total_months + 1;
	}


	private function getPeriodsList($start_date) {
		$periods = array();
		$dt = explode('-', $start_date);
		$period = $dt[0].'-'.$dt[1];
		$now = explode('-', date('Y-m'));
		$today = $now[0].'-'.$now[1];
		
		do {
			$periods[] = $period;
			$period = $this->getNextPeriod($period);
		} while ($period <= $today);

		return $periods;
	}

	private function getNextPeriod($period) {
		$dt = explode('-', $period);

		$year = intval($dt[0]);
		$month = intval($dt[1]);

		if ($month < 12) {
			$month++;
		} else {
			$month = 1;
			$year++;
		}

		return $year . '-' . (($month < 10) ? ('0' . $month) : $month);
	}

	private function getPeriodsBalance($type, $account, $periods) {
		$data = array();
		$amounts = $this->getAccountsMovsByPeriod($type, $account);
		$balance = 0;

		foreach ($periods as $period) {
			if (array_key_exists($period, $amounts)) {
				$balance += $amounts[$period];
			}

			$data[] = array(
				'anio_mes' => $period,
				'saldo' => $balance
			);
		}

		return $data;
	}

	private function getAccountsMovsByPeriod($type = false, $account = false) {
		$type_filter = '';
		$account_filter = '';

		if ($type) {
			if (gettype($type) == 'array') {
				$type_filter = " AND c.tipo IN (". implode(',',$type) .") ";
			} else {
				$type_filter = " AND c.tipo = '$type' ";
			}
		}

		if ($account) {
			$account_filter = " AND c.id = '$account' ";
		}

		$sql = "select DATE_FORMAT(mc.fecha, '%Y-%m') AS periodo, 
                       SUM(IF(mc.tipo = 'A', importe, importe * -1)) AS total 
                FROM movimientos_cuentas AS mc 
                LEFT JOIN cuentas AS c ON c.id = mc.cuenta_id 
                WHERE NOT mc.cancelado $type_filter $account_filter 
                GROUP BY periodo 
                ORDER BY periodo ";

        $query = $this->db->query($sql);
        $records = $query->result_array();

        $data = array();
        foreach ($records as $item) {
        	$data[$item['periodo']] = $item['total'];
        }

        return $data;
	}
}
