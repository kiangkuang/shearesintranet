<?php

class Ccas_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'ccas';
    }
    
    public function getAllJoinTypeNameJoinClassificationName()
    {
        $acad_year = $this->session->acadYearView;

        $this->db->select('ccas.*, ccatypes.name AS type_name, ccaclassifications.name AS classification_name');
        $this->db->where('acad_year', $acad_year);
        $this->db->join('ccatypes', 'ccas.type_id = ccatypes.id');
        $this->db->join('ccaclassifications', 'ccas.classification_id = ccaclassifications.id');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    // used by cca/edit
    public function getByIdAcadYear($id, $acad_year = null)
    {
        if ($acad_year === null) {
            $acad_year = $this->session->acadYearView;
        }

        $this->db->where('id', $id);
        $this->db->where('acad_year', $acad_year);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

}
