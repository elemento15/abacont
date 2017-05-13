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
		$month         = $_POST['month'];
		$year          = intval($_POST['year']);
		$extraordinary = intval($_POST['extraordinary']);
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);
		
		$data = $this->modelMov->movs_grouped('D', 'G', $month, $year, $extraordinary, $category, $subcategory);
		echo json_encode($data);
	}

	public function expenses_months() {
		$extraordinary = intval($_POST['extraordinary']);
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);

		$filters = '';
		$filters.= (! $extraordinary) ? " AND NOT mov.extraordinario " : "";
		$filters.= ($category)        ? " AND cat.id = $category " : "";
		$filters.= ($subcategory)     ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS fecha, SUM(mov.importe) AS total 
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY DATE_FORMAT(mov.fecha, '%Y-%m') 
			ORDER BY mov.fecha DESC 
			LIMIT 24;");

		$data = $query->result_array();
		echo json_encode($data);
	}

	public function average_months() {
		$extraordinary = intval($_POST['extraordinary']);
		$category      = intval($_POST['category']);
		$subcategory   = intval($_POST['subcategory']);

		$filters = '';
		$filters.= (! $extraordinary) ? " AND NOT mov.extraordinario " : "";
		$filters.= ($category)        ? " AND cat.id = $category " : "";
		$filters.= ($subcategory)     ? " AND subcat.id = $subcategory " : "";

		$query = $this->db->query("
			select DATE_FORMAT(mov.fecha, '%Y-%m') AS fecha, SUBSTR(LAST_DAY(mov.fecha), 9) AS dias, 
			       SUM(mov.importe) AS total
			FROM movimientos AS mov 
			LEFT JOIN subcategorias AS subcat ON subcat.id = mov.subcategoria_id 
			LEFT JOIN categorias AS cat ON cat.id = subcat.categoria_id 
			WHERE mov.tipo = 'G' AND NOT mov.cancelado $filters 
			GROUP BY DATE_FORMAT(mov.fecha, '%Y-%m') 
			ORDER BY mov.fecha DESC 
			LIMIT 24;");

		$data = $query->result_array();

		foreach ($data as $key => $item) {
			// on current month, set current days
			if ($item['fecha'] == date('Y-m')) {
				$item['dias'] = date('d');
			}

			$data[$key]['total'] = round($item['total'] / $item['dias'], 2);
		}

		echo json_encode($data);
	}
}
