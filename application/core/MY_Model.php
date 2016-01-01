<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class MY_Model extends CI_Model {

    public function getAll()
    {
        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getByAcadYearOrderedByName($acad_year)
    {
        $this->db->where('acad_year', $acad_year);
        $this->db->order_by('name');
        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function getById($id)
    {
        $query = $this->db->get_where($this->db_name, ['id' => $id]);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    // used by account/edit and cca/edit
    public function getByIdAcadYear($id, $acad_year)
    {
        $this->db->where('id', $id);
        $this->db->where('acad_year', $acad_year);

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    public function getByAcadYear($acad_year = ACAD_YEAR)
    {
        $this->db->where('acad_year', $acad_year);
        $this->db->order_by('name', 'asc');

        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    public function insert($data)
    {
        $this->db->insert($this->db_name , $data);
        return $this->db->insert_id();
    }

    public function insertBatch($data)
    {
        return $this->db->insert_batch($this->db_name , $data);
    }

    public function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update($this->db_name, $data);
    }

    public function updateBatch($data)
    {
        return $this->db->update_batch($this->db_name, $data, 'id');
    }

    public function deleteById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->db_name);
    }
}
