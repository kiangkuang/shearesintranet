<?php

class Ccatypes_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'ccatypes';
    }

}
