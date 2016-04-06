<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'BasePdf.php';

class AccountPdf extends BasePdf {

    public function __construct() {
    	parent::__construct();
    	$this->title = 'LISTADO DE CUENTAS';
    	$this->subtitle = '';
    }

    public function printing() {
    	$this->AddPage();
    	$this->main();
    	$this->Output();
    }

    private function main() {
    	$border = false;
    	$fill = false;
        $data = $this->getData();
        $total = 0;

    	$this->SetFont('Helvetica', '', 10);

        foreach ($data as $key => $item) {
            switch ($item['tipo']) {
                case 'E' : $type = 'EFECTIVO'; break;
                case 'C' : $type = 'CREDITO';  break;
                case 'D' : $type = 'DEBITO';   break;
                default  : $type = '';
            }

            $balance = $this->formatCurrency($item['saldo']);

            $this->setColorDefault();
            $this->Cell(15, 6, '', $border, 0, '', $fill);
            $this->Cell(90, 6, $item['nombre'], $border, 0, '', $fill);
            $this->Cell(30, 6, $type, $border, 0, '', $fill);
            
            $this->setColorNegative($item['saldo']);
            $this->Cell(30, 6, $balance, $border, 0, 'R', $fill);

            $this->Cell(0,  6, '', $border, 1, '', $fill);
            
            $total += $item['saldo'];
            $fill = !$fill;
        }

        $this->Ln(3);
        $this->SetFont('Helvetica', 'B', 12);
        $this->setColorDefault();

        $this->Cell(105, 7, '', false, 0, '', false);
        $this->Cell(30, 7, 'Saldo Total: ', false, 0, 'R', false);
        
        $this->setColorNegative($total);
        $this->Cell(30, 7, $this->formatCurrency($total), false, 0, 'R', false);
        $this->Cell(0,  7, '', false, 1, '', false);
    }

    private function getData() {
        $CI =& get_instance();

        $CI->db->from('cuentas');
        $CI->db->where('activo', 1);
        $CI->db->order_by('tipo', 'DESC');
        $data = $CI->db->get();
        return $data->result_array();
    }
}

/* End of file AccountPdf.php */