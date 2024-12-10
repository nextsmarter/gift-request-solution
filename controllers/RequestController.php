<?php
class RequestController {
    private $db;
    private $inventory;
    private $request_model;
    private $auth;
    
    public function __construct($db) {
        $this->db = $db;
        $this->inventory = new Inventory($db);
        $this->request_model = new Request($db);
        $this->auth = new AuthController($db);
    }
    
    public function createRequest($data) {
        if ($this->auth->isDistributor()) {
            $distributor = new Distributor($this->db);
            if (!$distributor->checkLimit($_SESSION['user']['email'], $data['quantity'])) {
                return ['success' => false, 'message' => 'Vượt quá hạn mức cho phép'];
            }
        }

        if (!$this->inventory->checkStock($data['enterprise_id'], $data['product_id'], $data['quantity'])) {
            return ['success' => false, 'message' => 'Không đủ số lượng trong kho'];
        }
        
        try {
            $this->db->beginTransaction();
            
            $requestData = [
                'enterprise_id' => $_SESSION['user']['enterprise_id'],
                'product_id' => $data['product_id'],
                'quantity' => $data['quantity'],
                'custom_request' => $data['custom_request'] ?? null,
                'created_by' => $_SESSION['user']['email'],
                'recipient_name' => $data['recipient_name'],
                'recipient_phone' => $data['recipient_phone'],
                'delivery_address' => $data['delivery_address'],
                'notes' => $data['notes'] ?? null
            ];
            
            $requestId = $this->request_model->create($requestData);
            $this->inventory->updateStock($data['enterprise_id'], $data['product_id'], $data['quantity']);
            
            $this->db->commit();
            
            // Get request details for webhook
            $requestData = $this->request_model->getRequestDetail($requestId);
            $this->sendWebhookNotification($requestData);
            
            return ['success' => true, 'message' => 'Tạo yêu cầu thành công'];
            
        } catch (Exception $e) {
            $this->db->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    private function sendWebhookNotification($requestData) {
        try {
            $managerQuery = "SELECT email FROM users WHERE enterprise_id = ? AND role = 'manager'";
            $stmt = $this->db->prepare($managerQuery);
            $stmt->execute([$requestData['enterprise_id']]);
            $managerEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $adminQuery = "SELECT email FROM users WHERE role = 'admin'";
            $stmt = $this->db->prepare($adminQuery);
            $stmt->execute();
            $adminEmails = $stmt->fetchAll(PDO::FETCH_COLUMN);

            $allEmails = array_merge($managerEmails, $adminEmails);

            $webhookData = [
                'emails' => implode(',', $allEmails),
                'enterprise_name' => $requestData['enterprise_name'],
                'product_name' => $requestData['product_name'],
                'quantity' => $requestData['quantity'],
                'custom_request' => $requestData['custom_request'],
                'created_by' => $requestData['created_by'],
                'created_at' => $requestData['created_at']
            ];

            $config = require __DIR__ . '/../config/config.php';
            $ch = curl_init($config['webhook_url']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($webhookData));
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
            
            curl_exec($ch);
            curl_close($ch);

        } catch (Exception $e) {
            error_log("Error sending webhook: " . $e->getMessage());
        }
    }
}