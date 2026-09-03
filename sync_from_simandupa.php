<?php
// sync_from_simandupa.php
define('SYNC_TOKEN', '9c9334171c09a77aa76826e250eafb9eaafaae8db10d3460');
define('SIMANDUPA_URL', 'https://simandupa.man2plg.sch.id');
define('PAGE_SECRET', 'rahasia-buka-via-browser'); // pengaman kalau dibuka lewat browser

if (php_sapi_name() !== 'cli' && ($_GET['secret'] ?? '') !== PAGE_SECRET) {
    http_response_code(403); die('Akses ditolak');
}

$db = new mysqli('localhost', 'USER_DB_SPH', 'PASS_DB_SPH', 'sph');
if ($db->connect_error) die('DB error: ' . $db->connect_error);
$db->set_charset('utf8mb4');

function httpGet($url) {
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['X-Sync-Token: ' . SYNC_TOKEN],
        CURLOPT_TIMEOUT => 120,
    ]);
    $out = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) return ['success' => false, 'message' => $err];
    return json_decode($out, true);
}

echo "=== SYNC SIMANDUPA -> SPH ===\n";

// Tabel penampung absensi (dibuat otomatis sekali)
$db->query("CREATE TABLE IF NOT EXISTS absensi_simandupa (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nisn VARCHAR(20) NOT NULL,
  nama VARCHAR(120), kelas VARCHAR(10),
  tanggal DATE NOT NULL, jam TIME, status VARCHAR(20),
  UNIQUE KEY uq_absen (nisn, tanggal, status),
  KEY idx_tanggal (tanggal)
) ENGINE=InnoDB DEFAULT CHARSET=latin1");

// 1) SYNC SISWA
$r = httpGet(SIMANDUPA_URL . '/api/sync/students');
if (!empty($r['success'])) {
    $ok = 0;
    $stmt = $db->prepare("INSERT INTO students (nisn, nama, kelas) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE nama = VALUES(nama), kelas = VALUES(kelas)");
    foreach ($r['data'] as $s) {
        $stmt->bind_param('sss', $s['nisn'], $s['nama'], $s['kelas']);
        if ($stmt->execute()) $ok++;
    }
    echo "Siswa tersinkron: $ok / {$r['count']}\n";
} else { echo "GAGAL siswa: " . ($r['message'] ?? '?') . "\n"; }

// 2) SYNC KELAS
$r = httpGet(SIMANDUPA_URL . '/api/sync/classes');
if (!empty($r['success'])) {
    $ok = 0;
    $stmt = $db->prepare("INSERT INTO kelas (nama_kelas, wali, nip_wali) VALUES (?, ?, ?)
                          ON DUPLICATE KEY UPDATE wali = VALUES(wali), nip_wali = VALUES(nip_wali)");
    foreach ($r['data'] as $k) {
        $stmt->bind_param('sss', $k['nama_kelas'], $k['wali'], $k['nip_wali']);
        if ($stmt->execute()) $ok++;
    }
    echo "Kelas tersinkron: $ok / {$r['count']}\n";
} else { echo "GAGAL kelas: " . ($r['message'] ?? '?') . "\n"; }

// 3) SYNC ABSENSI (hari ini + kemarin)
foreach ([date('Y-m-d'), date('Y-m-d', strtotime('-1 day'))] as $tgl) {
    $r = httpGet(SIMANDUPA_URL . '/api/sync/attendance?dateFrom=' . $tgl . '&dateTo=' . $tgl);
    if (!empty($r['success'])) {
        $ok = 0;
        $stmt = $db->prepare("INSERT IGNORE INTO absensi_simandupa (nisn, nama, kelas, tanggal, jam, status) VALUES (?, ?, ?, ?, ?, ?)");
        foreach ($r['data'] as $a) {
            $stmt->bind_param('ssssss', $a['nisn'], $a['nama'], $a['kelas'], $a['tanggal'], $a['jam'], $a['status']);
            if ($stmt->execute()) $ok++;
        }
        echo "Absensi $tgl: $ok / {$r['count']}\n";
    } else { echo "GAGAL absensi $tgl: " . ($r['message'] ?? '?') . "\n"; }
}

$db->close();
echo "=== SELESAI ===\n";