<?php
class Inventory {
    private $conn;
    private $table_name = 'inventory';
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    public function checkStock($enterprise_id, $product_id, $quantity) {
        $query = 'SELECT (initial_quantity + additional_quantity - used_quantity) as current_stock 
                 FROM ' . $this->table_name . '
                 WHERE enterprise_id = ? AND product_id = ?';
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$enterprise_id, $product_id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row['current_stock'] >= $quantity;
    }
    
    public function updateStock($enterprise_id, $product_id, $quantity) {
        $query = 'UPDATE ' . $this->table_name . '
                 SET used_quantity = used_quantity + ?
                 WHERE enterprise_id = ? AND product_id = ?';
        
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$quantity, $enterprise_id, $product_id]);
    }

    public function getCurrentStock($enterprise_id) {
        $query = 'SELECT SUM(initial_quantity + additional_quantity - used_quantity) as current_stock 
                  FROM ' . $this->table_name . ' 
                  WHERE enterprise_id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$enterprise_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['current_stock'] ?? 0;
    }

    public function getDashboardStats($enterprise_id) {
        $query = 'SELECT i.*, mp.name as product_name, e.name as enterprise_name,
                  (i.initial_quantity + i.additional_quantity - i.used_quantity) as current_stock
                  FROM ' . $this->table_name . ' i
                  JOIN master_products mp ON mp.id = i.product_id
                  JOIN enterprises e ON e.id = i.enterprise_id
                  WHERE i.enterprise_id = ?';
                  
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$enterprise_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}