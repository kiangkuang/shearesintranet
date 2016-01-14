<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Account extends MY_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('accounts_model');
        $this->load->model('memberships_model');
        $this->load->library('account_library');
    }

    public function index()
    {
        if (!$this->isLoggedIn) {
            $this->login();
        } elseif ($this->account->is_admin) {
            redirect('/account/view');
        } else {
            $data['this'] = $this;
            $this->twig->display('account/home', $data);
        }
    }

    public function login()
    {
        if ($this->isLoggedIn) {
            redirect('/');
        }

        $data = [];

        $data['this'] = $this;
        $this->twig->display('account/login', $data);
    }

    public function processLogin()
    {
        if ($this->isLoggedIn || !$this->input->post()) {
            redirect('/');
        }

        $data = $this->input->post();
        $account = $this->accounts_model->authenticate($data['user'], $data['password']);

        if ($account) {
            $this->session->set_userdata('accountId', $account->id);
            $this->session->set_userdata('acadYearView', ACAD_YEAR);
            if ($account->is_admin) {
                redirect('/account/view');
            } else {
                redirect('/');
            }
        } else {
            $this->session->set_flashdata('error', 'Incorrect login or password.');
            redirect('/login');
        }
    }

    public function processOpenId()
    {
        if ($this->isLoggedIn) {
            redirect('/');
        }

        if (!$this->settings->allow_login) {
            $this->session->set_flashdata('error', 'Login currently is disabled.');
            redirect('/login');
        }

        try {
            # Change 'localhost' to your domain name.
            $openid = new LightOpenID(DOMAIN);
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
                $this->session->set_flashdata('error', 'Please press allow!');
                redirect('/');
            }
            else {
                if ($openid->validate()) {
                    $openIdAttributes = $openid->getAttributes();
                    $account = $this->accounts_model->getByUserAcadYear($openIdAttributes['namePerson/friendly'], ACAD_YEAR);

                    if ($account) {
                        $this->session->set_userdata('accountId', $account->id);
                        $this->session->set_userdata('acadYearView', ACAD_YEAR);
                        redirect('/');
                    } else {
                        $this->session->set_flashdata('error', 'Account not found in intranet.');
                        redirect('/');
                    }
                } else {
                    $this->session->set_flashdata('error', 'An error has occurred.');
                    redirect('/');
                }
            }
        } catch(ErrorException $e) {
            $this->session->set_flashdata('error', 'An error has occurred.');
            redirect('/');
        }
    }

    public function logout()
    {
        $this->session->sess_destroy();
        redirect('/');
    }

    public function view()
    {
        if (!$this->isLoggedIn || !$this->account->is_admin) {
            redirect('/');
        }

        $data = [];

        $accounts = $this->accounts_model->getByAcadYear($this->session->acadYearView);
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

    public function edit($id = null)
    {
        if (!$this->isLoggedIn || !$this->account->is_admin) {
            redirect('/');
        }

        $data = [];
        if ($id) {
            $data['account'] = $this->accounts_model->getByIdAcadYear($id, $this->session->acadYearView);
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
        if (!$this->isLoggedIn || !$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();
        foreach ($input as &$row) {
            $row = trim($row);
        }

        if (isset($input['id'])) {
            // update
            $result = $this->accounts_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Account successfully updated!');
                redirect('/account/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/account/edit/'.$input['id']);
            }
        } else {
            // add
            $input['acad_year'] = ACAD_YEAR;

            $result = $this->accounts_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Account successfully created!');
                redirect('/account/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/account/edit');
            }
        }
    }

    public function adminChangePassword()
    {
        if (!$this->isLoggedIn || !$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();

        $input['has_password'] = isset($input['has_password']) ? 1 : 0;

        if ($input['has_password']) {
            if ($input['password'] !== $input['password2']) {
                $this->session->set_flashdata('error', 'The passwords do not match!');
                redirect('/account/edit/'.$input['id']);
            }

            unset($input['password2']);
            $input['key'] = time();
            $input['password'] = sha1($input['password'].$input['key']);
        } else {
            $input['key'] = '';
            $input['password'] = '';
        }

        $result = $this->accounts_model->update($input);
        if ($result) {
            $this->session->set_flashdata('success', 'Account password successfully updated!');
            redirect('/account/edit/'.$input['id']);
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
            redirect('/account/edit/'.$input['id']);
        }
    }

    public function changePassword()
    {
        if (!$this->isLoggedIn) {
            redirect('/');
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
                unset($input['currentPassword'], $input['password2']);
                $input['id'] = $this->account->id;
                $input['key'] = time();
                $input['password'] = sha1($input['password'].$input['key']);
                $input['has_password'] = 1;

                $result = $this->accounts_model->update($input);
                if ($result) {
                    $this->session->set_flashdata('success', 'Password successfully changed!');
                    redirect('/');
                } else {
                    $this->session->set_flashdata('error', 'An error has occurred!');
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

    public function removePassword($id = null)
    {
        if (!$this->isLoggedIn || !$this->account->is_admin || !$this->editable || !$id) {
            redirect('/');
        }

        $account = $this->accounts_model->getById($id);
        if ($account) {
            if ($account->is_admin) {
                $this->session->set_flashdata('error', 'Don\'t remove password on admin acount!');
                redirect('/account/edit/'.$id);
            }

            $input['id'] = $id;
            $input['has_password'] = 0;

            $result = $this->accounts_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'Password successfully removed!');
                redirect('/account/edit/'.$id);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/account/edit/'.$id);
            }
        }
    }

    public function delete($id = null)
    {
        if (!$this->isLoggedIn || !$this->account->is_admin || !$this->editable || !$id) {
            redirect('/');
        }

        $result = $this->accounts_model->deleteById($id);
        $result2 = $this->memberships_model->deleteByAccountId($id);
        if ($result && $result2) {
            $this->session->set_flashdata('success', 'Account successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        redirect('/account/view');
    }

    public function import()
    {
        if (!$this->account->is_admin || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();
        if ($input) {
            $config['upload_path']          = './uploads/';
            $config['allowed_types']        = 'csv';
            $config['max_size']             = 2048;

            $this->load->library('upload', $config);

            if ( ! $this->upload->do_upload('file')) {
                $this->session->set_flashdata('error', $this->upload->display_errors('', ''));
                redirect('account/import');
            } else {
                $upload = $this->upload->data();
                $csvFile = new Keboola\Csv\CsvFile($upload['full_path']);
                $import = [];
                $update = [];
                foreach ($csvFile as $row) {
                    // ignore header row and empty names
                    if (trim($row[0]) !== 'Name' && trim($row[1]) !== 'NUSNET ID' && trim($row[0]) !== '') {
                        $importRow['user'] = trim($row[1]);
                        $importRow['key'] = time();
                        $importRow['password'] = sha1(''.$importRow['key']);
                        $importRow['has_password'] = 0;
                        $importRow['name'] = trim($row[0]);
                        $importRow['room'] = trim($row[2]);
                        $importRow['email'] = trim($row[3]);
                        $importRow['contact'] = trim($row[4]);
                        $importRow['acad_year'] = ACAD_YEAR;

                        $existingRow = $this->accounts_model->getByUserAcadYear($importRow['user'], ACAD_YEAR);
                        if ($existingRow) {
                            $importRow['id'] = $existingRow->id;
                            $update[] = $importRow;
                        } else {
                            unset($importRow['id']);
                            $import[] = $importRow;
                        }
                    }
                }

                if ($import) {
                    $importResult = $this->accounts_model->insertBatch($import);
                }
                if ($update) {
                    $updateResult = $this->accounts_model->updateBatch($update);
                }

                if (isset($importResult) && $importResult !== false || isset($updateResult) && $updateResult !== false) {
                    $successMsg = count($import) ? count($import) . ' new accounts added!<br>' : '';
                    $successMsg .= count($update) ? count($update) . ' existing accounts updated!' : '';

                    $this->session->set_flashdata('success', $successMsg);
                    $this->session->set_flashdata('imported', $import);
                    $this->session->set_flashdata('updated', $update);
                    redirect('account/import');
                } else {
                    $this->session->set_flashdata('error', 'Nothing imported!');
                    redirect('account/import');
                }
            }
        }

        $data = [];

        if ($this->session->imported) {
            $data['imported'] = $this->session->imported;
        }
        if ($this->session->updated) {
            $data['updated'] = $this->session->updated;
        }

        $data['lastAcadYear'] = substr(ACAD_YEAR, 0, 2)-1 . '/' . substr(ACAD_YEAR, 0, 2);
        $data['lastAcadYearCcas'] = $this->ccas_model->getByAcadYear($data['lastAcadYear']);

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'account';
        $data['this'] = $this;
        $this->twig->display('account/import', $data);
    }

}
