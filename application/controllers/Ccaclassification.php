<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Ccaclassification extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn || !$this->account->is_admin) {
            redirect('/');
        }
        $this->load->model('ccaclassifications_model');
    }

    public function view($search = null)
    {
        $data = [];
        $data['ccaclassifications'] = $this->ccaclassifications_model->getAll();

        $data['csrf'] = [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['subSubMenu'] = 'viewCcaclassification';
        $data['this'] = $this;
        $this->twig->display('ccaclassification/view', $data);
    }

    public function edit($id = null)
    {
        $data = [];
        if ($id) {
            // editing existing cca
            $data['ccaclassification'] = $this->ccaclassifications_model->getById($id);
            if ($data['ccaclassification'] === false) {
                $this->session->set_flashdata('error', 'CCA Classification not found!');
                redirect('/ccaclassification/view');
            }
        }

        $data['csrf'] = [
            'name' => $this->security->get_csrf_token_name(),
            'hash' => $this->security->get_csrf_hash()
        ];

        $data['mainMenu'] = 'admin';
        $data['subMenu'] = 'cca';
        $data['this'] = $this;
        $this->twig->display('ccaclassification/edit', $data);
    }

    public function update()
    {
        if (!$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $input = $this->input->post();

        if (isset($input['id'])) {
            // update
            $exist = $this->ccaclassifications_model->getByName($input['name']);
            if ($exist && $exist->id !== $input['id']){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/ccaclassification/edit/'.$input['id']);
            }

            $result = $this->ccaclassifications_model->update($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Classification successfully updated!');
                redirect('/ccaclassification/edit/'.$input['id']);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/ccaclassification/edit/'.$input['id']);
            }
        } else {
            // add
            $exist = $this->ccaclassifications_model->getByName($input['name']);
            if ($exist){
                $this->session->set_flashdata('error', 'Name already exists!');
                redirect('/ccaclassification/edit');
            }

            $result = $this->ccaclassifications_model->insert($input);
            if ($result) {
                $this->session->set_flashdata('success', 'CCA Classification successfully created!');
                redirect('/ccaclassification/edit/'.$result);
            } else {
                $this->session->set_flashdata('error', 'An error has occurred!');
                redirect('/ccaclassification/edit');
            }
        }
    }

    public function delete()
    {
        if (!$this->input->post() || !$this->editable) {
            redirect('/');
        }

        $id = $this->input->post('id');

        $result = $this->ccaclassifications_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'CCA Classification successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        redirect('/ccaclassification/view');
    }

}
