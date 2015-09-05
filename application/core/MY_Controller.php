<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {

    var $isLoggedIn = false;
    var $userId = -1;
    var $success = false;
    var $error = false;

	public function __construct()
    {
        parent::__construct();
		
		$this->load->model('accounts_model');

        if ($this->session->account) {
            $this->isLoggedIn = true;
            $this->account = $this->session->account;
        }
        if ($this->session->success){
            $this->success = $this->session->success;
        }
        if ($this->session->error){
            $this->error = $this->session->error;
        }
    }
}