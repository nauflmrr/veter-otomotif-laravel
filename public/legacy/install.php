<?php
// Jalankan sekali di browser: /install.php lalu HAPUS file ini.
require 'config.php';
$pdo = new PDO('mysql:host='.DB_HOST.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS `'.DB_NAME.'` CHARACTER SET utf8mb4');
$pdo->exec('USE `'.DB_NAME.'`');
foreach (array_filter(array_map('trim', explode(';', file_get_contents(__DIR__.'/database.sql')))) as $q) $pdo->exec($q);
if (!$pdo->query("SELECT COUNT(*) FROM users")->fetchColumn()) {
  $pdo->prepare("INSERT INTO users(nama,email,password,role) VALUES('Admin Dealer','admin@veter.test',?, 'admin')")->execute([password_hash('admin123', PASSWORD_DEFAULT)]);
  $m = [['Toyota','Avanza 1.5 G',2026,255000000,'Manual','Bensin','Putih',5,'MPV keluarga 7 penumpang yang irit dan nyaman.'],
        ['Honda','Brio RS',2026,239000000,'Automatic','Bensin','Merah',3,'City car lincah untuk lalu lintas Jakarta.'],
        ['Mitsubishi','Xpander Ultimate',2026,330000000,'Automatic','Bensin','Abu-abu',4,'MPV bergaya SUV dengan kabin lega.'],
        ['Suzuki','Jimny 5-Door',2026,445000000,'Automatic','Bensin','Hijau',2,'SUV kompak 4x4 untuk petualangan.']];
  $s = $pdo->prepare("INSERT INTO mobil(merk,tipe,tahun,harga,transmisi,bbm,warna,stok,deskripsi) VALUES(?,?,?,?,?,?,?,?,?)");
  foreach ($m as $r) $s->execute($r);
  $pdo->exec("INSERT INTO cabang(nama,alamat) VALUES('Veter Kebon Nanas (Pusat)','Jl Hasan No 98, Kebon Nanas, Jakarta Barat'),('Veter Kemayoran','Jl Kemayoran Baru No 12, Jakarta Pusat')");
  $pdo->exec("UPDATE mobil SET cabang_id = IF(id%2=0,2,1)");
  $pdo->exec("INSERT INTO sales(nama,telp,cabang_id) VALUES('Rizky Pratama','0812-0000-0001',1),('Dewi Lestari','0812-0000-0002',1),('Andi Saputra','0812-0000-0003',2)");
  $pdo->exec("INSERT INTO suku_cadang(nama,kategori,harga,stok) VALUES('Oli Mesin 4L','Pelumas',380000,40),('Filter Oli','Filter',65000,60),('Kampas Rem Depan','Rem',320000,25),('Aki 45Ah','Kelistrikan',950000,12),('Busi (set)','Mesin',210000,30)");
}
echo 'Instalasi selesai. Login admin: admin@veter.test / admin123 (segera ganti) dan hapus install.php.';
