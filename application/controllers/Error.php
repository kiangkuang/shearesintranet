<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Error extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
    }

    public function index()
    {
        $data = [];
        $data['this'] = $this;
        $this->twig->display('errors/html/error_404', $data);
    }
}
