<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class RptIncomesExpensesCsv {

    private $months, $current_month;

    public function __construct() {
    	//
    }

    public function setParams($params) {
        $this->months = intval($params['months']) ?: 1000;
        $this->current_month = intval($params['current']);
    }

    public function printing() {
    	header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=inc_vs_exp.csv');
        
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('MES', 'INGRESOS', 'GASTOS', 'DIFERENCIA', 'ACUMULADO'));
        $data = $this->getData();
        $sum = 0;

        foreach ($data as $key => $item) {
            $diff = $item['ingresos'] - $item['gastos'];
            $sum += $diff;
            $month = ($item['mes_fecha'] > 9) ? $item['mes_fecha'] : '0'.$item['mes_fecha'];

            fputcsv($output, array(
                $item['anio_fecha'].'-'.$month,
                $item['ingresos'],
                $item['gastos'],
                $diff,
                $sum
            ));
        }
    }


    private function getData() {
        $dates = $this->getDates();
        $end_date = ($this->current_month) ? $dates['curr_date'] : $dates['end_date'];
        
        $CI =& get_instance();

        $CI->db->select('YEAR(fecha) as anio_fecha, MONTH(fecha) as mes_fecha, 
                         SUM(IF(tipo = "G", importe, 0)) AS gastos, 
                         SUM(IF(tipo = "I", importe, 0)) AS ingresos');
        $CI->db->from('movimientos AS mov');
        
        $CI->db->where('mov.fecha BETWEEN "'.$dates['start_date'].'" AND "'.$end_date.'"');
        $CI->db->where('mov.cancelado', 0);

        $CI->db->group_by('anio_fecha');
        $CI->db->group_by('mes_fecha');

        $CI->db->order_by('anio_fecha', 'ASC');
        $CI->db->order_by('mes_fecha', 'ASC');

        $data = $CI->db->get();

        return $data->result_array();
    }

    private function getDates() {
        $months = $this->months;

        $CI =& get_instance();
        $CI->db->select('DATE_FORMAT(SUBDATE(NOW(), INTERVAL '.$months.' MONTH), "%Y-%m-01") AS start_date,
                         SUBDATE(DATE_FORMAT(NOW(), "%Y-%m-01"), INTERVAL 1 DAY) AS end_date,
                         DATE_FORMAT(NOW(), "%Y-%m-%d") AS curr_date;');
        $data = $CI->db->get();
        return $data->row_array();
    }
}

/* End of file RptIncomesExpensesCsv.php */