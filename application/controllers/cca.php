<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Cca extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
        $this->load->model('accounts_model');
        $this->load->model('ccas_model');
        $this->load->model('ccatypes_model');
        $this->load->model('ccaclassifications_model');
        $this->load->model('memberships_model');
    }

    public function view($search = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        $ccas = $this->ccas_model->getAll();

        foreach ($ccas as &$cca) {
            $cca->typeObject = $this->ccatypes_model->getById($cca->type);
            $cca->classificationObject = $this->ccaclassifications_model->getById($cca->classification);
        }
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
            
            $memberIds = [];
            $memberships = $this->memberships_model->getByCcaId($id);
            if ($memberships) {
                foreach ($memberships as &$membership) {
                    $membership->account = $this->accounts_model->getById($membership->account_id);
                    $memberIds[] = $membership->account_id;
                }
            }
            $data['memberships'] = $memberships;

            $accounts = $this->accounts_model->getAll();
            // remove existing members from array
            if ($accounts) {
                foreach ($accounts as $key => $account) {
                    if (in_array($account->id, $memberIds) || $account->is_admin === '1') {
                        unset($accounts[$key]);
                    }
                }
            }
            $data['accounts'] = $accounts;
        }

        $data['types'] = $this->ccatypes_model->getAll();
        $data['classifications'] = $this->ccaclassifications_model->getAll();

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        if ($id === false) {
            $data['subSubMenu'] = 'addCca';
        }
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
                redirect('/cca/view/'.$input['name']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/cca/edit/'.$input['id']);
            }
        } else {
            // add
            $result = $this->ccas_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA successfully created!');
                redirect('/cca/view/'.$input['name']);
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
