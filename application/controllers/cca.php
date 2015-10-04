<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cca extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/login');
        }
        $this->load->model('ccas_model');
        $this->load->model('ccatypes_model');
        $this->load->model('ccaclassifications_model');
        $this->load->model('memberships_model');
        $this->load->library('account_library');
        $this->load->library('cca_library');
    }

    public function index()
    {
        if ($this->account->is_first_login){
            redirect('/changepassword');
        }

        $data = [];

        $memberships = $this->memberships_model->getByAccountId($this->account->id);
        $memberships = $this->cca_library->appendCCA($memberships);
        $data['memberships'] = $memberships;

        $data['totalPoints'] = $this->membership_library->getTotalPointsByAccountId($this->account->id);

        $data['mainMenu'] = 'cca';
        $this->load->view('cca/index',$data);
    }

    public function view($search = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];

        $ccas = $this->ccas_model->getAllJoinTypeNameJoinClassificationName();
        $data['ccas'] = $ccas;

        if ($this->input->get('search')) {
            $search = $this->input->get('search');
        }
        $data['search'] = urldecode($search);

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCca';
        $this->load->view('cca/view',$data);
    }

    public function edit($id = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            $data['cca'] = $this->ccas_model->getById($id);

            if ($data['cca'] === false) {
                $this->session->set_flashdata('error', 'CCA not found!');
                redirect('/cca/view');
            }

            $memberships = $this->memberships_model->getByCcaId($id);
            $memberships = $this->account_library->appendAccount($memberships);
            $data['memberships'] = $memberships;

            $data['accounts'] = $this->cca_library->getUnjoinedAccounts($memberships);
        }

        $data['types'] = $this->ccatypes_model->getAll();
        $data['classifications'] = $this->ccaclassifications_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $this->load->view('cca/edit',$data);
    }

    public function update()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();
        $input['shortname'] = strtolower(str_replace(' ', '-', $input['name']));

        if (isset($input['id'])) {
            // update
            $result = $this->ccas_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully updated!');
                redirect('/cca/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/cca/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccas_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully created!');
                redirect('/cca/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/cca/edit');
            }
        }
    }

    public function delete($id = false)
    {
        if (!$id) {
            redirect('/cca/view');
        }

        $result = $this->ccas_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'CCA successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/cca/view');
    }
}
