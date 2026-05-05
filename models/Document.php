<?php
class Document {
    private $conn;
    private $table = 'documents';

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Ambil semua dokumen by application_id
    public function getByApplication($application_id) {
        $application_id = (int) $application_id;
        $query = "SELECT * FROM {$this->table} WHERE application_id = $application_id ORDER BY created_at DESC";
        return mysqli_query($this->conn, $query);
    }

    // Ambil dokumen by ID
    public function getById($id) {
        $id = (int) $id;
        $query = "SELECT * FROM {$this->table} WHERE id = $id LIMIT 1";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    // Tambah dokumen baru
    public function create($application_id, $file_path) {
        $application_id = (int) $application_id;
        $file_path      = mysqli_real_escape_string($this->conn, $file_path);
        $query = "INSERT INTO {$this->table} (application_id, file_path)
                  VALUES ($application_id, '$file_path')";
        mysqli_query($this->conn, $query);
        return mysqli_insert_id($this->conn);
    }

    // Hapus dokumen
    public function delete($id) {
        $id = (int) $id;
        // Ambil file_path dulu sebelum dihapus (untuk hapus file fisik)
        $doc = $this->getById($id);
        $query = "DELETE FROM {$this->table} WHERE id=$id";
        if (mysqli_query($this->conn, $query)) {
            return $doc['file_path'] ?? null; // kembalikan path file untuk dihapus
        }
        return false;
    }

    // Hapus semua dokumen by application_id
    public function deleteByApplication($application_id) {
        $application_id = (int) $application_id;
        $query = "DELETE FROM {$this->table} WHERE application_id=$application_id";
        return mysqli_query($this->conn, $query);
    }
}