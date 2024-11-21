<?php

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Get all users or a specific user by ID
	public function getUsers($limit = 10, $offset = 0, $keyword = '')
	{
		$this->db->like('name', $keyword);
		$this->db->or_like('email', $keyword);
		$this->db->or_like('role', $keyword);
		$this->db->limit($limit, $offset);
		$this->db->order_by('created_at', 'asc');
		return $this->db->get('users')->result_array();
	}

	public function countUsers()
	{
		return $this->db->count_all('users');
	}

	public function getUserById($id)
	{
		return $this->db->get_where('users', ['id' => $id])->result_array()[0];
	}
	public function getUserByEmail($email)
	{
		return $this->db->get_where('users', ['email' => $email])->row();
	}

	// Create a new user
	public function createUser($data)
	{
		$this->db->insert('users', $data);
		return $this->db->insert_id();
	}

	// Update an existing user
	public function updateUser($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('users', $data);
		return $this->db->affected_rows();
	}

	// Delete a user
	public function deleteUser($id)
	{
		$this->db->delete('users', ['id' => $id]);
		return $this->db->affected_rows();
	}
}
