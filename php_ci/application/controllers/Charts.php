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
	}

	public function expenses_day() {
		$month         = $_POST['month'];
		$year          = intval($_POST['year']);
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		
		$data = $this->modelMov->movs_grouped('D', 'G', $month, $year, $category, $subcategory);
		echo json_encode($data);
	}

	public function expenses_months() {
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);

		$filters = '';
		$filters.= ($category)        ? " AND cat.id = $category " : "";
		$filters.= ($subcategory)     ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, SUM(mov.importe) AS total 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT 24;");

		$data = $query->result_array();
		echo json_encode($data);
	}

	public function average_months() {
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);

		$filters = '';
		$filters.= ($category)    ? " AND cat.id = $category " : "";
		$filters.= ($subcategory) ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS mov_fecha, 
			       SUM(mov.importe) AS sum_importe 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY mov_fecha 
			ORDER BY mov_fecha DESC 
			LIMIT 24;");

		$data = $query->result_array();

		foreach ($data as $key => $item) {
			$date = explode('-', $item['mov_fecha']);

			// on current month, set current days
			if (date('Y') == $date[0] && date('m') == $date[1]) {
				$days = date('d');
			} else {
				$days = cal_days_in_month(CAL_GREGORIAN, $date[1], $date[0]);
			}

			$data[$key]['total'] = round($item['sum_importe'] / $days, 2);
		}

		echo json_encode($data);
	}
}
