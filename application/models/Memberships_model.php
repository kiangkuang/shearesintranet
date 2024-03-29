<?php

class Memberships_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'memberships';
    }
    
    public function getByAccountIdCcaIdAcadYear($account_id, $cca_id, $acad_year)
    {
        $this->db->where('account_id', $account_id);
        $this->db->where('cca_id', $cca_id);
        $this->db->where('acad_year', $acad_year);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    public function getByCcaIdJoinAccountName($cca_id)
    {
        $this->db->select('memberships.*, accounts.name as account_name');
        $this->db->where('cca_id', $cca_id);
        $this->db->join('accounts', 'memberships.account_id = accounts.id');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }
    
    public function getByAccountIdJoinCcaName($account_id)
    {
        $this->db->select('memberships.*, ccas.name as cca_name');
        $this->db->where('account_id', $account_id);
        $this->db->join('ccas', 'memberships.cca_id = ccas.id');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }
    
    public function getTotalPointsByAccountId($account_id)
    {
        $this->db->select_sum('points');
        $this->db->where('account_id', $account_id);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    public function deleteByAccountId($account_id)
    {
        $this->db->where('account_id', $account_id);
        return $this->db->delete($this->db_name);
    }

    public function deleteByCcaId($cca_id)
    {
        $this->db->where('cca_id', $cca_id);
        return $this->db->delete($this->db_name);
    }

}
