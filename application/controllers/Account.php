<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('memberships_model');
        $this->load->library('account_library');
    }

    public function login()
    {
        if ($this->isLoggedIn) {
            redirect('/');
        }

        $data = [];

        $data['this'] = $this;
        $this->twig->display('account/login',$data);
    }

    public function processLogin()
    {
        if (!$this->input->post()) {
            redirect('/');
        }

        $data = $this->input->post();
        $account = $this->accounts_model->authenticate($data['user'], $data['password']);

        if ($account) {
            $this->session->set_userdata('accountId', $account->id);
            $this->session->set_userdata('acadYearView', ACAD_YEAR);
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
        if ($accounts) {
            $accounts = $this->account_library->appendTotalPoints($accounts);
            $accounts = $this->account_library->appendMemberships($accounts);
        }
        $data['accounts'] = $accounts;

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'account';
        $data['subSubMenu'] = 'viewAccount';
        $data['this'] = $this;
        $this->twig->display('account/view', $data);
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

            $data['memberships'] = $this->memberships_model->getByAccountIdJoinCcaName($id);
            $data['ccas'] = $this->account_library->getUnjoinedCcas($data['memberships']);
        }

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'account';
        $data['this'] = $this;
        $this->twig->display('account/edit', $data);
    }

    public function update()
    {
        if (!$this->input->post() || !$this->editable) {
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
                $input['is_first_login'] = 1;
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
            $input['is_first_login'] = 1;
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

    public function changePassword()
    {
        if (!$this->isLoggedIn) {
            redirect('/login');
        }

        if ($this->input->post()) {
            $input = $this->input->post();

            if (sha1($input['currentPassword'].$this->account->key) !== $this->account->password) {
                $this->session->set_flashdata('error', 'Incorrect password!');
                redirect('/changepassword');
            } elseif ($input['password'] !== $input['password2']) {
                $this->session->set_flashdata('error', 'Passwords do not match!');
                redirect('/changepassword');
            } else {
                unset($input['currentPassword']);
                unset($input['password2']);
                $input['id'] = $this->account->id;
                $input['key'] = time();
                $input['password'] = sha1($input['password'].$input['key']);
                $input['is_first_login'] = 0;

                $result = $this->accounts_model->update($input);
                if ($result) {
                    $this->session->set_flashdata('success', 'Password successfully changed!');
                    redirect('/');
                } else {
                    $this->session->set_flashdata('error', 'An error has occured!');
                    redirect('/changepassword');
                }
            }
        }

        $data = [];

        $data['mainMenu'] = 'account';
        $data['subMenu'] = 'changePassword';
        $data['this'] = $this;
        $this->twig->display('account/changePassword', $data);
    }

    public function delete($id = false)
    {
        if (!$id || !$this->editable) {
            redirect('/account/view');
        }

        $result = $this->accounts_model->deleteById($id);
        $result2 = $this->memberships_model->deleteByAccountId($id);
        if ($result && $result2) {
            $this->session->set_flashdata('success', 'Account successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occured!');
        }
        redirect('/account/view');
    }

    public function processOpenId()
    {
        require 'vendor/lightopenid/lightopenid/openid.php';

        try {
            # Change 'localhost' to your domain name.
            $openid = new LightOpenID('sheares-intranet.app');
            if(!$openid->mode) {
                $openid->identity = 'https://openid.nus.edu.sg';

                # The following two lines request email, full name, and a nickname
                # from the provider. Remove them if you don't need that data.
                //$openid->required = array('contact/email');
                //$openid->optional = array('namePerson', 'namePerson/friendly', 'contact/email');

                $openid->required = array('namePerson/friendly');
                redirect($openid->authUrl());
            }
            elseif($openid->mode == 'cancel') {
                $this->session->set_flashdata('error', 'User has cancelled authentication.');
                redirect('/login');
            }
            else {
                if ($openid->validate()) {
                    $openIdAttributes = $openid->getAttributes();
                    $account = $this->accounts_model->getByUser($openIdAttributes['namePerson/friendly']);

                    $this->session->set_userdata('accountId', $account->id);
                    $this->session->set_userdata('acadYearView', ACAD_YEAR);
                    redirect('/');
                } else {
                    $this->session->set_flashdata('error', 'An error has occurred.');
                    redirect('/login');
                }
            }
        } catch(ErrorException $e) {
            $this->session->set_flashdata('error', 'An error has occurred.');
            redirect('/login');
        }
    }

}
