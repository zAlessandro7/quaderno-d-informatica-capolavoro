<?php
// 1simulazione-esame2025/LpDatabaseConnection.php
require_once __DIR__ . '/lp_database_config.php';

class LpDatabaseConnection {
    private $host = LP_DB_HOST;
    private $db_user = LP_DB_USER_APP;
    private $db_pass = LP_DB_PASS_APP;
    private $charset = LP_DB_CHARSET;
    public $conn;

    public function __construct() {
        $this->conn = null;
        $actual_db_name = get_lp_db_name();
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $actual_db_name . ";charset=" . $this->charset;
            $this->conn = new PDO($dsn, $this->db_user, $this->db_pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        } catch(PDOException $e) {
            error_log("LINGUE PLATFORM DB CONNECTION ERROR: " . $e->getMessage());
            die("Errore di connessione al database della Piattaforma Lingue. (" . htmlspecialchars($actual_db_name) . ").");
        }
    }
    public function getConnection() { return $this->conn; }
    public function close() { $this->conn = null; }
}
?>