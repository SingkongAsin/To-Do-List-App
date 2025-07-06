<?php
// Sertakan file konfigurasi yang berisi koneksi ke database
include 'config.php';

// Cek apakah request yang masuk menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil nilai task_id dari data POST untuk menentukan task yang akan diupdate
    $task_id = $_POST['task_id'];
    // Ambil nilai task_name dari form dan hapus spasi ekstra di awal dan akhir
    $task_name = trim($_POST['task_name']);
    // Ambil nilai priority dari form
    $priority = $_POST['priority'];
    // Ambil nilai due_date dari form; jika tidak ada, set ke null
    $due_date = $_POST['due_date'] ?: null;
    // Ambil nilai description dari form; jika tidak ada, set ke string kosong dan hapus spasi ekstra
    $description = trim($_POST['description'] ?? '');
    
    // Siapkan prepared statement untuk mengupdate data task di tabel tasks
    $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, description = ?, priority = ?, due_date = ? WHERE id = ?");
    // Bind parameter ke query: "ssssi" artinya empat parameter pertama berupa string dan parameter terakhir berupa integer
    $stmt->bind_param("ssssi", $task_name, $description, $priority, $due_date, $task_id);
    // Jalankan query untuk memperbarui data task di database
    $stmt->execute();
}

// Setelah proses update selesai, redirect ke halaman index.php
header("Location: index.php");
// Hentikan eksekusi skrip agar tidak ada kode lain yang dijalankan
exit();
?>
