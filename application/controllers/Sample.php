<?php


class Sample extends CI_Controller {


	
	public function view(){

	$page = "sample";

	if(!file_exists(APPPATH.'views/'.$page.'.php')){
		show_404();
	
	}

	$data['title'] = "HAHAHAHAHA";

	$this->load->view('templates/header');
	$this->load->view('views/'.$page, $data);
	$this->load->view('templates/footer');
 
	}
}