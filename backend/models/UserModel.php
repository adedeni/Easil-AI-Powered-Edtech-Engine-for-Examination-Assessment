<?php
class UserModel {
	private $db;

	public function __construct($db) {
		$this->db = $db;
	}

	public function createUser($data) {
		return $this->db->insert('users', $data);
	}

	public function updateUser($id, $data) {
		return $this->db->update('users', $id, $data);
	}

	public function deleteUser($id) {
		return $this->db->delete('users', ['id', '=', $id]);
	}

	public function getUser($id) {
		$sql = "SELECT * FROM users WHERE id = ?";
		$this->db->query($sql, [$id]);
		return $this->db->first();
	}

	public function getUsers($filters = []) {
		$sql = "SELECT * FROM users WHERE 1=1";
		$params = [];
		if (!empty($filters['role_id'])) {
			$sql .= " AND role_id = ?";
			$params[] = $filters['role_id'];
		}
		if (!empty($filters['status'])) {
			$sql .= " AND status = ?";
			$params[] = $filters['status'];
		}
		if (!empty($filters['keywords'])) {
			$sql .= " AND (username LIKE ? OR name LIKE ? OR email LIKE ?)";
			$kw = "%" . $filters['keywords'] . "%";
			$params[] = $kw; $params[] = $kw; $params[] = $kw;
		}
		$sql .= " ORDER BY created_at DESC";
		$this->db->query($sql, $params);
		return $this->db->results();
	}

	public function getRoles() {
		$sql = "SELECT * FROM roles ORDER BY id ASC";
		$this->db->query($sql);
		return $this->db->results();
	}

	public function assignRole($userId, $roleId) {
		return $this->db->update('users', $userId, ['role_id' => $roleId]);
	}
}
