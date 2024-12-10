<?php
class AuthController {
    private $db;
    
    public function __construct($db) {
        $this->db = $db;
    }
    
    public function login($email, $password) {
        $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(":email", $email);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $user['password'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'email' => $user['email'],
                    'enterprise_id' => $user['enterprise_id'],
                    'role' => $user['role']
                ];
                return ['success' => true];
            }
        }
        return ['success' => false, 'message' => 'Email hoặc mật khẩu không đúng'];
    }
    
    public function logout() {
        session_destroy();
    }
    
    public function isLoggedIn() {
        return isset($_SESSION['user']);
    }

    public function isAdmin() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }

    public function isManager() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'manager';
    }

    public function isDistributor() {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'distributor';
    }
}