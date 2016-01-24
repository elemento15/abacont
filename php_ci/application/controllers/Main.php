<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function index() {
		// $this->load->helper('url');
		// $session = $this->session->userdata;
		
		// if NOT admin redirect to default
		// if (isset($session['is_admin']) && $session['is_admin']) {
		// 	$this->load->view('admin_panel');
		// } else {
		// 	redirect('/products/index/', 'location');
		// }

		$this->load->view('main');
	}
}
