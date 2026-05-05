<?php
class Applicant {
    private $conn;
    private $table = 'applicants';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ambil semua applicant
    public function getAll() {
        $query = "SELECT * FROM {$this->table} ORDER BY created_at DESC";
        return mysqli_query($this->conn, $query);
    }

    // Ambil applicant by ID
    public function getById($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Tambah applicant baru
    public function create($name, $phone, $passport_number = '') {
        $name            = mysqli_real_escape_string($this->conn, $name);
        $phone           = mysqli_real_escape_string($this->conn, $phone);
        $passport_number = mysqli_real_escape_string($this->conn, $passport_number);
        $query = "INSERT INTO {$this->table} (name, phone, passport_number) VALUES ('$name', '$phone', '$passport_number')";
        mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }

    // Update applicant
    public function update($id, $name, $phone, $passport_number = '') {
        $id              = (int) $id;
        $name            = mysqli_real_escape_string($this->conn, $name);
        $phone           = mysqli_real_escape_string($this->conn, $phone);
        $passport_number = mysqli_real_escape_string($this->conn, $passport_number);
        $query = "UPDATE {$this->table} SET name='$name', phone='$phone', passport_number='$passport_number' WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }

    // Hapus applicant
    public function delete($id) {
        $id = (int) $id;
        $query = "DELETE FROM {$this->table} WHERE id=$id";
        return mysqli_query($this->conn, $query);
    }
}