<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Calculator extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->library('session');
        $this->load->model('Calculator_model');
    }
    
    public function index() {
        $data['units'] = $this->Calculator_model->get_units();
        $data['expression'] = $this->session->userdata('expression') ?? '';
        $data['angle'] = $this->session->userdata('angle') ?? 'rad';
        $data['output'] = $this->session->userdata('output') ?? '';
        
        // Handle clear request
        if ($this->input->get('clear')) {
            $this->session->unset_userdata(['expression', 'angle', 'output']);
            redirect('calculator');
        }
        
        // Handle POST request
        if ($this->input->server('REQUEST_METHOD') === 'POST') {
            $expression = $this->input->post('expression') ?? '';
            $angle = $this->input->post('angle') ?? 'rad';
            
            if ($expression !== '') {
                $output = $this->Calculator_model->evaluate_expression($expression, $angle);
                
                $this->session->set_userdata([
                    'expression' => $expression,
                    'angle' => $angle,
                    'output' => $output
                ]);
                
                redirect('calculator');
            }
        }
        
        $this->load->view('calculator/index', $data);
    }
    
    public function convert() {
        $category = $this->input->post('category');
        $from = $this->input->post('from');
        $to = $this->input->post('to');
        $value = $this->input->post('value');
        
        $result = $this->Calculator_model->convert($category, $from, $to, $value);
        
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['result' => $result]));
    }
}
