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

    public function getAllOrderedByName()
    {
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

    public function getByShortname($shortname)
    {
        $query = $this->db->get_where($this->db_name, ['shortname' => $shortname]);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    public function insert($data)
    {
        return $this->db->insert($this->db_name , $data);
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
        $this->db->trans_start();
        $this->db->update_batch($this->db_name, $data, 'id'); 
        $this->db->trans_complete();
        return $this->db->trans_status(); 
    }

    public function deleteById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->db_name);
    }
}