<?php

class Memberships_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'memberships';
    }
    
    public function getByCcaId($cca_id)
    {
        $this->db->where('cca_id', $cca_id);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }
    
    public function getByAccountId($account_id)
    {
        $this->db->where('account_id', $account_id);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function deleteByAccountId($account_id)
    {
        $this->db->where('account_id', $account_id);
        return $this->db->delete($this->db_name);
    }

}
