<?php
class Application {
    private $conn;
    private $table = 'applications';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ambil semua application beserta nama applicant
    public function getAll() {
        $query = "SELECT a.*, ap.name AS applicant_name, ap.phone, ap.passport_number
                  FROM {$this->table} a
                  LEFT JOIN applicants ap ON a.applicant_id = ap.id
                  ORDER BY a.created_at DESC";
        return mysqli_query($this->conn, $query);
    }

    // Ambil application by ID
    public function getById($id) {
        $id = (int) $id;
        $query = "SELECT a.*, ap.name AS applicant_name, ap.phone, ap.passport_number
                  FROM {$this->table} a
                  LEFT JOIN applicants ap ON a.applicant_id = ap.id
                  WHERE a.id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Tambah application baru
    public function create($applicant_id, $country, $visa_type, $status = 'in_process') {
        $applicant_id = (int) $applicant_id;
        $country      = mysqli_real_escape_string($this->conn, $country);
        $visa_type    = mysqli_real_escape_string($this->conn, $visa_type);
        $status       = mysqli_real_escape_string($this->conn, $status);
        $query = "INSERT INTO {$this->table} (applicant_id, country, visa_type, status)
                  VALUES ($applicant_id, '$country', '$visa_type', '$status')";
        mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }

    // Update application
    public function update($id, $country, $visa_type, $status) {
        $id        = (int) $id;
        $country   = mysqli_real_escape_string($this->conn, $country);
        $visa_type = mysqli_real_escape_string($this->conn, $visa_type);
        $status    = mysqli_real_escape_string($this->conn, $status);
        $query = "UPDATE {$this->table}
                  SET country='$country', visa_type='$visa_type', status='$status'
                  WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Update status saja
    public function updateStatus($id, $status) {
        $id     = (int) $id;
        $status = mysqli_real_escape_string($this->conn, $status);
        $query  = "UPDATE {$this->table} SET status='$status' WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Hapus application
    public function delete($id) {
        $id = (int) $id;
        $query = "DELETE FROM {$this->table} WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Hitung total per status
    public function countByStatus($status) {
        $status = mysqli_real_escape_string($this->conn, $status);
        $result = mysqli_fetch_row(mysqli_query($this->conn,
            "SELECT COUNT(*) FROM {$this->table} WHERE status='$status'"));
        return $result[0];
    }
}