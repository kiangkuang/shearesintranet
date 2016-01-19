<?php

class Preferences_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'preferences';
    }

    public function getByAcadYearJoinAccountName($acad_year)
    {
        $this->db->select('preferences.*, accounts.name as account_name');
        $this->db->where('preferences.acad_year', $acad_year);
        $this->db->join('accounts', 'preferences.account_id = accounts.id');
        $this->db->order_by('account_name');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

}
