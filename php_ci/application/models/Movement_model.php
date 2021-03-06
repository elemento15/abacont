<?php

include_once('BaseModel.php');

class Movement_model extends BaseModel {
	
	protected $table_name    = 'movimientos';
	protected $list_fields   = array('id','fecha','tipo','importe','cancelado','es_meses_sin_intereses',
		                             'movimiento_cuenta_id','subcategoria_id',
		                             'subcategorias.nombre AS subcategoria_nombre', 'categorias.nombre AS categoria_nombre',
		                             'cuentas.nombre AS cuenta_nombre');
	protected $search_fields = array('fecha','subcategorias.nombre','categorias.nombre','cuentas.nombre');
	protected $save_fields   = array('fecha','tipo','importe','movimiento_cuenta_id','subcategoria_id',
		                             'observaciones','es_meses_sin_intereses');
	protected $edit_fields   = array('observaciones');
	protected $avoid_delete  = true;

	protected $join_tables = array(
  	          array('table' => 'movimientos_cuentas', 'type' => 'left', 'fk' => 'movimiento_cuenta_id'),
  	          array('table' => 'subcategorias', 'type' => 'left', 'fk' => 'subcategoria_id'),
  	          array('table' => 'categorias', 'type' => 'left', 'fk' => false, 'condition' => 'categorias.id = subcategorias.categoria_id'),
  	          array('table' => 'cuentas', 'type' => 'left', 'fk' => false, 'condition' => 'cuentas.id = movimientos_cuentas.cuenta_id')
            );

	public function find($id) {
		$this->db->select('mov.id, mov.fecha, mov.tipo, mov.importe, movimiento_cuenta_id, subcategoria_id, 
			               subcategorias.categoria_id, mov.observaciones, movimientos_cuentas.cuenta_id, 
			               es_meses_sin_intereses, movimientos_cuentas.cancelado, 
			               subcategorias.nombre AS nombre_subcategoria,
			               categorias.nombre AS nombre_categoria');
		$this->db->join('movimientos_cuentas', 'movimientos_cuentas.id = movimiento_cuenta_id', 'left');
		$this->db->join('subcategorias', 'subcategorias.id = subcategoria_id', 'left');
		$this->db->join('categorias', 'categorias.id = categoria_id', 'left');
		$query = $this->db->get_where('movimientos AS mov', array('mov.id' => $id));

	    $row = $query->row();
	    if (!$row) {
	      $this->setError('Record not found: '.$id);
	      return false;
	    }
	    return $row;
	}

	public function find_by_mov_acc ($id_mov_acc) {
		$query = $this->db->get_where('movimientos AS mov', array('mov.movimiento_cuenta_id' => $id_mov_acc));
		$row = $query->row();
	    
	    if (!$row) {
	      $this->setError('Record not found: '.$id);
	      return false;
	    }

	    return $this->find($row->id);
	}

	public function movs_grouped ($grouping = 'D', $type = 'G', $month, $year, $category, $subcategory) {
		$this->db->select('mov.fecha, SUM(mov.importe) AS total');
		$this->db->from('movimientos AS mov');
		$this->db->join('subcategorias AS sub', 'sub.id = mov.subcategoria_id', 'left');
		$this->db->join('categorias AS cat', 'cat.id = sub.categoria_id', 'left');
		$this->db->group_by('mov.fecha');
		$this->db->order_by('mov.fecha');

		if ($year) {
			$this->db->where(array('YEAR(mov.fecha)' => $year));
			if ($month) {
				$this->db->where(array('MONTH(mov.fecha)' => $month));
			}
			
			if ($category) {
				$this->db->where(array('cat.id' => $category));
			}
			if ($subcategory) {
				$this->db->where(array('sub.id' => $subcategory));
			}
		}

		$this->db->where(array('mov.tipo' => $type, 'mov.cancelado' => 0));

		$query = $this->db->get();

		return $query->result();
	}

}

?>