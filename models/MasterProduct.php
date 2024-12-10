<?php
class MasterProduct {
    private $conn;
    private $table_name = 'master_products';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function getList() {
        $query = 'SELECT * FROM ' . $this->table_name . ' WHERE status = 1';
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

    public function getProductsByEnterprise($enterprise_id) {
        $query = 'SELECT mp.*, 
                (i.initial_quantity + i.additional_quantity - i.used_quantity) as current_stock
                FROM ' . $this->table_name . ' mp
                JOIN inventory i ON i.product_id = mp.id
                WHERE i.enterprise_id = ? 
                AND mp.status = 1
                AND (i.initial_quantity + i.additional_quantity - i.used_quantity) > 0';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$enterprise_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}