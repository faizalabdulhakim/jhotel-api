<?php

class User_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	// Get all users or a specific user by ID
	public function getUsers($id = null)
	{
		if ($id === null) {
			return $this->db->get('users')->result_array();
		} else {
			return $this->db->get_where('users', ['id' => $id])->result_array();
		}
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
