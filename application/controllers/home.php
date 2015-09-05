<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    public function index()
    {
        if (!$this->isLoggedIn){
            redirect('/login');
        }

        $data = [];
        $this->load->view('home/index', $data);
    }
}
