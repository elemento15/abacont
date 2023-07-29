<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'BasePdf.php';

class RptIncomesExpensesPdf extends BasePdf {

    private $months, $current_month, $download;

    public function __construct() {
    	parent::__construct();
    	$this->title = 'Reporte de Gastos vs. Ingresos';
        $this->subtitle = '';
        $this->download = false;
    }

    public function setParams($params) {
        $this->months = intval($params['months']) ?: 1000;
        $this->current_month = intval($params['current']);
    	
        $this->subtitle = ($this->months < 1000) ? 'Ultimos '.$this->months.' meses' : 'Todos los meses';
    }

    public function printing() {
    	$this->AddPage();

        $this->printReport();

        $destination = ($this->download) ? 'D' : 'I';
    	$this->Output('rpt.pdf', $destination);
    }

    private function printReport() {
        $data = $this->getData();

        $border = 'B';
        $fill = false;
        $curr_year = 0;
        
        $total_inc = 0;
        $total_exp = 0;
        $months = count($data);
        $sum = 0;

        // header
        $this->SetFont('Helvetica', 'B', 8);
        $this->Cell(30, 5, '', '', 0, '', false);
        $this->Cell(10, 5, 'Año', $border, 0, 'C', $fill);
        $this->Cell(10, 5, 'Mes', $border, 0, 'C', $fill);
        $this->Cell(25, 5, 'Ingresos', $border, 0, 'R', $fill);
        $this->Cell(25, 5, 'Gastos', $border, 0, 'R', $fill);
        $this->Cell(25, 5, 'Diferencia', $border, 0, 'R', $fill);
        $this->Cell(25, 5, 'Acumulado', $border, 0, 'R', $fill);
        $this->Cell(0,  5, '', '', 1, '', $fill);

        $this->SetFont('Helvetica', '', 9);
        $border = false;

        foreach ($data as $key => $item) {

            $txt_year = ($curr_year != $item['anio_fecha']) ? $item['anio_fecha'] : '';
            $txt_month = $this->getMonthName($item['mes_fecha']);

            $total_inc += $item['ingresos']; 
            $total_exp += $item['gastos'];
            $diff = $item['ingresos'] - $item['gastos'];
            $sum += $diff;

            $this->Cell(30, 5, '', $border, 0, '', false);
            $this->Cell(10, 5, $txt_year, $border, 0, 'C', $fill);
            $this->Cell(10, 5, $txt_month, $border, 0, 'C', $fill);
            $this->Cell(25, 5, $this->formatCurrency($item['ingresos']), $border, 0, 'R', $fill);
            $this->Cell(25, 5, $this->formatCurrency($item['gastos']), $border, 0, 'R', $fill);

            if ($diff < 0) {
                $this->SetTextColor(255, 0, 0);
            }
            $this->Cell(25, 5, $this->formatCurrency($diff), $border, 0, 'R', $fill);

            $this->SetTextColor(0, 0, 0, 100); // reset text color

            if ($sum < 0) {
                $this->SetTextColor(255, 0, 0);
            }
            $this->Cell(25, 5, $this->formatCurrency($sum), $border, 0, 'R', $fill);

            $this->Cell(0,  5, '', $border, 1, '', false);

            $this->SetTextColor(0, 0, 0, 100); // reset text color

            $curr_year = $item['anio_fecha'];
            $fill = !$fill;
        }

        // totals at footer
        $this->SetFont('Helvetica', 'B', 9);
        $border = 'T';

        $this->Cell(30, 5, '', '', 0, '', false);
        $this->Cell(10, 5, '', $border, 0, 'C', false);
        $this->Cell(10, 5, 'Totales:', $border, 0, 'R', false);
        $this->Cell(25, 5, $this->formatCurrency($total_inc), $border, 0, 'R', false);
        $this->Cell(25, 5, $this->formatCurrency($total_exp), $border, 0, 'R', false);

        if (($diff = $total_inc - $total_exp) < 0) {
            $this->SetTextColor(255, 0, 0);
        }

        $this->Cell(25, 5, $this->formatCurrency($diff), $border, 0, 'R', false);
        $this->Cell(25, 5, '', $border, 0, 'R', false);
        $this->Cell(0,  5, '', '', 1, '', false);

        $this->SetTextColor(0, 0, 0, 100); // reset text color

        $this->SetFont('Helvetica', 'B', 8);
        $border = '';

        $this->Cell(30, 4, '', $border, 0, '', false);
        $this->Cell(10, 4, '', $border, 0, 'C', false);
        $this->Cell(10, 4, 'Prom. mes:', $border, 0, 'R', false);
        $this->Cell(25, 4, $this->formatCurrency($total_inc / $months), $border, 0, 'R', false);
        $this->Cell(25, 4, $this->formatCurrency($total_exp / $months), $border, 0, 'R', false);

        $diff = ($total_inc - $total_exp)  / $months;
        if ($diff < 0) {
            $this->SetTextColor(255, 0, 0);
        }

        $this->Cell(25, 4, $this->formatCurrency($diff), $border, 0, 'R', false);
        $this->Cell(0,  4, '', '', 1, '', false);

        $this->SetTextColor(0, 0, 0, 100); // reset text color
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

/* End of file AccountPdf.php */