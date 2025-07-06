<?php
// Mendefinisikan nama host dari server database (localhost digunakan untuk server lokal)
$host = "localhost";

// Mendefinisikan username untuk koneksi ke database (root biasanya digunakan pada lingkungan pengembangan lokal)
$user = "root";

// Mendefinisikan password untuk koneksi ke database (dalam contoh ini, tidak ada password)
$password = "";

// Mendefinisikan nama database yang akan digunakan oleh aplikasi (todo_app adalah nama database)
$dbname = "todo_app";

// Membuat objek mysqli baru dengan parameter yang telah didefinisikan
// Objek ini akan digunakan untuk menghubungkan aplikasi dengan database MySQL
$conn = new mysqli($host, $user, $password, $dbname);

// Mengecek apakah koneksi ke database gagal
if ($conn->connect_error) {
    // Jika terjadi error koneksi, hentikan eksekusi skrip dan tampilkan pesan error
    die("Connection failed: " . $conn->connect_error);
}
?>
