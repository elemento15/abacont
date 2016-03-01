<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class BaseController extends CI_Controller {

  protected $data = array();
  private $error = '';

  public function __construct() {
    parent::__construct();
    
    $session = $this->session->userdata;
    if (!(isset($session['user']) && $session['user'])) {
      throw new Exception("Session inactive");
    }
    
    $this->load->model($this->modelName,'model',true);
  }

  public function read() {
    $params = array(
      'order'  => isset($_POST['order'])  ? $_POST['order'] : null,
      'start'  => isset($_POST['start'])  ? $_POST['start']  : 0,
      'length' => isset($_POST['length']) ? $_POST['length'] : 0,
      'search' => isset($_POST['search']) ? $_POST['search'] : null,
      'filter' => isset($_POST['filter']) ? $_POST['filter'] : array()
    );

    $recs = $this->model->findAll($params);
    
    echo json_encode($recs);
  }

  public function model() {
    $method = $_SERVER['REQUEST_METHOD'];

    switch($method)
    {
      case 'GET' : $response = $this->find( intval($_GET['id']) );
                   break;
      case 'POST': $response = $this->save( json_decode($_POST['model'], true) );
                   break;
    }
    echo json_encode($response);
  }

  public function remove() {
    $id = $_POST['id'];
    if ($this->model->remove($id)) {
      $response = array('success' => true);
    } else {
      $response = array('success' => false, 'msg' => $this->model->getError());
    }
    echo json_encode($response);
  }

  // ---------- protected methods ----------
  protected function setError($msg) {
    $this->error = $msg;
  }

  protected function getError() {
    return $this->error;
  }

  // ---------- private methods ----------
  private function find($id) {
    $data = $this->model->find($id);
    if ($data) {
      $response = $data;
    } else {
      $response = array('success' => false, 'msg' => $this->model->getError());
    }
    return $response;
  }

  private function save($data) {
    $this->data = $data;

    if (method_exists($this, 'beforeSave')) {
      if ( ! $this->beforeSave()) {
        return array('success' => false, 'msg' => $this->getError());
      }
    }

    $id = $this->model->save($this->data);
    if ($id) {
      $response = array('success' => true, 'id' => $id);
    } else {
      $response = array('success' => false, 'msg' => $this->model->getError());
    }
    return $response;
  }

}

?>