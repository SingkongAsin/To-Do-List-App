<?php 
// Mulai session untuk menyimpan data session seperti pesan error, user login, dsb.
session_start(); 

// Sertakan file konfigurasi database agar koneksi ke database dapat digunakan
include 'config.php'; 

// Sertakan file fungsi-fungsi tambahan yang akan digunakan di halaman ini
include 'functions.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"> <!-- Menetapkan encoding karakter ke UTF-8 -->
  <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Membuat halaman responsif pada berbagai perangkat -->
  <title>Do Your List</title> <!-- Judul halaman -->
  <!-- Memuat Tailwind CSS dari CDN untuk menggunakan utility-first classes -->
  <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
  <!-- Memuat font Orbitron dari Google Fonts untuk tampilan futuristik -->
  <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&display=swap" rel="stylesheet">
  <style>
    /* Styling untuk background halaman dengan animasi gradient dan pengaturan font */
    body {
      background: linear-gradient(45deg, #0f2027, #203a43, #2c5364); /* Background gradient */
      background-size: 600% 600%; /* Ukuran background untuk animasi yang mulus */
      animation: gradientAnimation 15s ease infinite; /* Animasi gradient yang berjalan terus menerus */
      font-family: 'Orbitron', sans-serif; /* Menggunakan font Orbitron */
    }
    @keyframes gradientAnimation {
      0% { background-position: 0% 50%; }   /* Posisi awal gradient */
      50% { background-position: 100% 50%; }  /* Posisi tengah gradient */
      100% { background-position: 0% 50%; }   /* Kembali ke posisi awal */
    }
    
    /* Styling judul dengan efek neon glow */
    h1 {
      text-shadow: 0 0 10px rgba(255,255,255,0.8), 0 0 20px rgba(255,255,255,0.6);
    }
    
    /* Definisi animasi slideIn untuk task-card agar muncul dari kanan dengan efek fade-in */
    @keyframes slideIn {
      from { transform: translateX(100%); opacity: 0; }
      to { transform: translateX(0); opacity: 1; }
    }
    
    /* Styling kartu tugas dengan efek glassmorphism, border neon dan animasi slideIn */
    .task-card {
      transition: all 0.3s ease, opacity 0.5s ease; /* Transisi untuk perubahan gaya */
      background: rgba(20, 20, 20, 0.8); /* Background semi-transparan */
      border: 1px solid rgba(255,255,255,0.1); /* Border tipis dengan opacity rendah */
      box-shadow: 0 4px 8px rgba(0,0,0,0.3); /* Bayangan untuk efek 3D */
      animation: slideIn 0.5s ease-out; /* Animasi slide in saat muncul */
      backdrop-filter: blur(5px); /* Efek blur untuk tampilan glassmorphism */
      border-radius: 12px; /* Sudut membulat */
    }
    
    /* Efek hover pada kartu task: sedikit terangkat, diperbesar, dan bayangan neon */
    .task-card:hover {
      transform: translateY(-3px) scale(1.02);
      box-shadow: 0 10px 20px rgba(3, 238, 183, 0.5);
    }
    
    /* Animasi fadeOut untuk task yang sudah selesai */
    .completed-task {
      animation: fadeOut 0.5s ease-out forwards;
    }
    @keyframes fadeOut {
      to { opacity: 0; max-height: 0; margin-bottom: 0; }
    }
    
    /* Animasi bounce untuk elemen tertentu */
    .bounce { animation: bounce 0.5s; }
    @keyframes bounce {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }
    
    /* Efek hover pada elemen emoji: memperbesar dan menambahkan drop shadow neon */
    .emoji-hover:hover {
      transform: scale(1.2);
      filter: drop-shadow(0 0 8px rgba(0,255,255,0.7));
    }
    
    /* Styling futuristik untuk elemen form: input, select, dan date */
    input[type="text"],
    input[type="date"],
    select {
      background: rgba(30, 30, 30, 0.8); /* Background gelap semi-transparan */
      border: 1px solid rgba(255,255,255,0.2); /* Border dengan opacity rendah */
      color: #fff; /* Warna teks putih */
    }
    input[type="text"]::placeholder {
      color: rgba(255,255,255,0.6); /* Warna placeholder dengan opacity rendah */
    }
    input:focus,
    select:focus {
      outline: none; /* Menghilangkan outline default saat fokus */
      border-color: #00ffff; /* Mengubah border saat fokus menjadi neon */
      box-shadow: 0 0 8px rgba(0,255,255,0.6); /* Efek bayangan neon saat fokus */
    }
    
    /* Styling tombol Add Task dengan efek neon futuristik */
    .btn-add {
      background: transparent; /* Latar belakang transparan */
      border: 2px solid #00ffff; /* Border dengan warna neon */
      color: #00ffff; /* Teks dengan warna neon */
      font-weight: bold; /* Teks tebal */
      text-shadow: 0 0 5px #00ffff; /* Efek bayangan teks neon */
      padding: 0.75rem 1.5rem; /* Padding untuk ukuran tombol */
      border-radius: 8px; /* Sudut tombol membulat */
      cursor: pointer; /* Kursor pointer saat hover */
      transition: all 0.3s ease; /* Transisi halus */
    }
    .btn-add:hover {
      box-shadow: 0 0 20px #00ffff; /* Bayangan lebih besar saat hover */
      transform: scale(1.05); /* Membesarkan tombol saat hover */
    }
    
    /* Kelas khusus untuk tombol Edit dan Hapus agar background transparan dan tidak memiliki padding */
    .btn-no-bg {
      background: transparent;
      box-shadow: none;
      border: none;
      padding: 0;
    }
  </style>
</head>
<body class="min-h-screen text-white"> <!-- Mengatur tinggi minimum halaman dan warna teks -->
  <div class="container mx-auto px-4 py-8 max-w-4xl"> <!-- Container utama dengan margin otomatis, padding, dan lebar maksimal -->
    
    <!-- Judul halaman dengan styling Tailwind -->
    <h1 class="text-4xl font-bold mb-8 text-center">
      Do Your-List 📝
    </h1>
    
    <!-- Tampilkan pesan error jika ada di session -->
    <?php if(isset($_SESSION['error'])): ?>
      <div id="error-message" class="bg-red-500 text-white p-4 mb-4 rounded">
        <?= $_SESSION['error']; ?> <!-- Menampilkan pesan error dari session -->
      </div>
      <?php unset($_SESSION['error']); ?> <!-- Menghapus pesan error dari session setelah ditampilkan -->
    <?php endif; ?>
    
    <!-- Form untuk menambah task baru -->
    <form method="POST" class="mb-8 bg-gray-800 p-6 rounded-xl shadow-lg transform transition hover:scale-105">
      <div class="flex gap-4 flex-wrap">
        <!-- Input untuk nama task -->
        <div class="flex-1 relative">
          <span class="absolute left-3 top-4 text-gray-400">📝</span> <!-- Ikon di dalam input -->
          <input type="text" name="task_name" placeholder="New task..." 
                 class="w-full pl-10 p-3 rounded-lg focus:outline-none"> <!-- Input teks dengan placeholder -->
        </div>
        <!-- Input dropdown untuk memilih prioritas -->
        <div class="relative">
          <span class="absolute left-3 top-4 text-gray-400">🚨</span> <!-- Ikon untuk prioritas -->
          <select name="priority" class="pl-10 p-3 rounded-lg appearance-none">
            <option value="medium">⚠️ Medium Priority</option>
            <option value="high">🔥 High Priority</option>
            <option value="low">🌱 Low Priority</option>
          </select>
        </div>
        <!-- Input untuk tanggal jatuh tempo -->
        <div class="relative">
          <span class="absolute left-3 top-4 text-gray-400">📅</span> <!-- Ikon untuk tanggal -->
          <input type="date" name="due_date" class="pl-10 p-3 rounded-lg"> <!-- Input date -->
        </div>
        <!-- Tombol submit untuk menambah task -->
        <button type="submit" name="add_task" class="btn-add flex items-center gap-2">
          📰 Add Task <!-- Teks tombol dengan ikon -->
        </button>
      </div>
    </form>
    
    <!-- Menampilkan daftar tasks yang telah diambil dari database -->
    <div class="space-y-4">
      <?php 
      $tasks = get_all_tasks();  // Memanggil fungsi untuk mengambil semua task dari database
      while($task = $tasks->fetch_assoc()):  // Melakukan looping untuk setiap task
        // Menentukan emoji berdasarkan prioritas task
        $priority_emoji = [
          'high' => '🔥',
          'medium' => '⚠️',
          'low' => '🌿'
        ][$task['priority']];
      ?>
      <div class="task-card p-6 flex items-center gap-4 <?= $task['status'] == 'completed' ? 'opacity-75' : '' ?>">
        <!-- Jika task sudah selesai, tambahkan kelas opacity untuk efek redup -->
        <form method="POST" class="flex items-center gap-4 flex-1">
          <!-- Input tersembunyi untuk menyimpan ID task -->
          <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
          <!-- Checkbox untuk menandai status task (completed/pending) -->
          <input type="checkbox" name="status" value="completed" 
                 <?= $task['status'] == 'completed' ? 'checked' : '' ?>
                 onchange="this.form.submit()" 
                 class="w-5 h-5 rounded cursor-pointer emoji-hover">
          <div class="flex-1">
            <div class="flex items-center gap-2">
              <?= $priority_emoji ?> <!-- Menampilkan emoji prioritas -->
              <span class="<?= $task['status'] == 'completed' ? 'line-through text-gray-500' : '' ?>">
                <?= htmlspecialchars($task['task_name']) ?> <!-- Menampilkan nama task, dengan line-through jika selesai -->
              </span>
            </div>
            <?php if($task['due_date']): ?>
              <div class="text-sm text-gray-400 mt-1 flex items-center gap-2">
                📅 <?= date('M j, Y', strtotime($task['due_date'])) ?> <!-- Menampilkan tanggal jatuh tempo -->
                <?php if (strtotime($task['due_date']) < time()): ?>
                  <span class="text-red-400 animate-pulse">⏰ Late!</span> <!-- Jika due date sudah lewat, tampilkan peringatan "Late!" -->
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
          <!-- Dropdown untuk mengubah prioritas task -->
          <div class="flex items-center gap-2">
            <select name="priority" onchange="this.form.submit()"
                    class="rounded px-2 py-1 text-sm cursor-pointer emoji-hover">
              <?php foreach(['high' => '🔥', 'medium' => '⚠️', 'low' => '🌿'] as $p => $emoji): ?>
                <option value="<?= $p ?>" <?= $task['priority'] == $p ? 'selected' : '' ?>>
                  <?= $emoji ?> <?= ucfirst($p) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Tombol aksi untuk Detail, Edit, dan Hapus task -->
          <div class="flex items-center gap-2">
            <button class="btn-no-bg">
              <a href="detail.php?id=<?= $task['id'] ?>" class="text-green-400 hover:text-green-300 emoji-hover">
                🔍 Detail <!-- Link ke halaman detail task -->
              </a>
            </button>
            <button class="btn-no-bg">
              <a href="edit.php?id=<?= $task['id'] ?>" class="text-cyan-400 hover:text-cyan-300 emoji-hover">
                ✏️ Edit <!-- Link ke halaman edit task -->
              </a>
            </button>
            <button type="submit" name="delete_task" 
                    class="btn-no-bg text-red-500 hover:text-red-400 transition-all transform hover:scale-125 emoji-hover">
              🗑️ <!-- Tombol untuk menghapus task -->
            </button>
          </div>
        </form>
      </div>
      <?php endwhile; ?>
    </div>
  </div>
  
  <!-- JavaScript untuk menghapus pesan error setelah 3 detik dengan efek fade out -->
  <script>
    setTimeout(() => {
      const errorEl = document.getElementById('error-message'); // Mendapatkan elemen error berdasarkan id
      if(errorEl) {
        errorEl.style.transition = 'opacity 0.5s ease'; // Menetapkan transisi opacity
        errorEl.style.opacity = '0'; // Mengatur opacity menjadi 0 untuk efek fade out
        setTimeout(() => errorEl.remove(), 500); // Menghapus elemen error setelah transisi selesai
      }
    }, 3000); // Menunggu 3 detik sebelum memulai fade out
  </script>
  
  <!-- JavaScript untuk animasi penghapusan task dan efek hover pada emoji -->
  <script>
    // Tambahkan event listener pada tombol hapus untuk animasi fade out dan menghapus task dari tampilan
    document.querySelectorAll('[name="delete_task"]').forEach(btn => {
      btn.addEventListener('click', function(e) {
        const card = this.closest('.task-card'); // Cari elemen task-card terdekat
        card.style.animation = 'fadeOut 0.5s ease-out forwards'; // Terapkan animasi fadeOut
        setTimeout(() => card.remove(), 500); // Hapus elemen setelah animasi selesai
      });
    });
    // Tambahkan efek transisi saat hover pada elemen dengan kelas emoji-hover
    document.querySelectorAll('.emoji-hover').forEach(el => {
      el.addEventListener('mouseenter', function() {
        this.style.transition = 'all 0.2s ease'; // Tetapkan transisi saat hover
      });
    });
  </script>
</body>
</html>
