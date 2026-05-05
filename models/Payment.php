<?php
class Payment {
    private $conn;
    private $table = 'payments';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ambil semua payment beserta info application
    public function getAll() {
        $query = "SELECT p.*, a.country, a.visa_type, ap.name AS applicant_name
                  FROM {$this->table} p
                  LEFT JOIN applications a  ON p.application_id = a.id
                  LEFT JOIN applicants ap   ON a.applicant_id   = ap.id
                  ORDER BY p.created_at DESC";
        return mysqli_query($this->conn, $query);
    }

    // Ambil payment by ID
    public function getById($id) {
        $id = (int) $id;
        $query = "SELECT p.*, a.country, a.visa_type, ap.name AS applicant_name
                  FROM {$this->table} p
                  LEFT JOIN applications a  ON p.application_id = a.id
                  LEFT JOIN applicants ap   ON a.applicant_id   = ap.id
                  WHERE p.id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Ambil payment by application_id
    public function getByApplication($application_id) {
        $application_id = (int) $application_id;
        $query = "SELECT * FROM {$this->table} WHERE application_id=$application_id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Tambah payment baru
    public function create($application_id, $payment_type, $amount_total, $dp_amount, $amount_paid, $status = 'unpaid', $proof = null, $paid_at = null) {
        $application_id = (int) $application_id;
        $payment_type   = mysqli_real_escape_string($this->conn, $payment_type);
        $amount_total   = (float) $amount_total;
        $dp_amount      = (float) $dp_amount;
        $amount_paid    = (float) $amount_paid;
        $status         = mysqli_real_escape_string($this->conn, $status);
        $proof          = $proof   ? "'" . mysqli_real_escape_string($this->conn, $proof) . "'"   : "NULL";
        $paid_at        = $paid_at ? "'" . mysqli_real_escape_string($this->conn, $paid_at) . "'" : "NULL";

        $query = "INSERT INTO {$this->table}
                    (application_id, payment_type, amount_total, dp_amount, amount_paid, status, proof, paid_at)
                  VALUES
                    ($application_id, '$payment_type', $amount_total, $dp_amount, $amount_paid, '$status', $proof, $paid_at)";
        mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }

    // Update payment
    public function update($id, $payment_type, $amount_total, $dp_amount, $amount_paid, $status, $proof = null, $paid_at = null) {
        $id           = (int) $id;
        $payment_type = mysqli_real_escape_string($this->conn, $payment_type);
        $amount_total = (float) $amount_total;
        $dp_amount    = (float) $dp_amount;
        $amount_paid  = (float) $amount_paid;
        $status       = mysqli_real_escape_string($this->conn, $status);
        $proof        = $proof   ? "'" . mysqli_real_escape_string($this->conn, $proof) . "'"   : "NULL";
        $paid_at      = $paid_at ? "'" . mysqli_real_escape_string($this->conn, $paid_at) . "'" : "NULL";

        $query = "UPDATE {$this->table}
                  SET payment_type='$payment_type', amount_total=$amount_total,
                      dp_amount=$dp_amount, amount_paid=$amount_paid,
                      status='$status', proof=$proof, paid_at=$paid_at
                  WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Update status pembayaran
    public function updateStatus($id, $status, $proof = null, $paid_at = null) {
        $id      = (int) $id;
        $status  = mysqli_real_escape_string($this->conn, $status);
        $proof   = $proof   ? "'" . mysqli_real_escape_string($this->conn, $proof) . "'"   : "NULL";
        $paid_at = $paid_at ? "'" . mysqli_real_escape_string($this->conn, $paid_at) . "'" : "NULL";

        $query = "UPDATE {$this->table}
                  SET status='$status', proof=$proof, paid_at=$paid_at
                  WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Hapus payment
    public function delete($id) {
        $id = (int) $id;
        $query = "DELETE FROM {$this->table} WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Hitung total yang sudah bayar
    public function countPaid() {
        $result = mysqli_fetch_row(mysqli_query($this->conn,
            "SELECT COUNT(*) FROM {$this->table} WHERE status='paid'"));
        return $result[0];
    }
}