<?php 
// Sertakan file konfigurasi untuk mengakses koneksi database
include 'config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"> <!-- Menetapkan encoding karakter ke UTF-8 -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Agar halaman responsif di berbagai perangkat -->
  <title>Task Details</title> <!-- Judul halaman -->
  <!-- Memuat Tailwind CSS dari CDN untuk styling berbasis utility-first -->
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <!-- Memuat font Orbitron dari Google Fonts untuk tampilan futuristik -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* Styling untuk background halaman dengan animasi gradient */
    body {
      background: linear-gradient(45deg, #0f2027, #203a43, #2c5364);
      background-size: 600% 600%;
      animation: gradientAnimation 15s ease infinite;
      font-family: 'Orbitron', sans-serif;
    }
    /* Keyframes untuk animasi gradient */
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }
    /* Styling container utama untuk menampilkan detail task dengan efek glassmorphism */
    .details-container {
      background: rgba(20, 20, 20, 0.8);
      border: 1px solid rgba(255,255,255,0.1);
      backdrop-filter: blur(5px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.3);
      border-radius: 12px;
      padding: 2rem;
    }
    /* Kelas untuk judul dengan warna putih */
    .title-white {
      color: #ffffff;
    }
    /* Kelas untuk teks dengan efek neon */
    .neon-text {
      color: #00ffff;
      text-shadow: 0 0 5px #00ffff, 0 0 10px #00ffff;
    }
    /* Styling untuk kotak deskripsi dengan tampilan gelap dan border transparan */
    .desc-box {
      background: rgba(30, 30, 30, 0.8);
      border: 1px solid rgba(255,255,255,0.2);
      border-radius: 8px;
      padding: 1rem;
    }
  </style>
</head>
<body class="min-h-screen text-white">
  <!-- Container utama dengan padding dan lebar maksimal -->
  <div class="container mx-auto px-4 py-8 max-w-4xl">
    <?php
      // Cek apakah parameter 'id' ada pada URL
      if(isset($_GET['id'])) {
          // Ambil nilai id task dari URL dan simpan ke variabel $task_id
          $task_id = $_GET['id'];
          // Siapkan prepared statement untuk mengambil data task berdasarkan id
          $stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ?");
          // Bind parameter task_id sebagai integer
          $stmt->bind_param("i", $task_id);
          // Jalankan query
          $stmt->execute();
          // Ambil hasil query sebagai array asosiatif
          $task = $stmt->get_result()->fetch_assoc();
      ?>
    <!-- Container untuk menampilkan detail task -->
    <div class="details-container">
      <!-- Tautan kembali ke halaman daftar task -->
      <a href="index.php" class="neon-text hover:opacity-75 mb-4 inline-block">← Back to List</a>
      <!-- Menampilkan nama task dengan styling judul -->
      <h1 class="title-white text-3xl font-bold mb-6">📋 <?= htmlspecialchars($task['task_name']) ?></h1>
      
      <!-- Bagian untuk menampilkan detail task -->
      <div class="space-y-4">
        <!-- Menampilkan status task -->
        <div>
          <span class="neon-text">📌 Status:</span>
          <span class="ml-2 <?= $task['status'] == 'completed' ? 'text-green-400' : 'text-yellow-400' ?>">
            <?= ucfirst($task['status']) ?>
          </span>
        </div>
        
        <!-- Menampilkan prioritas task -->
        <div>
          <span class="neon-text">🚨 Priority:</span>
          <span class="ml-2">
            <?= [
              'high' => '🔥 High',
              'medium' => '⚠️ Medium',
              'low' => '🌱 Low'
            ][$task['priority']] ?>
          </span>
        </div>
        
        <!-- Jika task memiliki due date, tampilkan tanggal dan cek apakah terlambat -->
        <?php if($task['due_date']): ?>
        <div>
          <span class="neon-text">📅 Due Date:</span>
          <span class="ml-2">
            <?= date('M j, Y', strtotime($task['due_date'])) ?>
            <?php if(strtotime($task['due_date']) < time()): ?>
            <!-- Tampilkan peringatan jika due date sudah lewat -->
            <span class="text-red-400 ml-2">⏰ Late!</span>
            <?php endif; ?>
          </span>
        </div>
        <?php endif; ?>
        
        <!-- Menampilkan deskripsi task -->
        <div>
          <span class="neon-text">📝 Description:</span>
          <p class="mt-2 p-4 desc-box">
            <!-- Mengkonversi baris baru menjadi <br> dan memastikan teks aman dengan htmlspecialchars -->
            <?= nl2br(htmlspecialchars($task['description'] ?? 'No description')) ?>
          </p>
        </div>
      </div>
    </div>
    <?php } // Penutup if(isset($_GET['id'])) ?>
  </div>
</body>
</html>
