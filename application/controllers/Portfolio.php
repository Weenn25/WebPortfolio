<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Portfolio extends CI_Controller {

    /**
     * Shared data for views
     * @var array
     */
    protected $data = array();

    public function __construct()
    {
        parent::__construct();
        // helpers and libraries used across views
        $this->load->helper(['url','form']);
        $this->load->library(['session','form_validation']);
        $this->data = [
            'site_title' => 'My Portfolio',
        ];
    }

    public function index()
    {
        $this->data['page'] = 'home';
        $this->load->view('templates/header', $this->data);
        $this->load->view('home', $this->data);
        $this->load->view('templates/footer');
    }

    public function about()
    {
        $this->data['page'] = 'about';
        $this->load->view('templates/header', $this->data);
        $this->load->view('about', $this->data);
        $this->load->view('templates/footer');
    }

    public function skills()
    {
        $this->data['page'] = 'skills';
        $this->data['skills'] = [
            'HTML' => 90,
            'CSS'  => 85,
            'PHP'  => 80,
            'CodeIgniter' => 75,
            'JavaScript' => 60
        ];
        $this->load->view('templates/header', $this->data);
        $this->load->view('skills', $this->data);
        $this->load->view('templates/footer');
    }

    public function projects()
    {
        $this->data['page'] = 'projects';
        $this->data['projects'] = [
            [
                'title' => 'Simple Website',
                'description' => 'Responsive site built with HTML/CSS and basic PHP.',
                'link' => '#'
            ],
            [
                'title' => 'Login System',
                'description' => 'User authentication demo with CodeIgniter sessions.',
                'link' => '#'
            ],
            [
                'title' => 'Portfolio Theme',
                'description' => 'Creative layout showcasing UI and project cards.',
                'link' => '#'
            ]
        ];
        $this->load->view('templates/header', $this->data);
        $this->load->view('projects', $this->data);
        $this->load->view('templates/footer');
    }

    public function contact()
    {
        $this->data['page'] = 'contact';

        if ($this->input->post('submit'))
        {
            $this->form_validation->set_rules('name','Name','trim|required');
            $this->form_validation->set_rules('email','Email','trim|required|valid_email');
            $this->form_validation->set_rules('message','Message','trim|required');

            if ($this->form_validation->run() === TRUE)
            {
                $this->session->set_flashdata('success', 'Thank you — your message was received.');
                redirect('portfolio/contact');
            }
        }

        $this->load->view('templates/header', $this->data);
        $this->load->view('contact', $this->data);
        $this->load->view('templates/footer');
    }

    /**
     * Copy images from application/assets/images to public assets/images
     * Run once by visiting /portfolio/install_assets
     */
    public function install_assets()
    {
        $src = APPPATH . 'assets/images/';
        $dst = FCPATH . 'assets/images/';

        if ( ! is_dir($src)) {
            echo 'Source folder not found: ' . $src; return;
        }

        if ( ! is_dir($dst)) {
            if ( ! mkdir($dst, 0755, true)) {
                echo 'Failed to create destination folder: ' . $dst; return;
            }
        }

        $files = glob($src . '*');
        $copied = 0;
        foreach ($files as $file) {
            $base = basename($file);
            if (is_file($file)) {
                if (copy($file, $dst . $base)) {
                    $copied++;
                }
            }
        }

        echo "Copied {$copied} files from application/assets/images to assets/images.\n";
        echo '<a href="' . site_url('portfolio') . '">Back to portfolio</a>';
    }
}
