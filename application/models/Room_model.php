<?php

class Room_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getRooms($limit = 10, $offset = 0, $keyword = '')
	{
		$this->db->like('name', $keyword);
		$this->db->or_like('type', $keyword);
		$this->db->or_like('price', $keyword);
		$this->db->or_like('description', $keyword);
		$this->db->limit($limit, $offset);
		$this->db->order_by('created_at', 'asc');
		return $this->db->get('rooms')->result_array();
	}

	public function getRoomById($id)
	{
		$query = $this->db->get_where('rooms', ['id' => $id])->result_array();
		if (empty($query)) {
			return null;
		}
		return $query[0];
	}

	public function countRooms()
	{
		return $this->db->count_all('rooms');
	}

	public function createRoom($data)
	{
		$this->db->insert('rooms', $data);
		return $this->db->insert_id();
	}

	public function updateRoom($id, $data)
	{
		$this->db->where('id', $id);
		$this->db->update('rooms', $data);
		return $this->db->affected_rows();
	}

	public function deleteRoom($id)
	{
		$this->db->delete('rooms', ['id' => $id]);
		return $this->db->affected_rows();
	}
}
