<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'BasePdf.php';

class MovementsPdf extends BasePdf {

    private $rpt, $type, $account, $date_ini, $date_end, $comments;

    public function __construct() {
    	parent::__construct();
    	$this->title = 'Reporte de ';
    	$this->subtitle = '';
    }

    public function setParams($params, $orientation = 'P') {
        $this->orientation = $orientation;
        $this->rpt = $params['rpt'];
        $this->type = $params['type'];
        $this->type_date = $params['type_date'];
        $this->account = intval($params['account']);
        $this->category = intval($params['category']);
        $this->subcategory = intval($params['subcategory']);
        $this->date_ini = $params['date_ini'];
        $this->date_end = $params['date_end'];
        $this->comments = intval($params['comments']);

        if ($this->rpt != 'D' && $this->rpt != 'C' && $this->rpt != 'X') {
            echo "Report Unknown"; exit;
        }

        if ($this->type != 'G' && $this->type != 'I') {
            echo "Report Type Unknown"; exit;
        }

        $this->title .= ($this->type == 'G') ? 'Gastos' : 'Ingresos';
        switch ($this->rpt) {
            case 'D':
                $this->subtitle = 'Detallado';
                break;

            case 'C':
                $this->subtitle = 'Concentrado';
                break;
            
            case 'X':
                $this->subtitle = 'Comparativo';
                break;
        }

        // omit dates validation only when 'Comparativo' and 'Anual' 
        if (!($this->rpt == 'X' && $this->type_date == 'A')) {
            if (! $this->validDate($this->date_ini)) {
                echo "Invalid Initial Date"; exit;
            }

            if (! $this->validDate($this->date_end)) {
                echo "Invalid Final Date"; exit;
            }
        }
    }

    public function printing() {
        $this->AddPage($this->orientation);

        if ($this->rpt == 'D') {
            $this->printDetailed();
        } elseif ($this->rpt == 'C') {
            $this->printConcentrated();
        } else {
            $this->printCompared();
        }

    	$this->Output('rpt.pdf', 'I');
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

        $category = 0;

        foreach ($data['rows'] as $key => $item) {
            if ($category != $item['id_categoria']) {
                if ($category) $this->Ln(3);

                $border = 'B';
                $this->SetFont('Helvetica', 'B', 10);
                $this->Cell(75, 5, $item['nombre_categoria'], $border, 0, 'L', false);

                $subtotal = $this->getSubTotalCategory($item['id_categoria'], $data['rows']);
                $this->Cell(25, 5, $this->formatCurrency($subtotal), $border, 0, 'R', false);

                $this->SetFont('Helvetica', 'B', 9);
                $percent = ($subtotal / $data['total']) * 100;
                $this->Cell(18, 5, number_format($percent,2).' %', $border, 1, 'R', false);


                $category = $item['id_categoria'];
                $fill = false;
            }

            $border = false;
            $this->SetFont('Helvetica', '', 9);

            $this->Cell(5,  5, '', $border, 0, '', false);
            $this->Cell(70, 5, $item['nombre_subcategoria'], $border, 0, 'L', $fill, '', 1);
            $this->Cell(25, 5, $this->formatCurrency($item['importe']), $border, 1, 'R', $fill);
            // $this->Cell(0,  5, '', $border, 1, '', $fill);

            $fill = !$fill;
        }

        $this->Ln(3);
        $this->SetFont('Helvetica', 'B', 11);

        $this->Cell(115, 7, '', false, 0, '', false);
        $this->Cell(30, 7, 'Total: ', false, 0, 'R', false);
        
        $this->Cell(30, 7, $this->formatCurrency($data['total']), false, 0, 'R', false);
        $this->Cell(0,  7, '', false, 1, '', false);
    }

