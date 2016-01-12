<?php

class Ccas_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'ccas';
    }
    
    public function getByAcadYearJoinTypeNameJoinClassificationName($acad_year)
    {
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
    
    public function getByTypeIdAcadYear($type_id, $acad_year)
    {
        $this->db->where('type_id', $type_id);
        $this->db->where('acad_year', $acad_year);
        $this->db->order_by('name');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

}
