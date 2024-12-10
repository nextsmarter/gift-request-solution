<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';
require_once 'config/config.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/RequestController.php';
require_once 'models/Enterprise.php';
require_once 'models/Product.php';
require_once 'models/Inventory.php';
require_once 'models/Request.php';
require_once 'models/User.php';
require_once 'models/Distributor.php';

class App {
    private $db;
    private $auth;
    private $request_model;
    private $product_model;
    private $inventory_model;
    private $enterprise_model;
    private $request_controller;
    private $user_controller;
    private $distributor_model;
    private $master_product_model;
    private $logFile;

    public function __construct() {
        $this->logFile = __DIR__ . '/logs/app.log';
        try {
            $database = new Database();
            $this->db = $database->getConnection();
            
            $this->auth = new AuthController($this->db);
            $this->request_model = new Request($this->db);
            $this->product_model = new Product($this->db);
            $this->inventory_model = new Inventory($this->db);
            $this->enterprise_model = new Enterprise($this->db);
            $this->master_product_model = new MasterProduct($this->db);
            $this->distributor_model = new Distributor($this->db);
            $this->user_controller = new UserController($this->db);
            $this->request_controller = new RequestController($this->db);
            
        } catch (Exception $e) {
            file_put_contents($this->logFile, date('Y-m-d H:i:s') . ' Init Error: ' . $e->getMessage() . '\n', FILE_APPEND);
        }
    }

    public function run() {
        // ... existing run() method code ...
    }
}

try {
    $app = new App();
    $app->run();
} catch (Exception $e) {
    error_log('Critical Error: ' . $e->getMessage());
    include 'views/500.php';
}