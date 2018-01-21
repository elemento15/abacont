<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'BasePdf.php';

class MovementsPdf extends BasePdf {

    private $rpt, $type, $account, $date_ini, $date_end, $comments, $download;

    public function __construct() {
    	parent::__construct();
    	$this->title = 'Reporte de ';
    	$this->subtitle = '';
    }

    public function setParams($params) {
        $this->rpt = $params['rpt'];
        $this->type = $params['type'];
        $this->account = intval($params['account']);
        $this->category = intval($params['category']);
        $this->subcategory = intval($params['subcategory']);
        $this->date_ini = $params['date_ini'];
        $this->date_end = $params['date_end'];
        $this->comments = intval($params['comments']);
        $this->download = intval($params['download']);

        if ($this->rpt != 'D' && $this->rpt != 'C') {
            echo "Report Unknown"; exit;
        }

        if ($this->type != 'G' && $this->type != 'I') {
            echo "Report Type Unknown"; exit;
        }

        $this->title .= ($this->type == 'G') ? 'Gastos' : 'Ingresos';
        $this->subtitle = ($this->rpt == 'D') ? 'Detallado' : 'Concentrado';

        if (! $this->validDate($this->date_ini)) {
            echo "Invalid Initial Date"; exit;
        }

        if (! $this->validDate($this->date_end)) {
            echo "Invalid Final Date"; exit;
        }
    }

    public function printing() {
    	$this->AddPage();

        if ($this->rpt == 'D') {
            $this->printDetailed();
        } else {
            $this->printConcentrated();
        }

        $destination = ($this->download) ? 'D' : 'I';
    	$this->Output('rpt.pdf', $destination);
    }

    private function printDetailed() {
    	$border = false;
    	$fill = false;
        $data = $this->getDataDetailed();
        $total = 0;


        foreach ($data as $key => $item) {
            $this->SetFont('Helvetica', '', 9);

            $this->Cell(22, 5, $item['fecha'], $border, 0, 'C', $fill);
            $this->Cell(45, 5, $item['nombre_subcategoria'], $border, 0, 'L', $fill, '', 1);
            $this->Cell(45, 5, $item['nombre_categoria'], $border, 0, 'L', $fill, '', 1);
            $this->Cell(40, 5, $item['nombre_cuenta'], $border, 0, 'L', $fill, '', 1);
            $this->Cell(20, 5, $this->formatCurrency($item['importe']), $border, 0, 'R', $fill);
            $this->Cell(0,  5, '', $border, 1, '', $fill);

            if ($this->comments && trim($item['observaciones'])) {
                $this->SetFont('Helvetica', '', 8);

                $this->Cell(22, 3, '', $border, 0, '', $fill);
                $this->Cell(0,  3, 'Observaciones: '.$item['observaciones'], $border, 1, '', $fill);
            }

            $total += $item['importe'];
            $fill = !$fill;
        }

        $this->Ln(3);
        $this->SetFont('Helvetica', 'B', 11);

        $this->Cell(115, 7, '', false, 0, '', false);
        $this->Cell(30, 7, 'Total: ', false, 0, 'R', false);
        
        $this->Cell(30, 7, $this->formatCurrency($total), false, 0, 'R', false);
        $this->Cell(0,  7, '', false, 1, '', false);
    }

    private function printConcentrated() {
        $data = $this->getDataConcentrated();

        $total = 0;
        $category = 0;

        foreach ($data as $key => $item) {
            if ($category != $item['id_categoria']) {
                if ($category) $this->Ln(3);

                $border = 'B';
                $this->SetFont('Helvetica', 'B', 10);
                $this->Cell(75, 5, $item['nombre_categoria'], $border, 0, 'L', false);

                $subtotal = $this->getSubTotalCategory($item['id_categoria'], $data);
                $this->Cell(25, 5, $subtotal, $border, 1, 'R', false);


                $category = $item['id_categoria'];
                $fill = false;
            }

            $border = false;
            $this->SetFont('Helvetica', '', 9);

            $this->Cell(5,  5, '', $border, 0, '', false);
            $this->Cell(70, 5, $item['nombre_subcategoria'], $border, 0, 'L', $fill, '', 1);
            $this->Cell(25, 5, $this->formatCurrency($item['importe']), $border, 1, 'R', $fill);
            // $this->Cell(0,  5, '', $border, 1, '', $fill);

            $total += $item['importe'];
            $fill = !$fill;
        }

        $this->Ln(3);
        $this->SetFont('Helvetica', 'B', 11);

        $this->Cell(115, 7, '', false, 0, '', false);
        $this->Cell(30, 7, 'Total: ', false, 0, 'R', false);
        
        $this->Cell(30, 7, $this->formatCurrency($total), false, 0, 'R', false);
        $this->Cell(0,  7, '', false, 1, '', false);
    }

