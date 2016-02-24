<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

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
        $this->twig->display('home/index', $data);
    }

}
