<?php

class Memberships_model extends CI_Model {

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

}
