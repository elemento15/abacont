<?php

class BaseModel extends CI_Model {

  private $error;

  public function __construct() {
    parent::__construct();
  }

  public function findAll($params) {
    $this->setQuery();
    $this->setOrder($params['order']);
    $this->setFilter($params['filter']);
    $this->setSearch($params['search']);

    $count = $this->db->get()->num_rows();

    $this->setQuery();
    $this->setOrder($params['order']);
    $this->setFilter($params['filter']);
    $this->setSearch($params['search']);
    $this->setLimit($params['length'], $params['start']);

    $query = $this->db->get();

    return array(
      'count' => $count,
      'total' => $this->db->count_all($this->table_name),
      'data'  => $query->result()
    );
  }

  public function find($id) {
    $query = $this->db->get_where($this->table_name, array('id' => $id));
    $row = $query->row();
    if (!$row) {
      $this->setError('Record not found: '.$id);
      return false;
    }
    return $row;
  }

  public function save($data, $force = false) {
    // set '$force' to true if you need to save fields passed in '$data'
    // or it will save only the fields in '$this->save_fields' or '$this->edit_fields'
    
    $is_new = ( !$data['id'] ) ? true : false;

    try {
      $save_fields = ($force) ? $data : $this->getFieldsToSave($data, $is_new);
      if ( ! $is_new ) {
        $this->db->where('id', $data['id']);
        $this->db->update($this->table_name, $save_fields);
        $id = $data['id'];
      } else {
        $this->db->insert($this->table_name, $save_fields);
        $id = $this->db->insert_id();
      }
      return $id;
    } catch (Exception $e) {
      $this->setError($e->getMessage());
    }
    return false;
  }

  public function remove($id) {
    if ($this->find($id)) {
      if ( isset($this->avoid_delete) && $this->avoid_delete ) {
        $this->setError('Deleting records on this table is not allowed');
        return false;
      }
      try {
        $this->db->delete($this->table_name, array('id'=>$id));
      } catch (Exception $e) {
        $this->setError($e->getMessage());
      }
    } else {
      $this->setError('Record not found.');
      return false;
    }
    return true;
  }

  public function setError($msg) {
    $this->error = $msg;
  }

  public function getError() {
    return $this->error;
  }


  // ---------- Overwritable Methods ----------
  public function setQuery() {
    $this->db->select($this->getSelectStatement());
    $this->db->from($this->table_name);
    $this->setJoiningTables();
  }

  public function setOrder($order) {
    if ( isset($order) && $order['field'] ) {
      $order_field = (strpos($order['field'], '.')) ? $order['field'] : $this->table_name.'.'.$order['field'];
      $this->db->order_by($order_field, $order['type']);
    }
  }

  public function setFilter($filter) {
    foreach ($filter as $key => $val) {
      $field = (strpos($val['field'], '.')) ? $val['field'] : $this->table_name.'.'.$val['field'];
      $this->db->where($field, $val['value']);
    }
  }

  public function setSearch($search) {
    $text = '';
    if ($search && isset($this->search_fields)) {
      foreach ($this->search_fields as $item) {
        $text.= ($text) ? ' OR ' : '';
        $text.= (strpos($item, '.')) ? $item : $this->table_name.'.'.$item;
        $text.= " LIKE '%$search%'";
      }
      $this->db->where($text);
    }
  }

  public function setLimit($length, $start) {
    if ( isset($start) && $length > 0) {
      $this->db->limit($length, $start);
    }
  }


  // ---------- Private Methods ----------
  private function getFieldsToSave($form, $is_new) {
    $save_data = array();

    // if "new" or "edit_fields" doesn't exists, get fields from "save_fields"
    if ($is_new || !isset($this->edit_fields)) {
      $arr_data = $this->save_fields;
    } else {
      $arr_data = $this->edit_fields;
    }

    foreach ($arr_data as $item) {
      if (isset($form[$item])) {
        $save_data[$item] = $form[$item];
      }
    }

    return $save_data;
  }

  private function getSelectStatement() {
    // build select statement
    if ( isset($this->list_fields) ) {
      $fields = array();
      foreach ($this->list_fields as $field) {
        // if there isn't a dot (.) inside 'field', concat table_name with field
        $fields[] = (strpos($field, '.')) ? $field : $this->table_name.'.'.$field;
      }

    } else {
      $fields = $this->table_name.'.*';
    }

    return $fields;
  }

  private function setJoiningTables() {
    if ( isset($this->join_tables) ) {
      foreach ($this->join_tables as $join) {

        $table1 = $this->table_name;
        $table2 = $join['table'];
        $type   = ( isset($join['type']) ) ? $join['type'] : 'left';
        $fk     = $join['fk'];

        $this->db->join($table2, "$table2.id = $table1.$fk", $type);
      }
    }
  }

}

?>