<?php
// Pastikan session sudah dimulai. Jika belum, mulai session baru.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Fungsi untuk mengambil semua task dari tabel tasks di database.
function get_all_tasks() {
    global $conn; // Mengakses variabel koneksi database yang didefinisikan secara global (dari config.php)
    
    // Membuat query SQL untuk mengambil semua task,
    // mengurutkannya berdasarkan prioritas dengan urutan: high, medium, low, lalu berdasarkan due_date secara ascending.
    $sql = "SELECT * FROM tasks ORDER BY 
            FIELD(priority, 'high', 'medium', 'low'), 
            due_date ASC";
    
    // Menjalankan query dan mengembalikan hasilnya.
    return $conn->query($sql);
}

// Memeriksa apakah request yang masuk menggunakan metode POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // ---------- Proses Penambahan Task Baru ----------
    if (isset($_POST['add_task'])) {
        // Mengambil nilai task_name dari input form dan menghapus spasi berlebih di awal dan akhir.
        $task_name = trim($_POST['task_name']);
        
        // Validasi: Pastikan task_name tidak kosong.
        if (empty($task_name)) {
            // Jika kosong, simpan pesan error ke dalam session dan redirect kembali ke index.php.
            $_SESSION['error'] = "Task name tidak boleh kosong!";
            header("Location: index.php");
            exit();
        }
        
        // Mengambil nilai description dari form (jika ada, jika tidak, set menjadi string kosong).
        $description = trim($_POST['description'] ?? '');
        // Mengambil nilai priority dari form.
        $priority = $_POST['priority'];
        // Mengambil nilai due_date dari form; jika kosong, set menjadi null.
        $due_date = $_POST['due_date'] ?: null;
        
        // Menyiapkan prepared statement untuk menambahkan task baru ke database.
        $stmt = $conn->prepare("INSERT INTO tasks (task_name, description, priority, due_date) VALUES (?, ?, ?, ?)");
        // Mengikat parameter ke query: "ssss" artinya keempat parameter diperlakukan sebagai string.
        $stmt->bind_param("ssss", $task_name, $description, $priority, $due_date);
        // Menjalankan query untuk menambahkan task.
        $stmt->execute();
    }
    
    // ---------- Proses Penghapusan Task ----------
    if (isset($_POST['delete_task'])) {
        // Mengambil task_id dari data form.
        $task_id = $_POST['task_id'];
        // Menyiapkan prepared statement untuk menghapus task berdasarkan id.
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        // Mengikat task_id sebagai integer ("i").
        $stmt->bind_param("i", $task_id);
        // Menjalankan query untuk menghapus task.
        $stmt->execute();
    }
    
    // ---------- Proses Update Task (Status, Priority, atau Task Name) ----------
    // Jika salah satu dari input 'status', 'priority', atau 'task_name' ada, proses update dilakukan.
    if (isset($_POST['status']) || isset($_POST['priority']) || isset($_POST['task_name'])) {
        // Mengambil task_id dari data form.
        $task_id = $_POST['task_id'];
        // Jika input 'status' ada, set status ke 'completed'; jika tidak, set ke 'pending'.
        $status = isset($_POST['status']) ? 'completed' : 'pending';
        // Mengambil nilai priority dan task_name dari form, atau set ke null jika tidak ada.
        $priority = $_POST['priority'] ?? null;
        $task_name = $_POST['task_name'] ?? null;
        
        // Mulai membangun query UPDATE dengan kolom status wajib diperbarui.
        $sql = "UPDATE tasks SET status = ?";
        $params = [$status]; // Array untuk menampung nilai parameter
        $types = 's'; // String tipe parameter, 's' untuk status (string)
        
        // Jika nilai priority tidak null, tambahkan kolom priority ke query.
        if ($priority !== null) {
            $sql .= ", priority = ?";
            $params[] = $priority;
            $types .= 's'; // Tambahkan tipe string untuk priority.
        }
        
        // Jika nilai task_name tidak null, tambahkan kolom task_name ke query.
        if ($task_name !== null) {
            $sql .= ", task_name = ?";
            $params[] = $task_name;
            $types .= 's'; // Tambahkan tipe string untuk task_name.
        }
        
        // Tambahkan kondisi WHERE untuk mengupdate task dengan id tertentu.
        $sql .= " WHERE id = ?";
        $params[] = $task_id;
        $types .= 'i'; // Tipe integer untuk task_id.
        
        // Siapkan prepared statement dengan query yang telah dibangun.
        $stmt = $conn->prepare($sql);
        // Mengikat parameter secara dinamis menggunakan spread operator.
        $stmt->bind_param($types, ...$params);
        // Menjalankan query untuk mengupdate task.
        $stmt->execute();
    }
    
    // Setelah memproses POST, redirect kembali ke halaman yang sama untuk mencegah duplikasi pengiriman form.
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>
