<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

require_once 'BasePdf.php';

class MovsAccountsPdf extends BasePdf {

    private $account, $date_ini;
    protected $margin_header = 4;

    public function __construct() {
    	parent::__construct();
    	$this->title = 'Reporte de Movimientos';
    	$this->subtitle = '';
    }

    protected function subHeader() {
        $border = false;

        $this->SetFont('Helvetica', 'B', 8);
        $this->Cell(20, 5, 'FECHA', $border, 0, 'C', false);
        $this->Cell(65, 5, 'CONCEPTO', $border, 0, 'C', false, '', 1);
        $this->Cell(20, 5, 'ABONO', $border, 0, 'C', false, '', 1);
        $this->Cell(20, 5, 'CARGO', $border, 0, 'C', false, '', 1);
        $this->Cell(20, 5, 'SALDO', $border, 0, 'C', false, '', 1);
        $this->Cell(0,  5, '', $border, 1, '', false);
    }

    public function setParams($params) {
        $this->account = intval($params['account']);
        $this->date_ini = $params['date_ini'];

        if (! $this->account) {
            echo "Invalid Account"; exit;
        }

        if (! $this->validDate($this->date_ini)) {
            echo "Invalid Initial Date"; exit;
        }
    }

    public function printing() {
        $this->AddPage();
        $balance = $this->getInitialBalance();
        $data = $this->getData();

        $border = false;
        $fill = false;
        $this->SetFont('Helvetica', '', 9);

        // previous balance
        $this->Cell(20, 5, '', $border, 0, 'C', $fill);
        $this->Cell(65, 5, 'SALDO ANTERIOR', $border, 0, 'L', $fill, '', 1);
        $this->Cell(20, 5, '-', $border, 0, 'R', $fill, '', 1);
        $this->Cell(20, 5, '-', $border, 0, 'R', $fill, '', 1);
        $this->Cell(20, 5, $this->formatCurrency($balance), $border, 0, 'R', $fill, '', 1);
        $this->Cell(0,  5, '', $border, 1, '', $fill);

        $fill = true;

        foreach ($data as $key => $item) {

            $payment = ($item['tipo'] == 'A') ? $item['importe'] : 0;
            $charge = ($item['tipo'] == 'C') ? $item['importe'] : 0;
            $balance = $balance + $payment - $charge; 

            $this->Cell(20, 5, $item['fecha'], $border, 0, 'C', $fill);

            if ($item['automatico']) {
                $concept = ($item['tipo'] == 'A') ? 'Ingreso: ' : 'Gasto: ';
                $concept.= ucfirst(strtolower($item['nombre_categoria']));
                $concept.= ' - ';
                $concept.= ucfirst(strtolower($item['nombre_subcategoria']));

                $this->Cell(65, 5, $concept, $border, 0, 'L', $fill, '', 1);
            } else {
                $this->Cell(65, 5, $item['concepto'], $border, 0, 'L', $fill, '', 1);
            }
            
            $this->Cell(20, 5, $this->formatCurrency($payment, false), $border, 0, 'R', $fill, '', 1);
            $this->Cell(20, 5, $this->formatCurrency($charge, false), $border, 0, 'R', $fill, '', 1);
            $this->Cell(20, 5, $this->formatCurrency($balance), $border, 0, 'R', $fill, '', 1);
            $this->Cell(0,  5, '', $border, 1, '', $fill);

            $fill = !$fill;
        }

        $this->Output('rpt.pdf', 'I');
    }

    private function getInitialBalance() {
        $CI =& get_instance();

        $CI->db->select('SUM(IF(tipo = "A", importe, importe * -1)) as total');
        $CI->db->from('movimientos_cuentas');
        $CI->db->where('fecha < "'.$this->date_ini.'"');
        $CI->db->where('cuenta_id', $this->account);
        $CI->db->where('cancelado', 0);

        $data = $CI->db->get();
        return $data->row()->total; exit;
    }

    private function getData() {
        $CI =& get_instance();

        $CI->db->select('mva.fecha, mva.tipo, mva.importe, mva.concepto, mva.automatico, 
                         sub.nombre AS nombre_subcategoria, cat.nombre AS nombre_categoria');
        $CI->db->from('movimientos_cuentas AS mva');
        $CI->db->join('movimientos AS mov', 'mov.movimiento_cuenta_id = mva.id', 'left');
        $CI->db->join('subcategorias AS sub', 'sub.id = mov.subcategoria_id', 'left');
        $CI->db->join('categorias AS cat', 'cat.id = sub.categoria_id', 'left');

        
        $CI->db->where('mva.fecha >= "'.$this->date_ini.'"');
        $CI->db->where('mva.cuenta_id', $this->account);
        $CI->db->where('mva.cancelado', 0);

        $CI->db->order_by('mva.fecha', 'ASC');
        $CI->db->order_by('mva.id', 'ASC');

        $data = $CI->db->get();
        return $data->result_array();
    }

}

/* End of file AccountPdf.php */