    private function printCompared() {
        $data = $this->getDataCompared();

        // ===== start column headers =====
        $border = true;  // 'B';
        $this->SetFont('Helvetica', 'B', 6);
        $this->SetLineStyle(array('width' => 0.1, 'color' => array(150, 150, 150)));
        $this->Cell(45, 5, 'CATEGORIAS - SUBCATEGORIAS', $border, 0, 'C', true);
        foreach ($data['amounts'] as $key => $item) {
            $this->Cell(18, 5, $key, $border, 0, 'C', true);
        }
        $this->Cell(0,  5, '', false, 1);
        $this->Ln(1);
        // ===== end column headers =====


        // set line color darker again
        $this->SetLineStyle(array('width' => 0.1, 'color' => array(100, 100, 100)));

        foreach ($data['categories'] as $cKey => $cItem) {
            $border = 'B';  // 'B';
            $this->SetFont('Helvetica', 'BI', 6);
            $this->Cell(45, 5, $cItem['name'], $border, 0, 'L');
            foreach ($data['amounts'] as $key => $item) {
                $this->SetFont('Helvetica', 'BI', 8);
                $this->Cell(18, 5, $this->sumCategory($data['amounts'][$key], $cItem), $border, 0, 'R');
            }
            $this->Cell(0,  5, '', $border, 1);
            
            $fill = false;
            foreach ($cItem['subcategories'] as $sKey => $sItem) {
                $border = false;  // 'B';
                $this->SetFont('Helvetica', '', 7);
                $this->Cell(3,  4, '', $border, 0, 'L', $fill);
                $this->Cell(42, 4, $sItem, $border, 0, 'L', $fill);

                $this->SetFont('Helvetica', '', 8);
                foreach ($data['amounts'] as $key => $amounts) {
                    $this->Cell(18, 4, $this->getArrData($amounts, $sKey), $border, 0, 'R', $fill);
                }
                
                $fill = !$fill;
                $this->Cell(0,  4, '', $border, 1);
            }

            $this->Ln(2);
        }

        // ===== start summary =====
        $this->Ln(2);
        
        $border = true;  // 'B';
        $this->SetFont('Helvetica', 'B', 6);
        $this->SetLineStyle(array('width' => 0.1, 'color' => array(150, 150, 150)));
        $this->Cell(45, 5, 'TOTALES', $border, 0, 'C', true);
        foreach ($data['amounts'] as $key => $item) {
            $this->Cell(18, 5, $key, $border, 0, 'C', true);
        }
        $this->Cell(0,  5, '', false, 1);

        $this->SetFont('Helvetica', 'B', 8);
        $this->Cell(45, 5, '', $border, 0, 'C', false);
        foreach ($data['amounts'] as $key => $item) {
            $this->Cell(18, 5, $this->sumPeriod($item), $border, 0, 'R', false);
        }
        $this->Cell(0,  5, '', false, 1);
        // ===== end summary =====
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
        $rows = $data->result_array();

        // get totals
        $total = 0;
        $groups = [];
        foreach ($rows as $item) {
            $total += $item['importe'];
            if (array_key_exists($item['nombre_categoria'], $groups)) {
                $groups[$item['nombre_categoria']] += $item['importe'];
            } else {
                $groups[$item['nombre_categoria']] = $item['importe'];
            }
        }

        return [
            'rows'   => $rows,
            'total'  => $total,
            'groups' => $groups
        ];
    }

    private function getDataCompared() {
        $CI =& get_instance();

        $period_offset = ($this->type_date == 'M') ? '7' : '4';

        $CI->db->select('SUBSTR(mv.fecha, 1, '. $period_offset .') AS periodo, s.id AS sID, SUM(mv.importe) AS total,
                         s.nombre AS subcategoria, c.id AS cID, c.nombre AS categoria ');
        $CI->db->from('movimientos AS mv');
        $CI->db->join('subcategorias AS s', 's.id = mv.subcategoria_id', 'left');
        $CI->db->join('categorias AS c', 'c.id = s.categoria_id', 'left');
        $CI->db->join('movimientos_cuentas AS mov_cue', 'mov_cue.id = mv.movimiento_cuenta_id', 'left');
        
        if ($this->type_date == 'M') {
            $CI->db->where('mv.fecha BETWEEN "'.$this->date_ini.'" AND "'.$this->date_end.'"');
        }

        $CI->db->where('mv.tipo', $this->type);
        $CI->db->where('mv.cancelado', 0);

        if ($this->account) {
            $CI->db->where('mov_cue.cuenta_id', $this->account);
        }

        if ($this->category) {
            $CI->db->where('c.id', $this->category);
        }

        if ($this->subcategory) {
            $CI->db->where('s.id', $this->subcategory);
        }

        $CI->db->group_by("SUBSTR(mv.fecha, 1, $period_offset), mv.subcategoria_id"); 

        $CI->db->order_by('c.nombre', 'ASC');
        $CI->db->order_by('s.nombre', 'ASC');

        $data = $CI->db->get();
        $rows = $data->result_array();

        $categories = array();
        $amounts = array();
        
        foreach ($rows as $item) {
            // add to categories
            $ckey = 'c' . $item['cID'];
            if (! array_key_exists($ckey, $categories)) {
                $categories[$ckey] = [
                    'name' => $item['categoria'],
                    'subcategories' => []
                ];
            }
            // add to category's subcategories
            $skey = 's' . $item['sID'];
            if (! array_key_exists($skey, $categories[$ckey]['subcategories'])) {
                $categories[$ckey]['subcategories'][$skey] = $item['subcategoria'];
            }

            // add to amounts
            $dkey = $item['periodo'];
            if (! array_key_exists($dkey, $amounts)) {
                $amounts[$dkey] = [];
            }
            // add to amount's subcategories
            $skey = 's' . $item['sID'];
            if (! array_key_exists($skey, $amounts[$dkey])) {
                $amounts[$dkey][$skey] = $item['total'];
            }
        }

        ksort($amounts);

        return [
            'categories' => $categories,
            'amounts'    => $amounts,
        ];
    }

    private function getSubTotalCategory($category, $array) {
        $total = 0;

        foreach ($array as $key => $item) {
            if ($item['id_categoria'] == $category) {
                $total += $item['importe'];
            }
        }

        return $total;
    }

    private function getArrData($array, $key) {
        if (isset($array[$key])) {
            $value = $array[$key];
        } else {
            $value = 0;
        }

        return $this->formatNumber($value, false);
    }

    private function sumCategory($amounts, $category) {
        $sum = 0;
        foreach ($amounts as $key => $amount) {
            if (array_key_exists($key, $category['subcategories'])) {
                $sum += $amount;
            }
        }
        return $this->formatNumber($sum, true);
    }

    private function sumPeriod($amounts) {
        $sum = 0;
        foreach ($amounts as $value) {
            $sum += $value;
        }
        return $this->formatNumber($sum, true);
    }
}

/* End of file AccountPdf.php */