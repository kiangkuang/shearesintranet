<?php

class Ccas_model extends CI_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'ccas';
    }
    
    public function getByType($type)
    {
        $this->db->where('type', $type);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getByClassification($classification)
    {
        $this->db->where('classification', $classification);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getByTypeOrClassification($query)
    {
        if ($type = $this->getByType($query)) {
            return $type;
        } elseif ($classification = $this->getByClassification($query)) {
            return $classification;
        } else {
            return false;
        }
    }

}