    private function getDataDetailed() {
        $CI =& get_instance();

        $CI->db->select('mov.id, mov.fecha, mov.importe, mov.observaciones, cue.nombre AS nombre_cuenta,
                         sub.nombre AS nombre_subcategoria, cat.nombre AS nombre_categoria');
        $CI->db->from('movimientos AS mov');
        $CI->db->join('subcategorias AS sub', 'sub.id = mov.subcategoria_id', 'left');
        $CI->db->join('categorias AS cat', 'cat.id = sub.categoria_id', 'left');
        $CI->db->join('movimientos_cuentas AS mov_cue', 'mov_cue.id = mov.movimiento_cuenta_id', 'left');
        $CI->db->join('cuentas AS cue', 'cue.id = mov_cue.cuenta_id', 'left');
        
        $CI->db->where('mov.fecha BETWEEN "'.$this->date_ini.'" AND "'.$this->date_end.'"');
        $CI->db->where('mov.tipo', $this->type);
        $CI->db->where('mov.cancelado', 0);
        $CI->db->where('mov.extraordinario', 0);

        if ($this->account) {
            $CI->db->where('mov_cue.cuenta_id', $this->account);
        }

        if ($this->category) {
            $CI->db->where('cat.id', $this->category);
        }

        if ($this->subcategory) {
            $CI->db->where('sub.id', $this->subcategory);
        }

        $CI->db->order_by('mov.fecha', 'ASC');
        $CI->db->order_by('mov.id', 'ASC');

        $data = $CI->db->get();
        return $data->result_array();
    }

    private function getDataConcentrated() {
        $CI =& get_instance();

        $CI->db->select('sub.id AS id_subcategoria, cat.id AS id_categoria, 
                         SUM(mov.importe) AS importe, 
                         sub.nombre AS nombre_subcategoria, cat.nombre AS nombre_categoria');
        $CI->db->from('movimientos AS mov');
        $CI->db->join('subcategorias AS sub', 'sub.id = mov.subcategoria_id', 'left');
        $CI->db->join('categorias AS cat', 'cat.id = sub.categoria_id', 'left');
        $CI->db->join('movimientos_cuentas AS mov_cue', 'mov_cue.id = mov.movimiento_cuenta_id', 'left');
        $CI->db->join('cuentas AS cue', 'cue.id = mov_cue.cuenta_id', 'left');
        
        $CI->db->where('mov.fecha BETWEEN "'.$this->date_ini.'" AND "'.$this->date_end.'"');
        $CI->db->where('mov.tipo', $this->type);
        $CI->db->where('mov.cancelado', 0);
        $CI->db->where('mov.extraordinario', 0);

        if ($this->account) {
            $CI->db->where('mov_cue.cuenta_id', $this->account);
        }

        if ($this->category) {
            $CI->db->where('cat.id', $this->category);
        }

        if ($this->subcategory) {
            $CI->db->where('sub.id', $this->subcategory);
        }

        $CI->db->group_by("sub.id"); 

        $CI->db->order_by('cat.nombre', 'ASC');
        $CI->db->order_by('sub.nombre', 'ASC');

        $data = $CI->db->get();
        return $data->result_array();
    }

    private function getSubTotalCategory($category, $array) {
        $total = 0;

        foreach ($array as $key => $item) {
            if ($item['id_categoria'] == $category) {
                $total += $item['importe'];
            }
        }

        return $this->formatCurrency($total);
    }
}

/* End of file AccountPdf.php */