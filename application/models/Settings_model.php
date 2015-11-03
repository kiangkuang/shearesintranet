<?php

class Settings_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'settings';
    }
    
    public function getByAcadYear($acad_year = ACAD_YEAR)
    {
        $this->db->where('acad_year', $acad_year);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

}
