<?php
class UserController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function createManager($data) {
        try {
            $user = new User($this->db);
            $user->email = $data['email'];
            $user->password = password_hash($data['password'], PASSWORD_DEFAULT);
            $user->enterprise_id = $data['enterprise_id'];
            $user->role = 'manager';
            
            return ['success' => $user->create()];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function getManagersByEnterprise($enterprise_id) {
        $query = "SELECT * FROM users WHERE enterprise_id = ? AND role = 'manager'";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$enterprise_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}