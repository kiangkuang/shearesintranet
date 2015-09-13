<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('memberships_model');
        $this->load->library('account_library');
        $this->load->library('cca_library');
        $this->load->library('membership_library');
    }

    public function login()
    {
        if ($this->isLoggedIn) {
            redirect('/');
        }

        $data = [];

        $this->load->view('account/login',$data);
    }

    public function processLogin()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $data = $this->input->post();
        $account = $this->accounts_model->authenticate($data['user'], $data['password']);

        if ($account) {
            unset($account->password);
            unset($account->key);
            $this->session->set_userdata('account', $account);
            redirect('/');
        } else {
            $this->session->set_flashdata('error', 'Incorrect login or password.');
            redirect('/login');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    public function view()
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];

        $accounts = $this->accounts_model->getByAcadYear();
        $accounts = $this->account_library->appendTotalPoints($accounts);
        $accounts = $this->account_library->appendMembershipSummary($accounts);
        $data['accounts'] = $accounts;

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'account';
        $data['subSubMenu'] = 'viewAccount';
        $this->load->view('account/view', $data);
    }

    public function edit($id = false)
    {
        if (!$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            $data['account'] = $this->accounts_model->getById($id);
            if ($data['account'] === false) {
                $this->session->set_flashdata('error', 'Account not found!');
                redirect('/account/view');
            }

            $memberships = $this->memberships_model->getByAccountId($id);
            $memberships = $this->cca_library->appendCca($memberships);
            $data['memberships'] = $memberships;

            $data['ccas'] = $this->account_library->getUnjoinedCcas($memberships);
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'account';
        $this->load->view('account/edit', $data);
    }

    public function update()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $input = $this->input->post();

        if (isset($input['id'])) {
            // update
            if ($input['password'] === '') {
                // not changing password
                unset($input['password']);
                unset($input['password2']);
            } elseif ($input['password'] !== $input['password2']) {
                // password mismatch
                $this->session->set_flashdata('error', 'The passwords do not match!');
                redirect('/account/edit/');
            } else {
                // changing password
                unset($input['password2']);
                $input['key'] = time();
                $input['password'] = sha1($input['password'].$input['key']);
            }

            $result = $this->accounts_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Account successfully updated!');
                redirect('/account/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/account/edit/'.$input['id']);
            }
        } else {
            // add
            if ($input['password'] !== $input['password2']) {
                $this->session->set_flashdata('error', 'The passwords do not match!');
                redirect('/account/edit/');
            }

            unset($input['password2']);
            $input['key'] = time();
            $input['password'] = sha1($input['password'].$input['key']);
            $input['acad_year'] = ACAD_YEAR;

            $result = $this->accounts_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Account successfully created!');
                redirect('/account/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occured!');
                redirect('/account/edit');
            }
        }
    }

    public function delete($id = false)
    {
        if (!$id) {
            redirect('/account/view');
        }

        $result = $this->accounts_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'Account successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/account/view');
    }
}
