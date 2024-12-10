<?php
class Enterprise {
    private $conn;
    private $table_name = 'enterprises';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function create() {
        $query = 'INSERT INTO ' . $this->table_name . ' 
                (name, email, phone, contact_person) 
                VALUES (:name, :email, :phone, :contact_person)';
        
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':name', $this->name);
        $stmt->bindParam(':email', $this->email);
        $stmt->bindParam(':phone', $this->phone);
        $stmt->bindParam(':contact_person', $this->contact_person);
        
        return $stmt->execute();
    }

    public function getList() {
        $query = 'SELECT * FROM ' . $this->table_name . ' ORDER BY name';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = 'SELECT * FROM ' . $this->table_name . ' WHERE id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}