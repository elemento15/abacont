<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MovsAccountsCsv  {

    private $account, $date_ini, $option;

    public function __construct() {
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
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        fputcsv($output, array('FECHA', 'CONCEPTO', 'ABONO', 'CARGO', 'SALDO'));

        $balance = $this->getInitialBalance();
        $data = $this->getData();
        
        $text = array('', 'SALDO ANTERIOR', '-', '-', $balance);
        fputcsv($output, $text);

        foreach ($data as $key => $item) {
            $payment = ($item['tipo'] == 'A') ? $item['importe'] : 0;
            $charge = ($item['tipo'] == 'C') ? $item['importe'] : 0;
            $balance = $balance + $payment - $charge;

            if ($item['automatico']) {
                $concept = ($item['tipo'] == 'A') ? 'Ingreso: ' : 'Gasto: ';
                $concept.= ucfirst(strtolower($item['nombre_categoria']));
                $concept.= ' - ';
                $concept.= ucfirst(strtolower($item['nombre_subcategoria']));
            } else {
                $concept = $item['concepto'];
            }

            $text = array($item['fecha'], $concept, $payment, $charge, $balance);
            fputcsv($output, $text);
        }
    }


    protected function validDate($date) {
        if ( !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date )) { 
            return false;
        } else {
            return true;
        }
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

/* End of file */