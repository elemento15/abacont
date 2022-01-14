<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

class MovementsCsv {

    private $rpt, $type, $account, $date_ini, $date_end, $comments;

    public function __construct() {
    	//
    }

    public function setParams($params) {
        $this->rpt = $params['rpt'];
        $this->type = $params['type'];
        $this->type_date = $params['type_date'];
        $this->account = intval($params['account']);
        $this->category = intval($params['category']);
        $this->subcategory = intval($params['subcategory']);
        $this->date_ini = $params['date_ini'];
        $this->date_end = $params['date_end'];

        if ($this->rpt != 'D' && $this->rpt != 'C' && $this->rpt != 'X') {
            echo "Report Unknown"; exit;
        }

        if ($this->type != 'G' && $this->type != 'I') {
            echo "Report Type Unknown"; exit;
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

        if ($this->rpt == 'D') {
            //$this->printDetailed();
            echo "CSV Unavailable"; exit;
        } elseif ($this->rpt == 'C') {
            //$this->printConcentrated();
            echo "CSV Unavailable"; exit;
        } else {
            $this->printCompared();
        }
    }

    private function printCompared() {
        $data = $this->getDataCompared();
        //var_dump( json_encode($data) ); exit;

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=data.csv');
        
        // create a file pointer connected to the output stream
        $output = fopen('php://output', 'w');

        // output the column headings
        $cols = array_keys($data['amounts']);
        fputcsv($output, array_merge(['',''], $cols));

        foreach ($data['categories'] as $category) {
            fputcsv($output, [$category['name']]); // category name
            
            foreach ($category['subcategories'] as $key => $subcategory) {
                $amounts = [];
                foreach ($data['amounts'] as $year => $item) {
                    $amounts[] = $item[$key] ?? '-';
                }
                
                // subcategory and amounts
                fputcsv($output, array_merge(['', $subcategory], $amounts));
            }
            
            fputcsv($output, ['']); // empty line
        }
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

    private function validDate($date) {
        if ( !preg_match("/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/", $date )) { 
            return false;
        } else {
            return true;
        }
    }
}

/* End of file */