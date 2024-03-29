<?php

class Accounts_model extends MY_Model {

    public function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->db_name = 'accounts';
    }
    
    public function authenticate($user, $password)
    {
        $this->db->where('user', $user);
        $this->db->where('has_password', 1);
        $this->db->where('is_admin', 1);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            $result = $query->result();

            foreach ($result as $row) {
                $encrypted_password = sha1($password.$row->key);

                if($encrypted_password === $row->password) {
                    return $row;
                }
            }
        }

        // user manual login
        if ($this->settings->allow_login) {
            $this->db->where('user', $user);
            $this->db->where('has_password', 1);
            $this->db->where('acad_year', ACAD_YEAR);

            $query = $this->db->get($this->db_name);

            if ($query->num_rows() > 0) {
                $result = $query->result();

                foreach ($result as $row) {
                    $encrypted_password = sha1($password.$row->key);

                    if($encrypted_password === $row->password) {
                        return $row;
                    }
                }
            }
        }

        return false;
    }

    public function getByUserAcadYear($user, $acad_year)
    {
        $this->db->where('user', $user);
        $this->db->where('acad_year', $acad_year);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    public function getAcadYears()
    {
        $this->db->select('acad_year');
        $this->db->order_by('acad_year', 'desc');
        $this->db->distinct();

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getByAdmin()
    {
        $this->db->where('is_admin', 1);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getAdminUser($user)
    {
        $this->db->where('is_admin', 1);
        $this->db->where('user', $user);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

}
