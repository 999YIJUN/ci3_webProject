<?php

class user_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    public function get_user()
    {
        $query = $this->db->get('users');
        return $query->result();
    }

    public function get_user_by_id($id)
    {
        $query = $this->db->get_where('users', ['user_id' => $id]);
        return $query->row();
    }

    public function get_user_by_email($email)
    {
        $query = $this->db->get_where('users', ['email' => $email]);
        return $query->row();
    }

    public function getUserByUniqueCode($code)
    {
        $query = $this->db->get_where('users', ['unique_code' => $code]);
        return $query->row();
    }

    public function insert_user($data)
    {
        $this->db->insert('users', $data);
        return $this->db->insert_id();
    }

    public function update_user($id, $data)
    {
        $this->db->where('user_id', $id);
        $this->db->update('users', $data);
        return $this->db->affected_rows();
    }

    public function update_user_verified($id)
    {
        $data = [
            'verified' => 1
        ];

        $this->db->where('user_id', $id);
        $this->db->update('users', $data);

        return $this->db->affected_rows();
    }

    public function delete_user($id)
    {
        $this->db->where('$user_id', $id);
        $this->db->delete('users');
        return $this->db->affected_rows();
    }

    // userdata 
    public function is_sort_value_exists($new_sort_value)
    {
        $this->db->where('sort', $new_sort_value);
        $query = $this->db->get('users');

        return ($query->num_rows() > 0);
    }

    public function get_sort_value($user_id)
    {
        $this->db->select('sort');
        $this->db->where('user_id', $user_id);
        $query = $this->db->get('users');

        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->sort;
        }

        return null;
    }

    public function checked_sort($sort_value, $exclude_user_id = null)
    {
        $this->db->where('sort >=', $sort_value);

        if ($exclude_user_id !== null) {
            $this->db->where('user_id !=', $exclude_user_id);
        }

        $query = $this->db->get('users');

        return $query->result_array();
    }

    public function swap_sort_values($sort_value1, $sort_value2)
    {
        $this->db->trans_start();

        $this->db->where('sort', $sort_value1);
        $this->db->update('users', ['sort' => $sort_value2]);

        $this->db->where('sort', $sort_value2);
        $this->db->update('users', ['sort' => $sort_value1]);

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
