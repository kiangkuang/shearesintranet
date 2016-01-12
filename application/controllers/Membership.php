<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Membership extends MY_Controller {

    public function __construct()
    {
        parent::__construct();

        if (!$this->isLoggedIn) {
            redirect('/');
        }
        $this->load->model('memberships_model');
    }

    public function addMembership()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }
        if ($this->input->post('cca_id')) {
            $type = 'cca';
        } elseif ($this->input->post('account_id')) {
            $type = 'account';
        }

        if ($type === 'cca') {
            // cca adding members
            foreach ($this->input->post('account_ids') as $account_id) {
                $input[] = [
                    'cca_id' => $this->input->post('cca_id'),
                    'account_id' => $account_id,
                    'acad_year' => ACAD_YEAR, 
                ];
            }
        }

        if ($type === 'account') {
            // accounts joining ccas
            foreach ($this->input->post('cca_ids') as $cca_id) {
                $input[] = [
                    'account_id' => $this->input->post('account_id'),
                    'cca_id' => $cca_id,
                    'acad_year' => ACAD_YEAR, 
                ];
            }
        }

        $result = $this->memberships_model->insertBatch($input);
        if ($result) {
            $this->session->set_flashdata('success', count($input).' membership successfully added!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        if ($type === 'cca') {
            redirect('/cca/edit/'.$this->input->post('cca_id'));
        } elseif ($type === 'account') {
            redirect('/account/edit/'.$this->input->post('account_id'));
        }
    }

    public function updateMemberships()
    {
        if (!$this->account->is_admin || !$this->input->post() || !$this->editable) {
            redirect('/');
        }
        if ($this->input->post('cca_id')) {
            $type = 'cca';
        } elseif ($this->input->post('account_id')) {
            $type = 'account';
        }

        $input = $this->input->post('memberships');

        $result = $this->memberships_model->updateBatch($input);

        if ($result !== false) {
            $this->session->set_flashdata('success', 'Memberships successfully updated!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }
        
        if ($type === 'cca') {
            redirect('/cca/edit/'.$this->input->post('cca_id'));
        } elseif ($type === 'account') {
            redirect('/account/edit/'.$this->input->post('account_id'));
        }
    }

    public function delete($id = null)
    {
        if (!$this->account->is_admin || !$this->editable || !$id) {
            redirect('/');
        }

        $redirect = $this->input->get('redirect') ? : '/';
        $membership = $this->memberships_model->getById($id);
        $result = $this->memberships_model->deleteById($id);
        if ($result) {
            $this->session->set_flashdata('success', 'Membership successfully deleted!');
        } else {
            $this->session->set_flashdata('error', 'An error has occurred!');
        }

        redirect($redirect);
    }

}
