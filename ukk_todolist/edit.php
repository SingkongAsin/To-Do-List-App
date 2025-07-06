<?php 
// Sertakan file konfigurasi untuk koneksi database
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"> <!-- Menetapkan encoding karakter ke UTF-8 -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Agar tampilan responsif di perangkat mobile -->
  <title>Edit Task</title> <!-- Judul halaman -->
  
  <!-- Memuat Tailwind CSS dari CDN untuk menggunakan utility-first classes -->
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <!-- Memuat font Orbitron dari Google Fonts untuk tampilan yang modern dan futuristik -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  
  <style>
    /* Styling untuk background halaman dengan animasi gradient */
    body {
      background: linear-gradient(45deg, #0f2027, #203a43, #2c5364); /* Gradient background */
      background-size: 600% 600%; /* Ukuran background untuk animasi yang halus */
      animation: gradientAnimation 15s ease infinite; /* Animasi background berjalan terus menerus */
      font-family: 'Orbitron', sans-serif; /* Menggunakan font Orbitron */
    }
    /* Keyframes untuk animasi gradient */
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    /* Styling container untuk form edit task dengan efek glassmorphism */
    .edit-container {
      background: rgba(20, 20, 20, 0.8); /* Background semi-transparan */
      border: 1px solid rgba(255,255,255,0.1); /* Border tipis dengan opacity rendah */
      backdrop-filter: blur(5px); /* Efek blur di belakang container */
      box-shadow: 0 4px 8px rgba(0,0,0,0.3); /* Bayangan untuk efek depth */
      border-radius: 12px; /* Sudut container membulat */
      padding: 2rem; /* Padding di dalam container */
    }
    /* Styling judul dengan warna putih */
    h2.title-white {
      color: #ffffff;
    }
    /* Styling untuk elemen form: input, textarea, select */
    input[type="text"],
    input[type="date"],
    textarea,
    select {
      background: rgba(30, 30, 30, 0.8); /* Background gelap semi-transparan */
      border: 1px solid rgba(255,255,255,0.2); /* Border dengan opacity rendah */
      color: #fff; /* Teks berwarna putih */
    }
    /* Styling saat elemen form fokus (klik/active) */
    input:focus,
    textarea:focus,
    select:focus {
      outline: none; /* Menghilangkan outline default */
      border-color: #00ffff; /* Ubah border ke warna neon */
      box-shadow: 0 0 8px rgba(0,255,255,0.6); /* Efek bayangan neon saat fokus */
    }
    /* Styling tombol dengan efek neon */
    .btn-neon {
      background: transparent; /* Latar belakang transparan */
      border: 2px solid #00ffff; /* Border dengan warna neon */
      color: #00ffff; /* Teks dengan warna neon */
      text-shadow: 0 0 5px #00ffff; /* Efek bayangan pada teks */
      padding: 0.75rem 1.5rem; /* Padding pada tombol */
      border-radius: 8px; /* Sudut tombol membulat */
      cursor: pointer; /* Kursor pointer saat hover */
      transition: all 0.3s ease; /* Transisi halus untuk perubahan efek */
    }
    /* Efek hover pada tombol neon: bayangan dan pembesaran */
    .btn-neon:hover {
      box-shadow: 0 0 20px #00ffff;
      transform: scale(1.05);
    }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center text-white"> <!-- Atur tampilan body agar setinggi layar, center, dan teks putih -->
  <?php
    // Cek apakah parameter 'id' ada di URL untuk menentukan task yang akan diedit
    if(isset($_GET['id'])) {
      // Simpan nilai id task ke variabel $task_id
      $task_id = $_GET['id'];
      // Siapkan prepared statement untuk mengambil data task berdasarkan id
      $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
      // Bind parameter id sebagai integer
      $stmt->bind_param("i", $task_id);
      // Jalankan query
      $stmt->execute();
      // Ambil hasil query dan simpan dalam array asosiatif
      $task = $stmt->get_result()->fetch_assoc();
    }
  ?>
  <!-- Container utama untuk form edit task -->
  <div class="edit-container w-full max-w-md">
    <!-- Judul form -->
    <h2 class="title-white text-2xl font-bold mb-6">✏️ Edit Task</h2>
    <!-- Form edit task, data dikirim menggunakan metode POST ke file update_task.php -->
    <form method="POST" action="update_task.php">
      <!-- Input tersembunyi untuk menyimpan ID task yang sedang diedit -->
      <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
      
      <!-- Input untuk nama task -->
      <div class="mb-4">
        <label class="block mb-2">📝 Task Name</label>
        <!-- Nilai input diisi dengan nama task yang sudah ada, menggunakan htmlspecialchars untuk menghindari XSS -->
        <input type="text" name="task_name" value="<?= htmlspecialchars($task['task_name']) ?>" 
          class="w-full p-3 rounded-lg focus:outline-none">
      </div>
      
      <!-- Textarea untuk deskripsi task -->
      <div class="mb-4">
        <label class="block mb-2">📄 Description</label>
        <!-- Textarea dengan nilai default dari deskripsi task; jika tidak ada, tampilkan string kosong -->
        <textarea name="description" rows="4" 
          class="w-full p-3 rounded-lg resize-none focus:outline-none"><?= htmlspecialchars($task['description'] ?? '') ?></textarea>
      </div>
      
      <!-- Dropdown untuk memilih prioritas task -->
      <div class="mb-4">
        <label class="block mb-2">🚨 Priority</label>
        <select name="priority" class="w-full p-3 rounded-lg">
          <!-- Opsi untuk prioritas tinggi, sedang, dan rendah dengan pengecekan kondisi untuk menandai pilihan yang aktif -->
          <option value="high" <?= $task['priority'] == 'high' ? 'selected' : '' ?>>🔥 High</option>
          <option value="medium" <?= $task['priority'] == 'medium' ? 'selected' : '' ?>>⚠️ Medium</option>
          <option value="low" <?= $task['priority'] == 'low' ? 'selected' : '' ?>>🌱 Low</option>
        </select>
      </div>
      
      <!-- Input untuk tanggal jatuh tempo (due date) -->
      <div class="mb-6">
        <label class="block mb-2">📅 Due Date</label>
        <!-- Nilai input diisi dengan tanggal jatuh tempo task -->
        <input type="date" name="due_date" value="<?= $task['due_date'] ?>" 
          class="w-full p-3 rounded-lg">
      </div>
      
      <!-- Tombol aksi: Cancel dan Save Changes -->
      <div class="flex gap-2">
        <!-- Tautan Cancel mengarahkan kembali ke index.php -->
        <a href="index.php" class="flex-1 px-4 py-2 bg-gray-600 hover:bg-gray-700 rounded-lg text-center">
          Cancel
        </a>
        <!-- Tombol Save Changes untuk submit form edit task -->
        <button type="submit" class="flex-1 btn-neon">
          Save Changes
        </button>
      </div>
    </form>
  </div>
</body>
</html>
