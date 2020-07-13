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
		$months        = intval($_POST['months']);
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
		$months        = intval($_POST['months']);

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
		$months        = intval($_POST['months']);
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
		$months        = intval($_POST['months']);

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

		$account = intval($_POST['account']);

		if ($account) {
			// get the type
			$model_account = $this->account->find($account);

			$query = $this->db->query("
				select date_format(fecha, '%Y-%m') as anio_mes,
					(SUM(IF(mc.tipo = 'A', mc.importe, mc.importe * -1)) + 
					 IFNULL(
						(select SUM(IF(mc2.tipo = 'A', mc2.importe, mc2.importe * -1)) as saldo_anterior 
						 from movimientos_cuentas as mc2 
						 left join cuentas as cta2 on cta2.id = mc2.cuenta_id 
						 where cta2.id = cta.id and mc2.cancelado = 0 and mc2.fecha < concat(anio_mes,'-01') 
				        ), 0)) as saldo 
				FROM movimientos_cuentas as mc 
				LEFT JOIN cuentas as cta on cta.id = mc.cuenta_id 
				WHERE cta.id = '$account' AND mc.cancelado = 0 
				GROUP BY anio_mes;");

			if ($model_account->tipo == 'D' || $model_account->tipo == 'E' || $model_account->tipo == 'I') {
				$debit = $query->result_array();
			} else {
				$credit = $query->result_array();
			}

		} else {
			// debit
			$query = $this->db->query("
				select date_format(fecha, '%Y-%m') as anio_mes,
					(SUM(IF(mc.tipo = 'A', mc.importe, mc.importe * -1)) + 
					 IFNULL(
						(select SUM(IF(mc2.tipo = 'A', mc2.importe, mc2.importe * -1)) as saldo_anterior 
						 from movimientos_cuentas as mc2 
						 left join cuentas as cta2 on cta2.id = mc2.cuenta_id 
						 where cta2.tipo = cta.tipo and mc2.cancelado = 0 and mc2.fecha < concat(anio_mes,'-01') 
				        ), 0)) as saldo 
				FROM movimientos_cuentas as mc 
				LEFT JOIN cuentas as cta on cta.id = mc.cuenta_id 
				WHERE (cta.tipo = 'D' OR cta.tipo = 'I') AND mc.cancelado = 0 
				GROUP BY anio_mes;");
			$debit = $query->result_array();

			// credit
			$query = $this->db->query("
				select date_format(fecha, '%Y-%m') as anio_mes,
					(SUM(IF(mc.tipo = 'A', mc.importe, mc.importe * -1)) + 
					 IFNULL(
						(select SUM(IF(mc2.tipo = 'A', mc2.importe, mc2.importe * -1)) as saldo_anterior 
						 from movimientos_cuentas as mc2 
						 left join cuentas as cta2 on cta2.id = mc2.cuenta_id 
						 where cta2.tipo = cta.tipo and mc2.cancelado = 0 and mc2.fecha < concat(anio_mes,'-01') 
				        ), 0)) as saldo 
				FROM movimientos_cuentas as mc 
				LEFT JOIN cuentas as cta on cta.id = mc.cuenta_id 
				WHERE cta.tipo = 'C' AND mc.cancelado = 0 
				GROUP BY anio_mes;");
			$credit = $query->result_array();
		}

		echo json_encode(array('debit' => $debit, 'credit' => $credit));
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
}
