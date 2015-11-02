<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Settings extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('settings_model');
    }

    public function index()
    {
        if (!$this->isLoggedIn) {
            redirect('/login');
        }

        $data = [];

        $data['settings'] = $this->settings;
        $data['acadYears'] = $this->accounts_model->getAcadYears();
        $data['currentAcadYearView'] = $this->session->acadYearView;

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'settings';
        $data['this'] = $this;
        $this->twig->display('settings/index', $data);
    }

    public function general()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();

        $result = $this->settings_model->update($input);
        if ($result) {
            $this->session->set_flashdata('success', 'Settings successfully updated!');
            redirect('/settings');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
            redirect('/settings');
        }
    }

    public function archive()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();

        $this->session->set_userdata('acadYearView', $input['acad_year']);
        redirect('/account/view');
    }
}
