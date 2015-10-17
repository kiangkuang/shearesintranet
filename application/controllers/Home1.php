<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    public function index()
    {
        if (!$this->isLoggedIn){
            redirect('/login');
        }
        if ($this->account->is_first_login){
            redirect('/changepassword');
        }

        $data = [];
        $this->load->view('home/index', $data);
    }
}
