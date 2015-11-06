<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

    public function index()
    {
        if (!$this->isLoggedIn){
            redirect('/');
        }

        $data = [];

        $data['this'] = $this;
        $this->twig->display('home/index', $data);
    }
}
