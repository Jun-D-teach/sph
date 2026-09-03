CREATE TABLE settings (
  id INT PRIMARY KEY DEFAULT 1,
  sekolah VARCHAR(120), ta VARCHAR(20), smt VARCHAR(10),
  kkm INT DEFAULT 75, kepala VARCHAR(120), nip_kepala VARCHAR(60)
);
INSERT INTO settings (id,sekolah,ta,smt,kkm) VALUES (1,'MAN 2 PALEMBANG','2025/2026','Ganjil',85);

CREATE TABLE students (
  nisn VARCHAR(20) PRIMARY KEY,
  nama VARCHAR(120), kelas VARCHAR(10),
  INDEX(kelas)
);

CREATE TABLE exams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  mapel VARCHAR(60), kelas VARCHAR(10), smt VARCHAR(10), tanggal VARCHAR(20),
  bentuk VARCHAR(20), jumlah_soal INT, kkm INT, skor_per_soal DECIMAL(6,2),
  kunci VARCHAR(120), guru VARCHAR(120), nip_guru VARCHAR(60),
  wali VARCHAR(120), nip_wali VARCHAR(60), mode VARCHAR(10) NULL
);

CREATE TABLE results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  exam_id INT, nisn VARCHAR(20), nama VARCHAR(120), kelas VARCHAR(10),
  jawaban VARCHAR(120), detail VARCHAR(120), benar INT, skor INT, status VARCHAR(12),
  UNIQUE KEY uq (exam_id,nisn)
);

CREATE TABLE keterampilan (
  exam_id INT, nisn VARCHAR(20), p1 INT NULL, p2 INT NULL, p3 INT NULL,
  PRIMARY KEY (exam_id,nisn)
);