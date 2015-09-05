<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package        CodeIgniter
 * @author        EllisLab Dev Team
 * @copyright        Copyright (c) 2008 - 2014, EllisLab, Inc.
 * @copyright        Copyright (c) 2014 - 2015, British Columbia Institute of Technology (http://bcit.ca/)
 * @license        http://codeigniter.com/user_guide/license.html
 * @link        http://codeigniter.com
 * @since        Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * CodeIgniter Model Class
 *
 * @package        CodeIgniter
 * @subpackage    Libraries
 * @category    Libraries
 * @author        EllisLab Dev Team
 * @link        http://codeigniter.com/user_guide/libraries/config.html
 */
class CI_Model {

    /**
     * Constructor
     *
     * @access public
     */
    function __construct()
    {
        log_message('debug', "Model Class Initialized");
    }

    /**
     * __get
     *
     * Allows models to access CI's loaded classes using the same
     * syntax as controllers.
     *
     * @param    string
     * @access private
     */
    function __get($key)
    {
        $CI =& get_instance();
        return $CI->$key;
    }

    function getAll()
    {
        $query = $this->db->get($this->db_name);

        if ($query->num_rows() > 0) {
            return $query->result();
        }

        return false;
    }

    function getById($id)
    {
        $query = $this->db->get_where($this->db_name, ['id' => $id]);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    function getByShortname($shortname)
    {
        $query = $this->db->get_where($this->db_name, ['shortname' => $shortname]);

        if ($query->num_rows() > 0) {
            return $query->first_row();
        }

        return false;
    }

    function insert($data)
    {
        return $this->db->insert($this->db_name , $data);
    }

    function insertBatch($data)
    {
        return $this->db->insert_batch($this->db_name , $data);
    }

    function update($data)
    {
        $this->db->where('id', $data['id']);
        return $this->db->update($this->db_name, $data);
    }

    function updateBatch($data)
    {
        $this->db->trans_start();
        $this->db->update_batch($this->db_name, $data, 'id'); 
        $this->db->trans_complete();
        return $this->db->trans_status(); 
    }

    function deleteById($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->db_name);
    }
    
}
// END Model Class

/* End of file Model.php */
/* Location: ./system/core/Model.php */