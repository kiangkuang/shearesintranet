<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/login');
        }
    }

    public function index()
    {
        $this->load->view('errors/html/error_404');
    }
}
