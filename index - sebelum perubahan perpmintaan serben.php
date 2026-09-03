<?php
session_start();
require __DIR__.'/config.php';
define('LOGO_URL','https://man2plg.sch.id/Foto/logo.png');
function logoImg($s=70){return '<img src="'.LOGO_URL.'" alt="Logo" style="width:'.$s.'px;height:'.$s.'px;object-fit:contain;display:block;margin:0 auto 6px">';}
function e($s){return htmlspecialchars((string)$s,ENT_QUOTES,'UTF-8');}
function normKelas($k){$s=strtoupper(trim((string)$k));$s=preg_replace('/\s+/','',$s);$s=str_replace('.','',$s);$s=str_replace('XLL','XII',$s);$s=preg_replace('/^12/','XII',$s);$s=preg_replace('/^11/','XI',$s);$s=preg_replace('/^10/','X',$s);return preg_match('/^([XIV]+)(\d+[A-Z]?)$/',$s,$m)?$m[1].'.'.$m[2]:$s;}
function kelasRank($k){$rom=['I'=>1,'II'=>2,'III'=>3,'IV'=>4,'V'=>5,'VI'=>6,'VII'=>7,'VIII'=>8,'IX'=>9,'X'=>10,'XI'=>11,'XII'=>12];if(preg_match('/^([XIV]+)\.(\d+[A-Z]?)$/',$k,$m)){$r=$rom[$m[1]]??50;return $r*1000+(int)$m[2];}return 99999;}
function predikat($c,$n){$q=$c->query("SELECT * FROM kkm_range ORDER BY min DESC");while($x=$q->fetch_assoc()){if($n>=(int)$x['min']&&$n<=(int)$x['max'])return $x['predikat'];}return 'D';}
function rentangTable($c){$h='<table style="width:auto"><tr><th>Rentang Nilai</th><th>Predikat</th><th>Keterangan</th></tr>';$q=$c->query("SELECT * FROM kkm_range ORDER BY min DESC");while($x=$q->fetch_assoc())$h.='<tr><td class="c">'.$x['min'].' — '.$x['max'].'</td><td class="c">'.e($x['predikat']).'</td><td>'.e($x['keterangan']).'</td></tr>';return $h.'</table>';}
function deskripsiOf($r,$ex,$c){$n=$ex['jumlah_soal'];
 $ind=[];$q=$c->query("SELECT no_soal,indikator FROM indikator WHERE exam_id=".$ex['id']." ORDER BY no_soal");while($x=$q->fetch_assoc())$ind[(int)$x['no_soal']]=strtolower(trim($x['indikator']));
 $det=(string)$r['detail'];$len=strlen($det);
 $tuntas=$r['skor']>=$ex['kkm'];
 if(!$ind){return $tuntas?'Ananda mencapai ketuntasan; pertahankan dan tingkatkan prestasi.':'Ananda belum tuntas; perlu bimbingan ulang pada materi ini. (Catatan: guru belum mengisi indikator soal pada lembar koreksi ujian ini.)';}
 if($len!==$n){$all=implode('; ',array_values($ind));
   return $tuntas?'Ananda tuntas; pertahankan penguasaan materi ('.$all.').':'Ananda belum tuntas; perlu bimbingan ulang pada indikator: '.$all.'.';}
 $mampu=[];$belum=[];$noBelum=[];
 for($i=0;$i<$n;$i++){$tx=$ind[$i+1]??'';if($tx==='')continue;if($det[$i]==='1'){if(!in_array($tx,$mampu))$mampu[]=$tx;}else{if(!in_array($tx,$belum)){$belum[]=$tx;$noBelum[]=$i+1;}}}
 if(!$mampu&&!$belum){return $tuntas?'Ananda tuntas; pertahankan.':'Ananda belum tuntas; perlu bimbingan ulang pada indikator: '.implode('; ',array_values($ind)).'.';}
 if(!$belum)return 'Ananda mampu menguasai seluruh indikator ('.implode('; ',$mampu).') dengan sangat baik.';
 if(!$mampu)return 'Ananda perlu bimbingan intensif pada indikator: '.implode('; ',$belum).' (soal no. '.implode(', ',$noBelum).').';
 return 'Ananda mampu '.implode('; ',$mampu).'; namun perlu bimbingan pada '.implode('; ',$belum).' (soal no. '.implode(', ',$noBelum).').';}
function getResults($c,$eid){$r=[];$q=$c->query("SELECT * FROM results WHERE exam_id=$eid ORDER BY kelas,nama");while($x=$q->fetch_assoc())$r[]=$x;return $r;}
function statsOf($res,$n,$kkm){$cnt=count($res);$sum=0;$t=0;$perQ=array_fill(0,$n,0);foreach($res as $r){$sum+=$r['skor'];if($r['skor']>=$kkm)$t++;if(strlen($r['detail'])==$n)for($i=0;$i<$n;$i++)if($r['detail'][$i]==='1')$perQ[$i]++;}return['n'=>$cnt,'rata'=>$cnt?$sum/$cnt:0,'kk'=>$cnt?$t/$cnt*100:0,'tuntas'=>$t,'perQ'=>$perQ];}
function rhead($ex,$set,$judul){return '<div class="rhead">'.logoImg().'<div>'.$judul.'</div><div class="pink">'.e($set['sekolah']).'</div><div>TAHUN PELAJARAN '.e($set['ta']).'</div><div style="font-weight:normal;font-size:12px;margin-top:6px">Mapel: <b>'.e($ex['mapel']).'</b> | Kelas/Smt: <b>'.e($ex['kelas']).' / '.e($ex['smt']).'</b> | Tanggal: <b>'.e($ex['tanggal']).'</b> | Moda: <b>'.e($ex['mode']).'</b> | KKM: <b>'.$ex['kkm'].'</b></div></div>';}
function tglIndo($d){if(!$d)return '.......................';$p=explode('-',$d);if(count($p)!==3)return e($d);$bl=['Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','November','Desember'];$m=(int)$p[1];return ($m>=1&&$m<=12)?((int)$p[2].' '.$bl[$m-1].' '.(int)$p[0]):e($d);}
function kop($set){return '<div style="text-align:center;border-bottom:3px double #333;padding-bottom:6px;margin-bottom:10px">'.logoImg(60).'<div style="font-weight:bold;font-size:15px">KEMENTERIAN AGAMA</div><div style="font-weight:bold;font-size:15px">'.e($set['sekolah']).'</div><div style="font-weight:bold;font-size:12px">TERAKREDITASI A</div><div style="font-size:11px">Jl. Prof.Kh.Zainal Abidin Komplek Uin Raden Fatah Palembang</div><div style="font-size:11px">www.man2palembang.sch.id • manpalembang2@kemenag.go.id • NSM 131116710002 • Telp/Fax 0711-363875 • NPSN 10508051</div></div>';}
function sigBlock($ex,$set){return '<div class="sig"><div>Wali Kelas,<br><br><br><br><b><u>'.e($ex['wali']?:'........................').'</u></b><br>NIP. '.e($ex['nip_wali']?:'-').'</div><div>Palembang, '.tglIndo($ex['tanggal']).'<br>Guru Mata Pelajaran,<br><br><br><br><b><u>'.e($ex['guru']?:'........................').'</u></b><br>NIP. '.e($ex['nip_guru']?:'-').'</div><div>Mengesahkan, Kepala Madrasah,<br><br><br><br><b><u>'.e($set['kepala']?:'........................').'</u></b><br>NIP. '.e($set['nip_kepala']?:'-').'</div></div>';}
function sigResmi($ex,$set){return '<table class="info" style="margin-top:26px"><tr><td style="width:50%;vertical-align:top">Mengetahui<br>Waka Bidang Kurikulum dan Evaluasi,<br><br><br><br><b><u>'.e($set['waka']?:'............................').'</u></b><br>NIP. '.e($set['nip_waka']?:'-').'</td><td style="width:50%;vertical-align:top">Palembang, '.tglIndo($ex['tanggal']).'<br>Guru Mata Pelajaran,<br><br><br><br><b><u>'.e($ex['guru']?:'............................').'</u></b><br>NIP. '.e($ex['nip_guru']?:'-').'</td></tr><tr><td colspan="2" style="padding-top:12px">Mengesahkan<br>Kepala Madrasah,<br><br><br><br><b><u>'.e($set['kepala']?:'............................').'</u></b><br>NIP. '.e($set['nip_kepala']?:'-').'</td></tr></table>';}
function makeUsername($c,$nama,$nip){$base=$nip!==''?$nip:preg_replace('/[^a-z0-9]/','',strtolower(trim($nama)));if($base==='')$base='guru';$u=$base;$i=1;while($c->query("SELECT id FROM users WHERE username='".$c->real_escape_string($u)."'")->fetch_assoc()){$u=$base.$i;$i++;}return $u;}
function examOptions($c,$sel,$role,$uid){$h='';$w=($role==='admin')?'':' WHERE dibuat_oleh IN (0,'.$uid.')';$q=$c->query("SELECT * FROM exams".$w." ORDER BY id DESC");while($x=$q->fetch_assoc())$h.='<option value="'.$x['id'].'" '.($x['id']==$sel?'selected':'').'>'.e($x['mapel']).' — '.e($x['kelas']).' ('.e($x['mode']?:'belum ada data').')</option>';return $h?:'<option value="">-- belum ada ujian --</option>';}

/* seed admin default */
$hasUsers=$conn->query("SHOW TABLES LIKE 'users'")->num_rows>0;
if($hasUsers){$chk=$conn->query("SELECT id FROM users LIMIT 1");if($chk&&!$chk->fetch_assoc()){$h=password_hash('admin123',PASSWORD_DEFAULT);$conn->query("INSERT INTO users (username,pass_hash,pass_plain,nama,role) VALUES ('admin','".$conn->real_escape_string($h)."','admin123','Administrator','admin')");}}

$set=$conn->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();
$p=$_GET['p']??'home';$msg='';$rejected=[];$impInfo='';$loginErr='';
$logged=isset($_SESSION['uid']);$role=$logged?($_SESSION['role']??'guru'):'guru';$uid=$logged?(int)$_SESSION['uid']:0;
$isAdmin=($role==='admin');

/* ================= POST ACTIONS ================= */
if($_SERVER['REQUEST_METHOD']==='POST'){$act=$_POST['action']??'';
 if($act=='login'&&!$logged){$u=$conn->query("SELECT * FROM users WHERE username='".$conn->real_escape_string($_POST['username']??'')."'")->fetch_assoc();if($u&&password_verify($_POST['password']??'',$u['pass_hash'])){$_SESSION['uid']=$u['id'];$_SESSION['role']=$u['role'];$_SESSION['nama']=$u['nama'];$_SESSION['nip']=$u['nip']??'';header('Location: ?p=home');exit;}else{$loginErr='Username atau password salah!';}}
 elseif($act=='logout'){session_destroy();header('Location: ?p=home');exit;}
 elseif($logged){
  if($act=='change_pass'){$u=$conn->query("SELECT * FROM users WHERE id=$uid")->fetch_assoc();if($u&&password_verify($_POST['old']??'',$u['pass_hash'])){$h=password_hash($_POST['new'],PASSWORD_DEFAULT);$conn->query("UPDATE users SET pass_hash='".$conn->real_escape_string($h)."',pass_plain='".$conn->real_escape_string($_POST['new'])."' WHERE id=$uid");$msg='Password Anda diganti.';}else{$msg='Password lama salah!';}}
  if($act=='save_exam'){$n=(int)$_POST['jumlah_soal'];$k='';for($i=0;$i<$n;$i++)$k.=strtoupper(trim($_POST['k'][$i]??''));$skor=(float)($_POST['skor']?:(100/$n));
   $editId=(int)($_POST['edit_id']??0);$examIdFinal=0;
   $setsql="mapel='".$conn->real_escape_string($_POST['mapel'])."',kelas='".$conn->real_escape_string(normKelas($_POST['kelas']))."',smt='".$conn->real_escape_string($_POST['smt'])."',tanggal='".$conn->real_escape_string($_POST['tanggal'])."',bentuk='".$conn->real_escape_string($_POST['bentuk'])."',jumlah_soal=$n,kkm=".(int)$_POST['kkm'].",skor_per_soal=$skor,kunci='".$conn->real_escape_string($k)."',guru='".$conn->real_escape_string($_POST['guru'])."',nip_guru='".$conn->real_escape_string($_POST['nip_guru'])."',wali='".$conn->real_escape_string($_POST['wali'])."',nip_wali='".$conn->real_escape_string($_POST['nip_wali'])."'";
   if($editId>0){$ex0=$conn->query("SELECT * FROM exams WHERE id=$editId")->fetch_assoc();if($ex0&&($isAdmin||(int)($ex0['dibuat_oleh']??0)===$uid||(int)($ex0['dibuat_oleh']??0)===0)){$conn->query("UPDATE exams SET $setsql WHERE id=$editId");$examIdFinal=$editId;$msg='Ujian diperbarui.';}}
   else{$conn->query("INSERT INTO exams SET $setsql,dibuat_oleh=$uid");$examIdFinal=(int)$conn->insert_id;$msg='Ujian tersimpan. Lanjut ke menu 📥 IMPORT (CBT).';}
   if($examIdFinal>0){foreach(($_POST['ind']??[]) as $no=>$txt){$no=(int)$no;$txt=$conn->real_escape_string(strtolower(trim($txt)));if($txt===''){$conn->query("DELETE FROM indikator WHERE exam_id=$examIdFinal AND no_soal=$no");}else{$conn->query("REPLACE INTO indikator (exam_id,no_soal,indikator) VALUES ($examIdFinal,$no,'$txt')");}}}}
  elseif($act=='del_exam'){$eid=(int)$_POST['exam_id'];$ex=$conn->query("SELECT * FROM exams WHERE id=$eid")->fetch_assoc();if($ex&&($isAdmin||(int)$ex['dibuat_oleh']===$uid)){$conn->query("DELETE FROM results WHERE exam_id=$eid");$conn->query("DELETE FROM keterampilan WHERE exam_id=$eid");$conn->query("DELETE FROM indikator WHERE exam_id=$eid");$conn->query("DELETE FROM exams WHERE id=$eid");$msg='Ujian dihapus';}}
  elseif($act=='import_cbt'){$eid=(int)$_POST['exam_id'];$ex=$conn->query("SELECT * FROM exams WHERE id=$eid")->fetch_assoc();
    if($ex&&($isAdmin||(int)$ex['dibuat_oleh']===0||(int)$ex['dibuat_oleh']===$uid)){$n=(int)$ex['jumlah_soal'];$kunci=str_split($ex['kunci']);
    $lines=array_values(array_filter(array_map('trim',preg_split('/\r?\n/',$_POST['paste']??'')),'strlen'));
    $rows=[];foreach($lines as $l)$rows[]=explode("\t",$l);$start=0;
    if(isset($rows[0])&&(stripos($rows[0][0]??'','timestamp')!==false||stripos($rows[0][1]??'','nama')!==false))$start=1;
    $parsed=[];for($i=$start;$i<count($rows);$i++){$r=$rows[$i];$nisn=trim($r[2]??'');$nama=trim($r[1]??'');if(!$nisn||$nisn==='.'||!$nama||$nama==='.')continue;$ans=[];foreach($r as $cc){$cc=strtoupper(trim($cc));if(preg_match('/^[A-E]$/',$cc))$ans[]=$cc;}$parsed[]=['ts'=>trim($r[0]??''),'nama'=>$nama,'nisn'=>$nisn,'kelas'=>trim($r[3]??''),'ans'=>array_slice($ans,0,$n),'seq'=>$i];}
    $byN=[];foreach($parsed as $r){$t=strtotime($r['ts'])?:$r['seq'];if(!isset($byN[$r['nisn']])||$t>=$byN[$r['nisn']]['t']){$r['t']=$t;$byN[$r['nisn']]=$r;}}
    $auto=isset($_POST['auto_add']);$valid=[];$dup=count($parsed)-count($byN);
    foreach($byN as $r){$st=$conn->query("SELECT * FROM students WHERE nisn='".$conn->real_escape_string($r['nisn'])."'")->fetch_assoc();
      if($st)$valid[]=['nisn'=>$st['nisn'],'nama'=>$st['nama'],'kelas'=>$st['kelas'],'ans'=>$r['ans']];
      elseif($auto){$kk=normKelas($r['kelas']);$conn->query("INSERT INTO students (nisn,nama,kelas) VALUES ('".$conn->real_escape_string($r['nisn'])."','".$conn->real_escape_string($r['nama'])."','".$conn->real_escape_string($kk)."') ON DUPLICATE KEY UPDATE nama=VALUES(nama)");$valid[]=['nisn'=>$r['nisn'],'nama'=>$r['nama'],'kelas'=>$kk,'ans'=>$r['ans']];}
      else $rejected[]=$r;}
    usort($valid,function($a,$b){return strcmp($a['kelas'].$a['nama'],$b['kelas'].$b['nama']);});
    $conn->query("DELETE FROM results WHERE exam_id=$eid");
    foreach($valid as $v){$b=0;$det='';for($i=0;$i<$n;$i++){$ok=isset($v['ans'][$i])&&$v['ans'][$i]===$kunci[$i];$det.=$ok?'1':'0';if($ok)$b++;}$skor=(int)round($b*(float)$ex['skor_per_soal']);$status=$skor>=$ex['kkm']?'TUNTAS':'REMEDIAL';
      $conn->query("INSERT INTO results (exam_id,nisn,nama,kelas,jawaban,detail,benar,skor,status) VALUES ($eid,'".$conn->real_escape_string($v['nisn'])."','".$conn->real_escape_string($v['nama'])."','".$conn->real_escape_string($v['kelas'])."','".$conn->real_escape_string(implode('',$v['ans']))."','$det',$b,$skor,'$status')");}
    $conn->query("UPDATE exams SET mode='CBT' WHERE id=$eid");
    $impInfo="Baris terbaca: ".count($parsed)." • Duplikat dibuang: $dup • Peserta valid: ".count($valid)." • Ditolak: ".count($rejected);}}
  elseif($act=='save_manual'){$eid=(int)$_POST['exam_id'];$ex=$conn->query("SELECT * FROM exams WHERE id=$eid")->fetch_assoc();
    if($ex){$n=(int)$ex['jumlah_soal'];$kunci=str_split($ex['kunci']);$conn->query("DELETE FROM results WHERE exam_id=$eid");
    $q=$conn->query("SELECT * FROM students WHERE kelas='".$conn->real_escape_string($ex['kelas'])."' ORDER BY nama");
    while($s=$q->fetch_assoc()){$skor=0;$det='';$b=0;
      if(($_POST['mode']??'')=='nilai'){$skor=(int)($_POST['nilai'][$s['nisn']]??0);$b=(int)round($skor/(float)$ex['skor_per_soal']);}
      else{for($j=0;$j<$n;$j++){$v=strtoupper(trim($_POST['huruf'][$s['nisn']][$j]??''));$ok=$v===$kunci[$j];$det.=$ok?'1':'0';if($ok)$b++;}$skor=(int)round($b*(float)$ex['skor_per_soal']);}
      $status=$skor>=$ex['kkm']?'TUNTAS':'REMEDIAL';
      $conn->query("INSERT INTO results (exam_id,nisn,nama,kelas,jawaban,detail,benar,skor,status) VALUES ($eid,'".$conn->real_escape_string($s['nisn'])."','".$conn->real_escape_string($s['nama'])."','".$conn->real_escape_string($s['kelas'])."','','$det',$b,$skor,'$status')");}
    $conn->query("UPDATE exams SET mode='NON CBT' WHERE id=$eid");$msg='Data NON CBT tersimpan & terkoreksi.';}}
    elseif($act=='save_ket'){$eid=(int)$_POST['exam_id'];foreach($_POST['kt'] as $nisn=>$v){$vals=[];for($x=0;$x<5;$x++)$vals[]=(isset($v[$x])&&$v[$x]!=='')?(int)$v[$x]:'NULL';$conn->query("REPLACE INTO keterampilan (exam_id,nisn,p1,p2,p3,p4,p5) VALUES ($eid,'".$conn->real_escape_string($nisn)."',".implode(',',$vals).")");}$msg='Nilai keterampilan tersimpan.';}
    elseif($act=='save_tl'){$eid=(int)$_POST['exam_id'];foreach($_POST['tl'] as $nisn=>$v){$na=trim((string)($v['akhir']??''));$bentuk=$conn->real_escape_string($v['bentuk']??'');$naSql=$na!==''?(int)$na:'NULL';$conn->query("REPLACE INTO tindak_lanjut (exam_id,nisn,nilai_akhir,bentuk) VALUES ($eid,'".$conn->real_escape_string($nisn)."',$naSql,'$bentuk')");}$msg='Data remedial/pengayaan tersimpan.';}
  /* ===== KHUSUS ADMIN ===== */
  if($isAdmin){
      if($act=='save_settings'){$conn->query("UPDATE settings SET sekolah='".$conn->real_escape_string($_POST['sekolah'])."',ta='".$conn->real_escape_string($_POST['ta'])."',smt='".$conn->real_escape_string($_POST['smt'])."',kkm=".(int)$_POST['kkm'].",kepala='".$conn->real_escape_string($_POST['kepala'])."',nip_kepala='".$conn->real_escape_string($_POST['nip_kepala'])."',waka='".$conn->real_escape_string($_POST['waka']??'')."',nip_waka='".$conn->real_escape_string($_POST['nip_waka']??'')."'");$set=$conn->query("SELECT * FROM settings LIMIT 1")->fetch_assoc();$msg='Pengaturan tersimpan.';}
   elseif($act=='add_siswa'){$n=$conn->real_escape_string(trim($_POST['nisn']));$m=$conn->real_escape_string(trim($_POST['nama']));$k=$conn->real_escape_string(normKelas($_POST['kelas']));$conn->query("INSERT INTO students (nisn,nama,kelas) VALUES ('$n','$m','$k') ON DUPLICATE KEY UPDATE nama='$m',kelas='$k'");$msg='Siswa tersimpan.';}
      elseif($act=='import_siswa'){$c=0;$map=null;foreach(array_filter(array_map('trim',preg_split('/\r?\n/',$_POST['paste_siswa'])),'strlen') as $l){$r=explode("\t",$l);
     if($map===null){$low=array_map(function($x){return strtolower(trim($x));},$r);$join=implode(' ',$low);
       if(strpos($join,'nisn')!==false||strpos($join,'nama')!==false||strpos($join,'kelas')!==false){$map=['nisn'=>0,'nama'=>1,'kelas'=>2];foreach($low as $i=>$h){if(strpos($h,'nisn')!==false)$map['nisn']=$i;elseif(strpos($h,'nama')!==false)$map['nama']=$i;elseif(strpos($h,'kelas')!==false||strpos($h,'rombel')!==false)$map['kelas']=$i;}continue;}
       else{$map=['nisn'=>0,'nama'=>1,'kelas'=>2];}}
     $nisn=trim($r[$map['nisn']]??'');$nama=trim($r[$map['nama']]??'');$kelas=normKelas(trim($r[$map['kelas']]??''));
     if($nisn!==''&&!preg_match('/^\d+$/',$nisn)&&preg_match('/^\d{8,}$/',$nama)){$t=$nisn;$nisn=$nama;$nama=$t;}
     $nisn=$conn->real_escape_string($nisn);$nama=$conn->real_escape_string($nama);$kelas=$conn->real_escape_string($kelas);
     if(!$nisn||!$nama||$nisn==='.')continue;$conn->query("INSERT INTO students (nisn,nama,kelas) VALUES ('$nisn','$nama','$kelas') ON DUPLICATE KEY UPDATE nama='$nama',kelas='$kelas'");$c++;}$msg="$c siswa terimport.";}
   elseif($act=='del_siswa'){$conn->query("DELETE FROM students WHERE nisn='".$conn->real_escape_string($_POST['nisn'])."'");}
      elseif($act=='add_guru'||$act=='import_guru'){$cnt=0;
     if($act=='add_guru'){$rows=[[trim($_POST['nip']),trim($_POST['nama']),trim($_POST['mapel']??''),trim($_POST['username']??''),trim($_POST['password']??'')]];}
     else{$rows=[];$map=null;foreach(array_filter(array_map('trim',preg_split('/\r?\n/',$_POST['paste_guru'])),'strlen') as $l){$r=explode("\t",$l);
       if($map===null){$low=array_map(function($x){return strtolower(trim($x));},$r);$join=implode(' ',$low);
         if(strpos($join,'nip')!==false||strpos($join,'nama')!==false||strpos($join,'mapel')!==false||strpos($join,'pelajaran')!==false){$map=['nip'=>0,'nama'=>1,'mapel'=>2,'username'=>3,'password'=>4];foreach($low as $i=>$h){if(strpos($h,'nip')!==false)$map['nip']=$i;elseif(strpos($h,'nama')!==false)$map['nama']=$i;elseif(strpos($h,'mapel')!==false||strpos($h,'pelajaran')!==false)$map['mapel']=$i;elseif(strpos($h,'user')!==false)$map['username']=$i;elseif(strpos($h,'pass')!==false)$map['password']=$i;}continue;}
         else{$map=['nip'=>0,'nama'=>1,'mapel'=>2,'username'=>3,'password'=>4];}}
       $nip=trim($r[$map['nip']]??'');$nama=trim($r[$map['nama']]??'');
       if($nip!==''&&!preg_match('/^\d+$/',$nip)&&preg_match('/^\d{10,}$/',$nama)){$t=$nip;$nip=$nama;$nama=$t;}
       $rows[]=[$nip,$nama,trim($r[$map['mapel']]??''),trim($r[$map['username']]??''),trim($r[$map['password']]??'')];}}
     foreach($rows as $r){list($nip,$nama,$mapel,$user,$pass)=$r;if(!$nip||!$nama)continue;if($pass==='')$pass='guru123';if($user==='')$user=makeUsername($conn,$nama,$nip);$h=password_hash($pass,PASSWORD_DEFAULT);
       $conn->query("INSERT INTO users (username,pass_hash,pass_plain,nama,nip,mapel,role) VALUES ('".$conn->real_escape_string($user)."','".$conn->real_escape_string($h)."','".$conn->real_escape_string($pass)."','".$conn->real_escape_string($nama)."','".$conn->real_escape_string($nip)."','".$conn->real_escape_string($mapel)."','guru') ON DUPLICATE KEY UPDATE pass_hash=VALUES(pass_hash),pass_plain=VALUES(pass_plain),nama=VALUES(nama),mapel=VALUES(mapel)");$cnt++;}
     $msg="$cnt akun guru tersimpan.";}
   elseif($act=='del_guru'){$conn->query("DELETE FROM users WHERE id=".(int)$_POST['uid']." AND role='guru'");$msg='Akun guru dihapus.';}
   elseif($act=='reset_pass'){$np=$_POST['newpass']?:'guru123';$h=password_hash($np,PASSWORD_DEFAULT);$conn->query("UPDATE users SET pass_hash='".$conn->real_escape_string($h)."',pass_plain='".$conn->real_escape_string($np)."' WHERE id=".(int)$_POST['uid']." AND role='guru'");$msg='Password guru direset.';}
   elseif($act=='save_kelas'){$nk=$conn->real_escape_string(normKelas($_POST['nama_kelas']));$w=$conn->real_escape_string($_POST['wali']);$u=$conn->query("SELECT nip FROM users WHERE nama='".$w."' AND role='guru'")->fetch_assoc();$nip=$u?$conn->real_escape_string($u['nip']):'';$conn->query("INSERT INTO kelas (nama_kelas,wali,nip_wali) VALUES ('$nk','$w','$nip') ON DUPLICATE KEY UPDATE wali='$w',nip_wali='$nip'");$msg='Data kelas & wali tersimpan.';}
   elseif($act=='del_kelas'){$conn->query("DELETE FROM kelas WHERE id=".(int)$_POST['kid']);$msg='Kelas dihapus.';}
   elseif($act=='save_kkm'){$conn->query("DELETE FROM kkm_range");$mn=$_POST['mn']??[];foreach($mn as $i=>$a){if($a===''||($_POST['mx'][$i]??'')==='')continue;$conn->query("INSERT INTO kkm_range (min,max,predikat,keterangan) VALUES (".(int)$a.",".(int)($_POST['mx'][$i]??0).",'".$conn->real_escape_string($_POST['pr'][$i]??'')."','".$conn->real_escape_string($_POST['kt'][$i]??'')."')");}$msg='Rentang predikat tersimpan.';}
  }
 }
}

/* ================= TEMPLATE & BACKUP & CSV ================= */
if($logged&&$isAdmin&&isset($_GET['tpl'])){$t=$_GET['tpl'];header('Content-Type:text/csv');header('Content-Disposition:attachment;filename="template_'.$t.'.csv"');echo "\xEF\xBB\xBF";if($t==='siswa')echo "NISN;NAMA;KELAS\n0089526958;zahara pebriyanti;XII.3\n";else echo "NIP;NAMA;MAPEL;USERNAME;PASSWORD\n198001012005011001;MARLAYLI S.Pd;Fiqih;;guru123\n";exit;}
if($logged&&$isAdmin&&isset($_GET['backup'])){header('Content-Type:application/json');header('Content-Disposition:attachment;filename="BACKUP_SPH_'.date('Ymd_His').'.json"');$data=['settings'=>$set,'students'=>[],'users'=>[],'exams'=>[],'results'=>[]];$q=$conn->query("SELECT * FROM students");while($x=$q->fetch_assoc())$data['students'][]=$x;$q=$conn->query("SELECT id,username,pass_plain,nama,nip,mapel,role FROM users");while($x=$q->fetch_assoc())$data['users'][]=$x;$q=$conn->query("SELECT * FROM exams");while($x=$q->fetch_assoc())$data['exams'][]=$x;$q=$conn->query("SELECT * FROM results");while($x=$q->fetch_assoc())$data['results'][]=$x;echo json_encode($data);exit;}
if($logged&&$p==='csv'){$eid=(int)($_GET['exam']??0);$ex=$conn->query("SELECT * FROM exams WHERE id=$eid")->fetch_assoc();header('Content-Type:text/csv');header('Content-Disposition:attachment;filename="SPH_'.$ex['mapel'].'_'.$ex['kelas'].'.csv"');$o=fopen('php://output','w');fputcsv($o,['No','NISN','Nama','Kelas','Benar','Nilai','Predikat','Status']);foreach(getResults($conn,$eid) as $i=>$r)fputcsv($o,[$i+1,$r['nisn'],$r['nama'],$r['kelas'],$r['benar'],$r['skor'],predikat($conn,$r['skor']),$r['status']]);exit;}

/* ================= LOAD UJIAN TERPILIH ================= */
$examSel=null;$eid=(int)($_GET['exam']??$_POST['exam_id']??0);
if($logged&&$eid){$ex=$conn->query("SELECT * FROM exams WHERE id=$eid")->fetch_assoc();if($ex&&($isAdmin||in_array((int)($ex['dibuat_oleh']??0),[0,$uid])))$examSel=$ex;}
if($logged&&!$isAdmin&&in_array($p,['master','guru','kelas','kkm'])){$p='home';$msg='Halaman khusus Admin.';}

/* ================= HALAMAN LOGIN ================= */
if(!$logged){?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Login SPH</title><link rel="icon" type="image/png" href="https://man2plg.sch.id/Foto/logo.png">
<style>body{background:#1a3e72;font-family:'Segoe UI',Arial;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{background:#fff;border-radius:12px;padding:34px;width:340px;box-shadow:0 8px 30px rgba(0,0,0,.4)}h1{font-size:18px;color:#1a3e72;text-align:center;margin:0 0 4px}p.sub{text-align:center;font-size:12px;color:#666;margin:0 0 18px}label{font-size:12px;font-weight:600;display:block;margin-top:10px}input{width:100%;padding:10px;border:1px solid #bbb;border-radius:6px;margin-top:3px;font-size:14px}button{width:100%;margin-top:16px;padding:11px;border:none;border-radius:6px;background:#1a3e72;color:#fff;font-weight:bold;font-size:14px;cursor:pointer}.err{background:#fddede;border:1px solid #d33;border-radius:6px;padding:8px;font-size:12px;margin-top:10px}.hint{font-size:11px;color:#888;text-align:center;margin-top:14px}</style></head>
<body><form class="box" method="post"><img src="https://man2plg.sch.id/Foto/logo.png" alt="" style="width:70px;height:70px;object-fit:contain;display:block;margin:0 auto 8px"><h1>📘 APLIKASI SPH</h1><p class="sub"><?=e($set['sekolah'])?> • TA <?=e($set['ta'])?></p>
<label>Username</label><input name="username" required autofocus>
<label>Password</label><input type="password" name="password" required>
<input type="hidden" name="action" value="login">
<button>MASUK</button>
<?php if($loginErr)echo '<div class="err">'.e($loginErr).'</div>'; if(!$hasUsers)echo '<div class="err">Tabel users belum ada — jalankan <b>update_login.sql</b> di phpMyAdmin.</div>';?>
<div class="hint">Default admin: <b>admin / admin123</b></div></form></body></html>
<?php exit;} ?>
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>SPH - <?=e($set['sekolah'])?></title><link rel="icon" type="image/png" href="https://man2plg.sch.id/Foto/logo.png">
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<style>
*{box-sizing:border-box;margin:0;padding:0;font-family:'Segoe UI',Arial,sans-serif}body{background:#eef2f7;color:#222}
header{background:#1a3e72;color:#fff;padding:12px 20px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap}header h1{font-size:17px}header small{opacity:.85}
nav{background:#16325c;display:flex;flex-wrap:wrap}nav a{color:#fff;padding:12px 16px;text-decoration:none;font-size:13px}nav a:hover,nav a.active{background:#0d2547;font-weight:bold}
main{padding:20px;max-width:1200px;margin:auto}.card{background:#fff;border-radius:10px;padding:20px;margin-bottom:16px;box-shadow:0 1px 4px rgba(0,0,0,.12)}
h2{margin-bottom:12px;color:#1a3e72;font-size:17px}h3{margin:10px 0 6px;font-size:14px}
label{display:block;font-size:12px;margin-top:8px;font-weight:600}input,select,textarea{width:100%;padding:7px;border:1px solid #bbb;border-radius:5px;font-size:13px;margin-top:2px}
textarea{font-family:Consolas,monospace;font-size:12px}.row{display:flex;gap:12px;flex-wrap:wrap}.row>div{flex:1;min-width:160px}
.btn{margin-top:12px;padding:9px 16px;border:none;border-radius:6px;background:#1a3e72;color:#fff;cursor:pointer;font-size:13px;font-weight:600;text-decoration:none;display:inline-block}.btn:hover{background:#0d2547}.btn.red{background:#b03030}.btn.green{background:#2e7d32}.btn.gray{background:#666}
table{width:100%;border-collapse:collapse;font-size:12px;margin-top:10px;background:#fff}th,td{border:1px solid #999;padding:5px 7px;text-align:left;vertical-align:top}th{background:#a9c46c}td.c,th.c{text-align:center}
table.info td{border:none;font-size:12px}
.grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(210px,1fr));gap:12px}.gbtn{padding:16px 8px;border:3px solid #fff;border-radius:8px;color:#fff;font-weight:bold;text-align:center;font-size:12px;box-shadow:0 2px 5px rgba(0,0,0,.3);text-transform:uppercase;text-decoration:none;display:block}.gbtn.off{opacity:.4;pointer-events:none}
.c1{background:#8fb56a}.c2{background:#f0ee9a;color:#333}.c3{background:#b8860b}.c4{background:#3e7d3a}.c5{background:#f48fb1}.c6{background:#9575cd}.c7{background:#2e4f8f}.c9{background:#bf5b16}
.stat{display:flex;gap:12px;flex-wrap:wrap}.stat div{background:#1a3e72;color:#fff;border-radius:8px;padding:10px 18px;font-size:13px}
.note{background:#fff8c9;border:1px solid #e0c860;padding:8px;border-radius:6px;font-size:12px;margin-top:8px}.err{background:#fddede;border:1px solid #d33;padding:8px;border-radius:6px;font-size:12px;margin-top:8px}.ok{background:#e2f2e2;border:1px solid #2e7d32;padding:8px;border-radius:6px;font-size:12px;margin-top:8px}
.rhead{text-align:center;margin-bottom:10px}.rhead div{font-weight:bold}.rhead .pink{color:#e91e63}
.sig{display:flex;justify-content:space-around;margin-top:40px;font-size:12px;text-align:center}.sig div{min-width:200px}
@media print{.no-print{display:none!important}body{background:#fff}.card{box-shadow:none;border:none}main{padding:0}}
</style></head><body>
<header class="no-print"><h1><img src="<?=LOGO_URL?>" alt="" style="height:38px;vertical-align:middle;margin-right:8px">📘 APLIKASI SPH — <?=e($set['sekolah'])?> <small>• TA <?=e($set['ta'])?> • MySQL</small></h1>
<div style="font-size:12px">👤 <?=e($_SESSION['nama'])?> (<?=e($role)?>) &nbsp;<?php if($isAdmin){?><a class="btn green" style="margin:0;padding:5px 10px" href="?backup=1">⬇ Backup</a><?php }?><form method="post" style="display:inline"><input type="hidden" name="action" value="logout"><button class="btn red" style="margin:0;padding:5px 10px">Keluar</button></form></div></header>
<nav class="no-print">
<?php
$NAV=[['home','🏠 BERANDA']];
if($isAdmin){$NAV[]=['master','🗂 MASTER SISWA'];$NAV[]=['guru','👥 DATA GURU & AKUN'];$NAV[]=['kelas','🏫 KELAS & WALI'];$NAV[]=['kkm','🎯 RENTANG PREDIKAT'];}
$NAV=array_merge($NAV,[['setup','📝 LEMBAR KOREKSI'],['import','📥 IMPORT (CBT)'],['manual','✍ MANUAL (NON CBT)'],['gen','📊 GENERATE DATA']]);
foreach($NAV as $nv){echo '<a href="?p='.$nv[0].'"'.($p===$nv[0]?' class="active"':'').'>'.$nv[1].'</a>';}
?>
</nav>
<main>
<?php if($msg)echo '<div class="card no-print"><div class="ok">'.e($msg).'</div></div>'; ?>

<?php if($p=='home'){ $jS=$conn->query("SELECT COUNT(*) c FROM students")->fetch_assoc()['c']; $jE=$conn->query("SELECT COUNT(*) c FROM exams")->fetch_assoc()['c']; $jG=$conn->query("SELECT COUNT(*) c FROM users WHERE role='guru'")->fetch_assoc()['c']; ?>
<div class="card"><div style="text-align:center"><?=logoImg(90)?></div><h2 style="text-align:center">Beranda — <?=e($set['sekolah'])?></h2>
 <div class="stat"><div>👥 Siswa: <b><?=$jS?></b></div><div>👤 Akun Guru: <b><?=$jG?></b></div><div>📝 Ujian: <b><?=$jE?></b></div><div>📅 TA: <b><?=e($set['ta'])?></b></div></div></div>
<div class="card"><h2>Ganti Password Saya</h2><form method="post"><input type="hidden" name="action" value="change_pass"><div class="row"><div><label>Password Lama</label><input type="password" name="old" required></div><div><label>Password Baru</label><input type="password" name="new" required></div><div style="flex:0"><label>&nbsp;</label><button class="btn">💾 Ganti</button></div></div></form></div>
<?php if($isAdmin){?><div class="card"><h2>Pengaturan Madrasah (Admin)</h2><form method="post"><input type="hidden" name="action" value="save_settings">
 <div class="row"><div><label>Madrasah</label><input name="sekolah" value="<?=e($set['sekolah'])?>"></div><div><label>Tahun Pelajaran</label><input name="ta" value="<?=e($set['ta'])?>"></div><div><label>Semester</label><select name="smt"><option <?=$set['smt']=='Ganjil'?'selected':''?>>Ganjil</option><option <?=$set['smt']=='Genap'?'selected':''?>>Genap</option></select></div><div><label>KKM Default</label><input type="number" name="kkm" value="<?=e($set['kkm'])?>"></div></div>
 <div class="row"><div><label>Kepala Madrasah</label><input name="kepala" value="<?=e($set['kepala'])?>"></div><div><label>NIP Kepala</label><input name="nip_kepala" value="<?=e($set['nip_kepala'])?>"></div></div>
  <div class="row"><div><label>Waka Bid. Kurikulum</label><input name="waka" value="<?=e($set['waka']??'')?>"></div><div><label>NIP Waka</label><input name="nip_waka" value="<?=e($set['nip_waka']??'')?>"></div></div>
 <button class="btn">💾 Simpan</button></form></div><?php } } ?>

<?php if($p=='master'&&$isAdmin){ ?>
<div class="card"><h2>Import Siswa — upload Excel/CSV atau paste</h2>
 <p><a class="btn gray" href="?tpl=siswa">⬇ Download Template (CSV)</a></p>
 <input type="file" id="fileSiswa" accept=".xlsx,.xls,.csv" style="margin-top:8px">
 <form method="post"><input type="hidden" name="action" value="import_siswa"><textarea name="paste_siswa" id="paste_siswa" rows="5" placeholder="0089526958&#9;zahara pebriyanti&#9;XII.3"></textarea><button class="btn green">📥 Import Siswa</button></form></div>
<div class="card"><h2>Tambah Manual</h2><form method="post"><input type="hidden" name="action" value="add_siswa"><div class="row"><div><label>NISN</label><input name="nisn" required></div><div><label>Nama</label><input name="nama" required></div><div><label>Kelas</label><input name="kelas" placeholder="XII.3"></div><div style="flex:0"><label>&nbsp;</label><button class="btn">+ Tambah</button></div></div></form>
<?php $cari=$conn->real_escape_string($_GET['cari']??'');$q=$conn->query("SELECT * FROM students ".($cari?"WHERE CONCAT(nisn,nama,kelas) LIKE '%$cari%'":'')." ORDER BY kelas,nama LIMIT 500");$i=0;echo '<table><tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th class="no-print">Aksi</th></tr>';while($s=$q->fetch_assoc()){echo '<tr><td class="c">'.(++$i).'</td><td>'.e($s['nisn']).'</td><td>'.e($s['nama']).'</td><td class="c">'.e($s['kelas']).'</td><td class="no-print"><form method="post" style="margin:0"><input type="hidden" name="action" value="del_siswa"><input type="hidden" name="nisn" value="'.e($s['nisn']).'"><button class="btn red" style="margin:0;padding:3px 8px">✖</button></form></td></tr>';}echo '</table>';?></div>
<script>document.getElementById('fileSiswa').onchange=function(ev){var f=ev.target.files[0];if(!f)return;var r=new FileReader();r.onload=function(e2){var wb=XLSX.read(e2.target.result,{type:'array'});var ws=wb.Sheets[wb.SheetNames[0]];document.getElementById('paste_siswa').value=XLSX.utils.sheet_to_csv(ws,{FS:'\t'});};r.readAsArrayBuffer(f);};</script>
<?php } ?>

<?php if($p=='guru'&&$isAdmin){ ?>
<div class="card"><h2>Import Guru (NIP ⟶ NAMA  MAPEL ⟶ USERNAME  PASSWORD)</h2>
 <p><a class="btn gray" href="?tpl=guru">⬇ Download Template Guru</a></p>
 <input type="file" id="fileGuru" accept=".xlsx,.xls,.csv" style="margin-top:8px">
 <form method="post"><input type="hidden" name="action" value="import_guru"><textarea name="paste_guru" id="paste_guru" rows="4" placeholder="198001012005011001&#9;MARLAYLI S.Pd&#9;Fiqih&#9;&#9;guru123"></textarea><button class="btn green">📥 Import Guru</button></form></div>
<div class="card"><h2>Tambah Guru Manual</h2><form method="post"><input type="hidden" name="action" value="add_guru"><div class="row"><div><label>NIP</label><input name="nip" required></div><div><label>Nama</label><input name="nama" required></div><div><label>Mapel</label><input name="mapel"></div><div><label>Username (kosong=otomatis)</label><input name="username"></div><div><label>Password (kosong=guru123)</label><input name="password"></div><div style="flex:0"><label>&nbsp;</label><button class="btn">+ Tambah</button></div></div></form></div>
<div class="card"><h2>Akun Guru (lihat & reset password)</h2>
<table><tr><th>No</th><th>Nama</th><th>NIP</th><th>Mapel</th><th>Username</th><th>Password</th><th>Reset</th><th>Hapus</th></tr>
<?php $q=$conn->query("SELECT * FROM users WHERE role='guru' ORDER BY nama");$i=0;while($u=$q->fetch_assoc()){$i++;echo '<tr><td class="c">'.$i.'</td><td>'.e($u['nama']).'</td><td>'.e($u['nip']).'</td><td>'.e($u['mapel']).'</td><td>'.e($u['username']).'</td><td><input type="password" readonly id="pw'.$u['id'].'" value="'.e($u['pass_plain']).'" style="width:90px"><button type="button" class="btn gray" style="margin:0;padding:3px 8px" onclick="var el=document.getElementById(\'pw'.$u['id'].'\');el.type=el.type===\'password\'?\'text\':\'password\';">👁</button></td><td><form method="post" style="margin:0"><input type="hidden" name="action" value="reset_pass"><input type="hidden" name="uid" value="'.$u['id'].'"><input name="newpass" placeholder="baru" style="width:70px"><button class="btn" style="margin:0;padding:3px 8px">↺</button></form></td><td><form method="post" style="margin:0" onsubmit="return confirm(\'Hapus akun?\')"><input type="hidden" name="action" value="del_guru"><input type="hidden" name="uid" value="'.$u['id'].'"><button class="btn red" style="margin:0;padding:3px 8px">✖</button></form></td></tr>';}?></table></div>
<script>document.getElementById('fileGuru').onchange=function(ev){var f=ev.target.files[0];if(!f)return;var r=new FileReader();r.onload=function(e2){var wb=XLSX.read(e2.target.result,{type:'array'});var ws=wb.Sheets[wb.SheetNames[0]];document.getElementById('paste_guru').value=XLSX.utils.sheet_to_csv(ws,{FS:'\t'});};r.readAsArrayBuffer(f);};</script>
<?php } ?>

<?php if($p=='kelas'&&$isAdmin){ ?>
<div class="card"><h2>Data Kelas & Wali Kelas</h2>
<form method="post"><input type="hidden" name="action" value="save_kelas">
<div class="row"><div><label>Nama Kelas</label><input name="nama_kelas" placeholder="XII.3" required></div>
<div><label>Wali Kelas (dropdown data guru)</label><select name="wali"><?php $q=$conn->query("SELECT nama FROM users WHERE role='guru' ORDER BY nama");while($g=$q->fetch_assoc())echo '<option>'.e($g['nama']).'</option>';?></select></div>
<div style="flex:0"><label>&nbsp;</label><button class="btn">💾 Simpan</button></div></div></form>
<table><tr><th>No</th><th>Kelas</th><th>Wali Kelas</th><th>NIP Wali</th><th>Aksi</th></tr>
<?php $rowsK=[];$q=$conn->query("SELECT * FROM kelas");while($k=$q->fetch_assoc())$rowsK[]=$k;
usort($rowsK,function($a,$b){return kelasRank($a['nama_kelas'])-kelasRank($b['nama_kelas']);});
$i=0;foreach($rowsK as $k){$i++;echo '<tr><td class="c">'.$i.'</td><td class="c">'.e($k['nama_kelas']).'</td><td>'.e($k['wali']).'</td><td>'.e($k['nip_wali']).'</td><td><form method="post" style="margin:0"><input type="hidden" name="action" value="del_kelas"><input type="hidden" name="kid" value="'.$k['id'].'"><button class="btn red" style="margin:0;padding:3px 8px">✖</button></form></td></tr>';}?>
</table></div>
<?php } ?>

<?php if($p=='kkm'&&$isAdmin){ ?>
<div class="card"><h2>Tabel Rentang Predikat (panduan hasil nilai siswa)</h2>
<form method="post"><input type="hidden" name="action" value="save_kkm">
<table><tr><th>Min</th><th>Max</th><th>Predikat</th><th>Keterangan</th></tr>
<?php $q=$conn->query("SELECT * FROM kkm_range ORDER BY min DESC");while($k=$q->fetch_assoc())echo '<tr><td><input type="number" name="mn[]" value="'.e($k['min']).'" style="width:80px"></td><td><input type="number" name="mx[]" value="'.e($k['max']).'" style="width:80px"></td><td><input name="pr[]" value="'.e($k['predikat']).'" style="width:60px"></td><td><input name="kt[]" value="'.e($k['keterangan']).'"></td></tr>';?>
<tr><td><input type="number" name="mn[]" style="width:80px"></td><td><input type="number" name="mx[]" style="width:80px"></td><td><input name="pr[]" style="width:60px"></td><td><input name="kt[]"></td></tr>
</table><button class="btn green">💾 Simpan Rentang</button></form>
<div class="note">Catatan: <b>Indikator soal</b> kini diisi oleh <b>guru</b> di menu 📝 LEMBAR KOREKSI (melekat pada mapel + kelas + tanggal ujian).</div></div>
<?php } ?>

<?php if($p=='setup'){
 $mapelList=[];$q=$conn->query("SELECT DISTINCT mapel FROM users WHERE role='guru' AND mapel<>'' ORDER BY mapel");while($x=$q->fetch_assoc())$mapelList[]=$x['mapel'];
 $kelasList=[];$q=$conn->query("SELECT DISTINCT kelas FROM students WHERE kelas<>'' ORDER BY kelas");while($x=$q->fetch_assoc())$kelasList[]=$x['kelas'];
 $q=$conn->query("SELECT nama_kelas FROM kelas ORDER BY nama_kelas");while($x=$q->fetch_assoc())if(!in_array($x['nama_kelas'],$kelasList))$kelasList[]=$x['nama_kelas'];
 sort($kelasList);
 $guruList=[];$q=$conn->query("SELECT nama,nip FROM users WHERE role='guru' ORDER BY nama");while($x=$q->fetch_assoc())$guruList[]=$x;
 $kelasWali=[];$q=$conn->query("SELECT nama_kelas,wali FROM kelas");while($x=$q->fetch_assoc())$kelasWali[$x['nama_kelas']]=$x['wali'];
 $me=$conn->query("SELECT nama,nip,mapel FROM users WHERE id=$uid")->fetch_assoc();
 $editEx=null;$editId=(int)($_GET['edit']??0);
 if($editId){$ex=$conn->query("SELECT * FROM exams WHERE id=$editId")->fetch_assoc();if($ex&&($isAdmin||in_array((int)($ex['dibuat_oleh']??0),[0,$uid])))$editEx=$ex;}
 $curM=$editEx['mapel']??($me['mapel']??'');
 if($curM!==''&&!in_array($curM,$mapelList))array_unshift($mapelList,$curM);
 $curK=$editEx['kelas']??'';
 if($curK!==''&&!in_array($curK,$kelasList)){$kelasList[]=$curK;sort($kelasList);}
 $curW=$editEx['wali']??'';
 $kunciArr=$editEx?str_split($editEx['kunci']):[];
 $indArr=[];if($editEx){$q=$conn->query("SELECT no_soal,indikator FROM indikator WHERE exam_id=".$editEx['id']);while($x=$q->fetch_assoc())$indArr[(int)$x['no_soal']]=$x['indikator'];}
 $nCur=$editEx['jumlah_soal']??10;
?>
<div class="card"><h2>LEMBAR KOREKSI HASIL PENILAIAN HARIAN BERSAMA <?= $editEx?'— MODE EDIT ✏':''?></h2>
<form method="post"><input type="hidden" name="action" value="save_exam"><input type="hidden" name="edit_id" value="<?=$editEx['id']??0?>">
 <div class="row">
  <div><label>Mata Pelajaran</label>
   <?php if($isAdmin){?><input name="mapel" list="mapelList" value="<?=e($curM)?>" required><datalist id="mapelList"><?php foreach($mapelList as $m2)echo '<option value="'.e($m2).'">';?></datalist><?php }
   else{?><select name="mapel" required><?php foreach($mapelList as $m2)echo '<option '.($m2===$curM?'selected':'').'>'.e($m2).'</option>';?></select><?php }?>
  </div>
  <div><label>Kelas / Rombel (dropdown data siswa)</label><select name="kelas" id="kelasSel" required><?php foreach($kelasList as $kl)echo '<option '.($kl===$curK?'selected':'').'>'.e($kl).'</option>';?></select></div>
  <div><label>Semester</label><select name="smt"><option <?=($editEx['smt']??'Ganjil')==='Ganjil'?'selected':''?>>Ganjil</option><option <?=($editEx['smt']??'')==='Genap'?'selected':''?>>Genap</option></select></div>
  <div><label>Tanggal</label><input type="date" name="tanggal" value="<?=e($editEx['tanggal']??'')?>"></div>
 </div>
 <div class="row">
  <div><label>Jumlah Soal</label><input type="number" name="jumlah_soal" id="jml" value="<?=e($editEx['jumlah_soal']??10)?>"></div>
  <div><label>Bentuk</label><select name="bentuk"><?php foreach(['Pilihan Ganda','Benar-Salah','Menjodohkan','Isian Singkat','Uraian'] as $b)echo '<option '.(($editEx['bentuk']??'Pilihan Ganda')===$b?'selected':'').'>'.$b.'</option>';?></select></div>
  <div><label>KKM (WAJIB)</label><input type="number" name="kkm" value="<?=e($editEx['kkm']??$set['kkm'])?>" required></div>
  <div><label>Skor/Soal (kosong=100÷n)</label><input type="number" name="skor" value="<?=e($editEx['skor_per_soal']??'')?>"></div>
 </div>
 <div class="row">
  <?php if($isAdmin){?>
  <div><label>Guru Mapel</label><input name="guru" value="<?=e($editEx['guru']??$_SESSION['nama'])?>"></div>
  <div><label>NIP Guru</label><input name="nip_guru" value="<?=e($editEx['nip_guru']??($me['nip']??''))?>"></div>
  <?php } else { ?>
  <div><label>Guru Mapel (terkunci)</label><input name="guru" readonly value="<?=e($editEx['guru']??($me['nama']??$_SESSION['nama']))?>"></div>
  <div><label>NIP Guru (terkunci)</label><input name="nip_guru" readonly value="<?=e($editEx['nip_guru']??($me['nip']??''))?>"></div>
  <?php } ?>
  <div><label>Wali Kelas (dropdown)</label><select name="wali" id="waliSel"><?php $wFound=false;foreach($guruList as $g){if($g['nama']===$curW)$wFound=true;echo '<option '.($g['nama']===$curW?'selected':'').'>'.e($g['nama']).'</option>';}if($curW!==''&&!$wFound)echo '<option selected>'.e($curW).'</option>';?></select></div>
  <div><label>NIP Wali (otomatis)</label><input name="nip_wali" id="nipWali" readonly value="<?=e($editEx['nip_wali']??'')?>"></div>
 </div>
 <button type="button" class="btn" onclick="buatKunci()">🔑 Buat/Perbarui Kolom Kunci</button>
 <div id="kk" style="margin-top:10px;overflow-x:auto">
 <?php if($kunciArr){echo '<table><tr>';foreach($kunciArr as $i=>$kv)echo '<th class="c">'.($i+1).'</th>';echo '</tr><tr>';foreach($kunciArr as $i=>$kv)echo '<td class="c"><input name="k['.$i.']" maxlength="1" value="'.e($kv).'" style="width:34px;text-align:center"></td>';echo '</tr></table>';}?>
 </div>
 <button type="button" class="btn" onclick="buatInd()">📋 Buat/Perbarui Kolom Tema Soal</button>
 /* seed admin default */
 /*<div class="note">Isikan minimal 3 indikator; gunakan <b>huruf kecil</b>; soal dengan indikator sama cukup ditulis sama (deskripsi menggabungkan otomatis). Indikator melekat pada mapel + kelas + tanggal ujian ini.</div>*/
 <div id="ii" style="margin-top:10px">
 <table><tr><th style="width:70px">No. Soal</th><th>Tema  Soal</th></tr>
 <?php for($i=1;$i<=$nCur;$i++){echo '<tr><td class="c">'.$i.'</td><td><input name="ind['.$i.']" value="'.e($indArr[$i]??'').'" placeholder="contoh: menjelaskan konsep wilayah"></td></tr>';}?>
 </table></div>
 <button class="btn green"><?= $editEx?'💾 SIMPAN PERUBAHAN':'💾 SIMPAN UJIAN'?></button>
 <?php if($editEx){?><a class="btn gray" href="?p=setup">✖ Batalkan Edit</a><?php }?></form></div>
<div class="card"><h2>Ujian Tersimpan</h2><table><tr><th>Mapel</th><th>Kelas</th><th>Tanggal</th><th>Soal</th><th>KKM</th><th>Moda</th><th>Peserta</th><th>Aksi</th></tr>
<?php $w=$isAdmin?'':' WHERE dibuat_oleh IN (0,'.$uid.')';$q=$conn->query("SELECT e.*,(SELECT COUNT(*) FROM results r WHERE r.exam_id=e.id) pes FROM exams e".$w." ORDER BY id DESC");while($x=$q->fetch_assoc()){echo '<tr><td>'.e($x['mapel']).'</td><td class="c">'.e($x['kelas']).'</td><td>'.e($x['tanggal']).'</td><td class="c">'.$x['jumlah_soal'].'</td><td class="c">'.$x['kkm'].'</td><td class="c">'.e($x['mode']?:'-').'</td><td class="c">'.$x['pes'].'</td><td><a class="btn" style="margin:0;padding:3px 8px" href="?p=setup&edit='.$x['id'].'">✏</a> <form method="post" style="margin:0;display:inline"><input type="hidden" name="action" value="del_exam"><input type="hidden" name="exam_id" value="'.$x['id'].'"><button class="btn red" style="margin:0;padding:3px 8px">✖</button></form></td></tr>';}?></table></div>
<script>
var KELAS_WALI=<?=json_encode($kelasWali)?>;
var WALI_NIP=<?=json_encode(array_column($guruList,'nip','nama'))?>;
function updWali(){var w=document.getElementById('waliSel').value;document.getElementById('nipWali').value=WALI_NIP[w]||document.getElementById('nipWali').value||'';}
document.getElementById('kelasSel').onchange=function(){var w=KELAS_WALI[this.value]||'';var s=document.getElementById('waliSel');if(w){for(var i=0;i<s.options.length;i++){if(s.options[i].text===w){s.value=w;break;}}}updWali();};
document.getElementById('waliSel').onchange=function(){var w=this.value;document.getElementById('nipWali').value=WALI_NIP[w]||'';};
(function(){var s=document.getElementById('kelasSel');if(s&&s.value&&!<?= $editEx?'true':'false'?>)s.onchange.call(s);})();
function buatKunci(){var old={};document.querySelectorAll('#kk input').forEach(function(inp){old[inp.name]=inp.value;});var n=+document.getElementById('jml').value||10;var h='<table><tr>';for(var i=1;i<=n;i++)h+='<th class="c">'+i+'</th>';h+='</tr><tr>';for(var i=0;i<n;i++)h+='<td class="c"><input name="k['+i+']" maxlength="1" value="'+(old['k['+i+']']||'')+'" style="width:34px;text-align:center"></td>';h+='</tr></table>';document.getElementById('kk').innerHTML=h;}
function buatInd(){var old={};document.querySelectorAll('#ii input').forEach(function(inp){old[inp.name]=inp.value;});var n=+document.getElementById('jml').value||10;var h='<table><tr><th style="width:70px">No. Soal</th><th>Indikator Soal</th></tr>';for(var i=1;i<=n;i++){var v=(old['ind['+i+']']||'').replace(/"/g,'&quot;');h+='<tr><td class="c">'+i+'</td><td><input name="ind['+i+']" value="'+v+'" placeholder="contoh: menjelaskan konsep wilayah"></td></tr>';}h+='</table>';document.getElementById('ii').innerHTML=h;}
</script>
<?php } ?>

<?php if($p=='import'){ ?>
<div class="card"><h2>IMPORT HASIL GOOGLE FORM (CBT)</h2>
<div class="note">Cara 1: upload langsung file <b>.xlsx / .csv</b> hasil export Google Form/Sheets. Cara 2: paste copy spreadsheet (header: time Stamp | NAMA | NISN | KELAS | jawaban a–e). NISN tak dikenal tidak dibaca (atau centang auto-add). Duplikat NISN diambil submission terakhir.</div>
<form method="post"><input type="hidden" name="action" value="import_cbt">
 <label>Pilih Ujian</label><select name="exam_id"><?php echo examOptions($conn,$eid,$role,$uid);?></select>
 <label>Upload File Hasil Google Form (.xlsx/.csv)</label>
 <input type="file" id="fileHasil" accept=".xlsx,.xls,.csv">
 <label>Atau Paste Data di Sini</label><textarea name="paste" id="pasteNilai" rows="10"><?=e($_POST['paste']??'')?></textarea>
 <label style="font-weight:normal"><input type="checkbox" name="auto_add" style="width:auto" checked> NISN tak dikenal otomatis ditambah ke Master Siswa</label>
 <br><button class="btn green">⚙ IMPORT & KOREKSI OTOMATIS</button> <a class="btn gray" href="?p=import">🧹 BERSIH</a></form>
 <?php if($impInfo)echo '<div class="ok">✔ '.e($impInfo).'</div>';
 if($rejected){echo '<div class="err"><b>Tidak dapat dibaca:</b> ';foreach($rejected as $r)echo e($r['nama']).' ('.e($r['nisn']).'), ';echo '</div>';}
 if($examSel){$res=getResults($conn,$examSel['id']);if($res){echo '<table><tr><th>No</th><th>NISN</th><th>Nama</th><th>Kelas</th><th>Benar</th><th>Nilai</th><th>Status</th></tr>';foreach($res as $i=>$r)echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nisn']).'</td><td>'.e($r['nama']).'</td><td class="c">'.e($r['kelas']).'</td><td class="c">'.$r['benar'].'/'.$examSel['jumlah_soal'].'</td><td class="c"><b>'.$r['skor'].'</b></td><td class="c">'.e($r['status']).'</td></tr>';echo '</table>';}}?>
</div>
<script>document.getElementById('fileHasil').onchange=function(ev){var f=ev.target.files[0];if(!f)return;var r=new FileReader();r.onload=function(e2){var wb=XLSX.read(e2.target.result,{type:'array'});var ws=wb.Sheets[wb.SheetNames[0]];document.getElementById('pasteNilai').value=XLSX.utils.sheet_to_csv(ws,{FS:'\t'});};r.readAsArrayBuffer(f);};</script>
<?php } ?>

<?php if($p=='manual'&&$examSel){ $n=$examSel['jumlah_soal'];$mode=$_GET['mode']??'huruf'; ?>
<div class="card"><h2>INPUT MANUAL (NON CBT) — <?=e($examSel['mapel'])?> <?=e($examSel['kelas'])?></h2>
<p><a class="btn gray" href="?p=manual&exam=<?=$examSel['id']?>&mode=huruf">Mode Huruf</a> <a class="btn gray" href="?p=manual&exam=<?=$examSel['id']?>&mode=nilai">Mode Nilai Langsung</a></p>
<form method="post"><input type="hidden" name="action" value="save_manual"><input type="hidden" name="exam_id" value="<?=$examSel['id']?>"><input type="hidden" name="mode" value="<?=e($mode)?>">
<table><tr><th>No</th><th>NISN</th><th>Nama</th><?php if($mode=='huruf'){for($j=1;$j<=$n;$j++)echo '<th class="c">'.$j.'</th>';}else{echo '<th>Nilai</th>';} echo '</tr>';
$q=$conn->query("SELECT * FROM students WHERE kelas='".$conn->real_escape_string($examSel['kelas'])."' ORDER BY nama");$i=0;
while($s=$q->fetch_assoc()){$i++;echo '<tr><td class="c">'.$i.'</td><td>'.e($s['nisn']).'</td><td>'.e($s['nama']).'</td>';
 if($mode=='huruf'){for($j=0;$j<$n;$j++)echo '<td class="c"><input name="huruf['.e($s['nisn']).']['.$j.']" maxlength="1" style="width:30px;text-align:center;padding:3px"></td>';}
 else{echo '<td><input type="number" name="nilai['.e($s['nisn']).']" style="width:80px"></td>';}
 echo '</tr>';}?></table>
<button class="btn green">💾 SIMPAN & KOREKSI</button></form></div>
<?php } elseif($p=='manual'){ ?><div class="card"><h2>Input Manual (NON CBT)</h2><form method="get"><input type="hidden" name="p" value="manual"><label>Pilih Ujian</label><select name="exam"><?php echo examOptions($conn,0,$role,$uid);?></select><button class="btn">Buka</button></form></div><?php } ?>

<?php if($p=='gen'){ ?>
<div class="card no-print"><h2>GENERATE DATA</h2><form method="get"><input type="hidden" name="p" value="gen"><label>Pilih Ujian</label><select name="exam"><?php echo examOptions($conn,$eid,$role,$uid);?></select><button class="btn">Muat</button></form>
<?php if($examSel){$LAP=[['analisis','ANALISIS','CBT','c1'],['nilai','DAFTAR NILAI','CBT','c2'],['analisis','ANALISIS','NON CBT','c1'],['nilai','DAFTAR NILAI','NON CBT','c2'],['kesimpulan','KESIMPULAN','CBT','c3'],['target','TARGET','CBT','c4'],['kesimpulan','KESIMPULAN','NON CBT','c3'],['target','TARGET','NON CBT','c4'],['remedial','REMEDIAL','CBT','c5'],['pengayaan','PENGAYAAN','CBT','c6'],['remedial','REMEDIAL','NON CBT','c5'],['pengayaan','PENGAYAAN','NON CBT','c6'],['ket','KETERAMPILAN','','c7'],['penyerahan','LEMBAR PENYERAHAN','CBT','c7'],['penyerahan','LEMBAR PENYERAHAN','NON CBT','c7'],['rdm','LEMBAR SPH UNTUK RDM','','c9']];
$has=count(getResults($conn,$examSel['id']))>0;
echo '<div class="grid" style="margin-top:14px">';foreach($LAP as $L){$ok=$has&&($L[2]===''||$L[2]===$examSel['mode']);echo '<a class="gbtn '.$L[3].($ok?'':' off').'" href="?p=gen&exam='.$examSel['id'].'&t='.$L[0].'&m='.urlencode($L[2]).'">'.$L[1].($L[2]?' ('.$L[2].')':'').'</a>';}echo '</div>';}?></div>

<?php if($examSel){$res=getResults($conn,$examSel['id']);$st=statsOf($res,$examSel['jumlah_soal'],$examSel['kkm']);$t=$_GET['t']??'';$m=$_GET['m']??$examSel['mode'];
  if($t=='ket'&&$res){ ?>
 <div class="no-print" style="background:#fff;border:1px solid #2e7d32;border-radius:8px;padding:14px;margin-bottom:14px"><h2>INPUT NILAI KETERAMPILAN — <?=e($examSel['mapel'])?> <?=e($examSel['kelas'])?></h2>
 <div class="note">Isikan skor <b>1–4</b> untuk 5 aspek (jumlah skor maksimal 20). <b>Nilai = Jumlah Skor ÷ 20 × 100</b>.</div>
 <form method="post"><input type="hidden" name="action" value="save_ket"><input type="hidden" name="exam_id" value="<?=$examSel['id']?>">
 <table><tr><th>Nama</th><th>Aspek 1</th><th>Aspek 2</th><th>Aspek 3</th><th>Aspek 4</th><th>Aspek 5</th></tr><?php foreach($res as $r){$kt=$conn->query("SELECT * FROM keterampilan WHERE exam_id=".$examSel['id']." AND nisn='".$conn->real_escape_string($r['nisn'])."'")->fetch_assoc();echo '<tr><td>'.e($r['nama']).'</td>';for($x=1;$x<=5;$x++)echo '<td class="c"><input type="number" min="1" max="4" name="kt['.e($r['nisn']).']['.($x-1).']" value="'.e($kt['p'.$x]??'').'" style="width:60px"></td>';echo '</tr>';}?></table>
 <button class="btn green">💾 SIMPAN & PERBARUI LAPORAN</button></form></div>
 <?php
  echo kop($set);
  echo '<div class="rhead"><div>FORM PENILAIAN PRAKTIK/PROYEK/PORTOFOLIO</div><div style="font-weight:normal;font-size:12px">PENILAIAN HARIAN TAHUN AJARAN '.e($set['ta']).' — MATA PELAJARAN '.e(strtoupper($examSel['mapel'])).' — KELAS '.e($examSel['kelas']).' SEMESTER '.e(strtoupper($examSel['smt'])).'</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Nama Guru</td><td>: <b>'.e($examSel['guru']).'</b></td></tr><tr><td>Kelas / Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>NIP</td><td>: <b>'.e($examSel['nip_guru']).'</b></td></tr><tr><td>KKM</td><td>: <b>'.$examSel['kkm'].'</b></td><td>Tahun Pelajaran</td><td>: <b>'.e($set['ta']).'</b></td></tr></table>';
  echo '<table><tr><th>No</th><th>Nama Siswa</th><th>A1</th><th>A2</th><th>A3</th><th>A4</th><th>A5</th><th>Jumlah Skor (maks 20)</th><th>Nilai</th><th>Predikat</th></tr>';
  foreach($res as $i=>$r){$kt=$conn->query("SELECT * FROM keterampilan WHERE exam_id=".$examSel['id']." AND nisn='".$conn->real_escape_string($r['nisn'])."'")->fetch_assoc();$tot=0;$any=false;for($x=1;$x<=5;$x++){$v=$kt['p'.$x]??null;$tot+=$v?:0;if($v!==null)$any=true;}$nilai=$any?round($tot/20*100):null;
   echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nama']).'</td>';for($x=1;$x<=5;$x++)echo '<td class="c">'.e($kt['p'.$x]??'').'</td>';echo '<td class="c">'.($any?$tot:'').'</td><td class="c"><b>'.($nilai!==null?$nilai:'').'</b></td><td class="c">'.($nilai!==null?predikat($conn,$nilai):'').'</td></tr>';}
  echo '</table><h3>Rentang Predikat</h3>'.rentangTable($conn);
  echo '<table class="info" style="margin-top:26px"><tr><td style="width:50%"></td><td style="width:50%">Palembang, '.tglIndo($examSel['tanggal']).'<br>Guru Mata Pelajaran,<br><br><br><br><b><u>'.e($examSel['guru']?:'............................').'</u></b><br>NIP. '.e($examSel['nip_guru']?:'-').'</td></tr></table>';
 } elseif($t&&$t!=='ket'&&$res){ ?>
 <div class="card"><div class="no-print" style="margin-bottom:10px"><button class="btn" onclick="window.print()">🖨 CETAK/PDF</button> <a class="btn green" href="?p=csv&exam=<?=$examSel['id']?>">⬇ CSV</a></div>
 <?php
 if($t=='analisis'){
  echo '<div class="rhead">'.logoImg().'<div>LEMBAR ANALISIS HASIL PENILAIAN HARIAN BERSAMA ('.e($m).')</div><div class="pink">'.e($set['sekolah']).'</div><div>TAHUN PELAJARAN '.e($set['ta']).'</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Hari/Tanggal</td><td>: <b>'.e($examSel['tanggal']).'</b></td></tr>';
  echo '<tr><td>Kelas/Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>Nama Guru/NIP</td><td>: <b>'.e($examSel['guru']).' / '.e($examSel['nip_guru']).'</b></td></tr>';
  echo '<tr><td>Jumlah Peserta</td><td>: <b>'.$st['n'].' orang</b></td><td>Nama Wali Kelas/NIP</td><td>: <b>'.e($examSel['wali']).' / '.e($examSel['nip_wali']).'</b></td></tr>';
  echo '<tr><td>Jumlah Soal</td><td>: <b>'.$examSel['jumlah_soal'].' butir</b></td><td>KKM</td><td>: <b>'.$examSel['kkm'].'</b></td></tr>';
  echo '<tr><td>Bentuk Soal</td><td>: <b>'.e($examSel['bentuk']).'</b></td><td>Skor Soal</td><td>: <b>'.$examSel['skor_per_soal'].'</b></td></tr></table>';
  echo '<table><tr><th rowspan="2">No</th><th rowspan="2">NISN</th><th rowspan="2">Nama Siswa</th><th colspan="'.$examSel['jumlah_soal'].'">Nomor Soal / Skor Soal</th><th colspan="3">Hasil</th><th colspan="2">Ketuntasan</th></tr><tr>';
  for($i=1;$i<=$examSel['jumlah_soal'];$i++)echo '<th class="c">'.$i.'</th>';
  echo '<th class="c">Benar</th><th class="c">Salah</th><th class="c">Nilai</th><th class="c">Ya</th><th class="c">Tidak</th></tr>';
  foreach($res as $i=>$r){
    echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nisn']).'</td><td>'.e($r['nama']).'</td>';
    $hasDet=strlen((string)$r['detail'])==$examSel['jumlah_soal'];
    for($j=0;$j<$examSel['jumlah_soal'];$j++){echo '<td class="c">'.(($hasDet&&$r['detail'][$j]==='1')?$examSel['skor_per_soal']:'').'</td>';}
    echo '<td class="c">'.$r['benar'].'</td><td class="c">'.($examSel['jumlah_soal']-$r['benar']).'</td><td class="c"><b>'.$r['skor'].'</b></td>';
    $tun=$r['skor']>=$examSel['kkm'];
    echo '<td class="c">'.($tun?'✓':'').'</td><td class="c">'.($tun?'':'✓').'</td></tr>';
  }
  echo '<tr><td colspan="3"><b>Jumlah Benar per Soal</b></td>';
  foreach($st['perQ'] as $c)echo '<td class="c"><b>'.$c.'</b></td>';
  echo '<td colspan="5"></td></tr><tr><td colspan="3"><b>Daya Serap per Soal</b></td>';
  foreach($st['perQ'] as $c)echo '<td class="c">'.($st['n']?round($c/$st['n']*100):0).'%</td>';
  echo '<td colspan="5"></td></tr></table>';
  echo '<table class="info" style="margin-top:8px"><tr><td>Rata-rata Kelas</td><td>: <b>'.number_format($st['rata'],1).'</b></td><td>Ketuntasan Klasikal</td><td>: <b>'.number_format($st['kk'],1).'%</b> ('.$st['tuntas'].'/'.$st['n'].')</td></tr></table>';
 }
 elseif($t=='nilai'){
  echo '<div class="rhead">'.logoImg().'<div>DAFTAR NILAI PENILAIAN HARIAN BERSAMA ('.e($m).')</div><div class="pink">'.e($set['sekolah']).'</div><div>MATA PELAJARAN '.e(strtoupper($examSel['mapel'])).'</div><div>TAHUN PELAJARAN '.e($set['ta']).' — KELAS '.e($examSel['kelas']).' SEMESTER '.e(strtoupper($examSel['smt'])).'</div></div>';
  echo '<table><tr><th>No.</th><th>NISN</th><th>Nama</th><th>KKM</th><th>Nilai</th><th>Predikat</th><th>Deskripsi</th></tr>';
  foreach($res as $i=>$r){echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nisn']).'</td><td>'.e($r['nama']).'</td><td class="c">'.$examSel['kkm'].'</td><td class="c"><b>'.$r['skor'].'</b></td><td class="c">'.predikat($conn,$r['skor']).'</td><td>'.e(deskripsiOf($r,$examSel,$conn)).'</td></tr>';}
  echo '</table>';
  echo '<h3>Tabel Predikat (KKM '.$examSel['kkm'].')</h3>'.rentangTable($conn);
 }
  elseif($t=='kesimpulan'){
  $maxS=0;$minS=100;$dist=[];$q=$conn->query("SELECT * FROM kkm_range ORDER BY min DESC");while($x=$q->fetch_assoc()){$x['jml']=0;$dist[]=$x;}
  foreach($res as $r){$maxS=max($maxS,$r['skor']);$minS=min($minS,$r['skor']);foreach($dist as &$dd){if($r['skor']>=(int)$dd['min']&&$r['skor']<=(int)$dd['max'])$dd['jml']++;}}unset($dd);
  $soalPerlu=[];for($i=0;$i<$examSel['jumlah_soal'];$i++){$pc=$st['n']?$st['perQ'][$i]/$st['n']*100:0;if($pc<75)$soalPerlu[]=$i+1;}
  $siswaPerlu=[];foreach($res as $i=>$r){if($r['skor']<$examSel['kkm'])$siswaPerlu[]=$i+1;}
  echo kop($set);
  echo '<div class="rhead"><div>PENILAIAN HARIAN TAHUN AJARAN '.e($set['ta']).'</div><div>KESIMPULAN HASIL ANALISIS ('.e($m).')</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Jumlah Soal</td><td>: <b>'.$examSel['jumlah_soal'].' butir</b></td></tr><tr><td>Kelas/Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>Bentuk Soal</td><td>: <b>'.e($examSel['bentuk']).'</b></td></tr><tr><td>Tahun Pelajaran</td><td>: <b>'.e($set['ta']).'</b></td><td>KKM</td><td>: <b>'.$examSel['kkm'].'</b></td></tr></table>';
  echo '<div style="display:flex;gap:16px;flex-wrap:wrap"><div style="flex:2;min-width:300px"><table class="info"><tr><td colspan="2"><b>1. Ketuntasan Belajar</b></td></tr><tr><td colspan="2">a. Perorangan</td></tr><tr><td style="padding-left:18px">Jumlah siswa seluruhnya</td><td>: <b>'.$st['n'].' orang</b></td></tr><tr><td style="padding-left:18px">Jumlah siswa yang telah tuntas</td><td>: <b>'.$st['tuntas'].' orang</b></td></tr><tr><td style="padding-left:18px">Jumlah siswa yang belum tuntas</td><td>: <b>'.($st['n']-$st['tuntas']).' orang</b></td></tr><tr><td style="padding-left:18px">Nilai tertinggi</td><td>: <b>'.$maxS.'</b></td></tr><tr><td style="padding-left:18px">Nilai terendah</td><td>: <b>'.$minS.'</b></td></tr><tr><td colspan="2">b. Klasikal</td></tr><tr><td style="padding-left:18px">Persentase siswa yang telah tuntas</td><td>: <b>'.number_format($st['kk'],1).'%</b></td></tr><tr><td style="padding-left:18px">Rata-rata hasil penilaian harian</td><td>: <b>'.number_format($st['rata'],1).'</b></td></tr></table></div>';
  echo '<div style="flex:1;min-width:260px"><table><tr><th>No</th><th>Rentang</th><th>Jumlah</th><th>Keterangan</th></tr>';$no=1;foreach($dist as $dd){echo '<tr><td class="c">'.$no++.'</td><td class="c">'.$dd['min'].' — '.$dd['max'].'</td><td class="c">'.$dd['jml'].'</td><td>'.e(ucfirst($dd['keterangan'])).'</td></tr>';}echo '</table></div></div>';
  echo '<table class="info" style="margin-top:8px"><tr><td colspan="2"><b>2. Kesimpulan</b></td></tr><tr><td style="width:26px">a.</td><td>Perlu perbaikan secara klasikal untuk soal nomor: <b>'.($soalPerlu?implode(', ',$soalPerlu):'-').'</b></td></tr><tr><td>b.</td><td>Perlu perbaikan secara individual untuk siswa bernomor: <b>'.($siswaPerlu?implode(', ',$siswaPerlu):'-').'</b></td></tr></table>';
  echo '<table class="info" style="margin-top:8px"><tr><td colspan="2"><b>Keterangan</b></td></tr><tr><td style="width:26px">A.</td><td>Daya serap perorangan: seorang siswa disebut telah tuntas belajar bila mencapai nilai ≥ KKM ('.$examSel['kkm'].') dengan rumus: Nilai = Jumlah Benar × Skor per Soal.</td></tr><tr><td>B.</td><td>Daya serap klasikal: suatu kelas telah tuntas belajar bila ≥ 75% siswa mencapai daya serap yang dipersyaratkan, dengan rumus: (Siswa Tuntas ÷ Jumlah Siswa) × 100% = '.number_format($st['kk'],1).'%.</td></tr></table>';
  echo sigResmi($examSel,$set);
 }
  elseif($t=='target'){
  $ind=[];$q=$conn->query("SELECT no_soal,indikator FROM indikator WHERE exam_id=".$examSel['id']." ORDER BY no_soal");while($x=$q->fetch_assoc())$ind[(int)$x['no_soal']]=$x['indikator'];
  echo kop($set);
  echo '<div class="rhead"><div>PENILAIAN HARIAN TAHUN AJARAN '.e($set['ta']).'</div><div>PENCAPAIAN TARGET KURIKULUM DAN DAYA SERAP SISWA ('.e($m).')</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Jumlah Soal</td><td>: <b>'.$examSel['jumlah_soal'].' butir</b></td></tr><tr><td>Kelas/Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>Bentuk Soal</td><td>: <b>'.e($examSel['bentuk']).'</b></td></tr><tr><td>Jumlah Siswa</td><td>: <b>'.$st['n'].' orang</b></td><td>KKM</td><td>: <b>'.$examSel['kkm'].'</b></td></tr></table>';
  echo '<table><tr><th style="width:80px">Nomor Soal</th><th>Materi / Kompetensi Dasar / Indikator</th><th class="c">Target Tuntas</th><th class="c">Belum Tuntas</th><th class="c">Daya Serap</th></tr>';
  for($i=0;$i<$examSel['jumlah_soal'];$i++){$c=$st['perQ'][$i];$pc=$st['n']?round($c/$st['n']*100):0;echo '<tr><td class="c">'.($i+1).'</td><td>'.e($ind[$i+1]??'-').'</td><td class="c">'.$c.' orang</td><td class="c">'.($st['n']-$c).' orang</td><td class="c">'.$pc.'%</td></tr>';}
  echo '</table>';
  echo sigResmi($examSel,$set);
 }
  elseif($t=='remedial'){
  $rem=array_values(array_filter($res,function($r)use($examSel){return $r['skor']<$examSel['kkm'];}));
  $tl=[];$q=$conn->query("SELECT * FROM tindak_lanjut WHERE exam_id=".$examSel['id']);while($x=$q->fetch_assoc())$tl[$x['nisn']]=$x;
  $indText=[];$q=$conn->query("SELECT indikator FROM indikator WHERE exam_id=".$examSel['id']." ORDER BY no_soal");while($x=$q->fetch_assoc()){$v=strtolower(trim($x['indikator']));if($v!==''&&!in_array($v,$indText))$indText[]=$v;}
  $kd=$indText?implode('; ',$indText).'.':'(indikator belum diisi pada lembar koreksi)';
  $BR=['Pemberian bimbingan secara individu','Pemberian bimbingan secara kelompok','Pemberian pembelajaran ulang','Pemanfaatan tutor sebaya'];
  echo '<div class="no-print" style="background:#fff;border:1px solid #2e7d32;border-radius:8px;padding:14px;margin-bottom:14px"><h2>INPUT PROGRAM REMEDIAL — '.e($examSel['mapel']).' '.e($examSel['kelas']).'</h2>';
  echo '<div class="note">Nilai Awal otomatis dari hasil koreksi. Isi <b>Nilai Akhir</b> setelah remedial dan pilih <b>Bentuk Remedial</b>. Keterangan berubah otomatis: <b>Tuntas</b> bila Nilai Akhir ≥ KKM '.$examSel['kkm'].'.</div>';
  echo '<form method="post"><input type="hidden" name="action" value="save_tl"><input type="hidden" name="exam_id" value="'.$examSel['id'].'">';
  echo '<table><tr><th>No</th><th>Nama Siswa</th><th>Nilai Awal</th><th>Nilai Akhir</th><th>Bentuk Remedial</th><th>Keterangan</th></tr>';
  foreach($rem as $i=>$r){$x=$tl[$r['nisn']]??null;$na=$x['nilai_akhir']??null;$ket=$na!==null?(($na>=$examSel['kkm'])?'Tuntas':'Belum Tuntas'):'Belum Tuntas';
   echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nama']).'</td><td class="c">'.$r['skor'].'</td>';
   echo '<td class="c"><input type="number" name="tl['.e($r['nisn']).'][akhir]" value="'.e($na).'" style="width:70px" oninput="ketHitung(this,\'ket_'.e($r['nisn']).'\','.$examSel['kkm'].')"></td>';
   echo '<td><select name="tl['.e($r['nisn']).'][bentuk]"><option value="">-- pilih --</option>';foreach($BR as $b)echo '<option '.(($x['bentuk']??'')===$b?'selected':'').'>'.e($b).'</option>';echo '</select></td>';
   echo '<td class="c" id="ket_'.e($r['nisn']).'">'.$ket.'</td></tr>';}
  if(!$rem)echo '<tr><td colspan="6">Tidak ada peserta remedial 🎉</td></tr>';
  echo '</table><button class="btn green">💾 SIMPAN & PERBARUI LAPORAN</button></form></div>';
  echo '<script>function ketHitung(el,id,kkm){var v=el.value===""?null:+el.value;document.getElementById(id).textContent=(v!==null&&v>=kkm)?"Tuntas":"Belum Tuntas";}</script>';
  echo '<div class="rhead">'.logoImg().'<div>PROGRAM REMEDIAL HASIL PENILAIAN HARIAN BERSAMA</div><div class="pink">'.e($set['sekolah']).'</div><div>TAHUN PELAJARAN '.e($set['ta']).'</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Hari/Tanggal</td><td>: <b>'.e($examSel['tanggal']).'</b></td></tr><tr><td>Kelas/Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>Guru Mapel</td><td>: <b>'.e($examSel['guru']).'</b></td></tr></table>';
  echo '<table><tr><th rowspan="2">No</th><th rowspan="2">Nama Siswa</th><th rowspan="2">Kompetensi Dasar / Indikator</th><th colspan="'.$examSel['jumlah_soal'].'">Indikator / Nomor Soal yang Belum Tuntas</th><th rowspan="2">KKM</th><th rowspan="2">Bentuk Remedial</th><th colspan="2">Hasil</th><th rowspan="2">Keterangan</th></tr><tr>';
  for($i=1;$i<=$examSel['jumlah_soal'];$i++)echo '<th class="c">'.$i.'</th>';
  echo '<th class="c">Awal</th><th class="c">Akhir</th></tr>';
  if($rem){foreach($rem as $i=>$r){$x=$tl[$r['nisn']]??null;$na=$x['nilai_akhir']??null;$ket=$na!==null?(($na>=$examSel['kkm'])?'Tuntas':'Belum Tuntas'):'Belum Tuntas';
    echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nama']).'</td>';
    if($i===0)echo '<td rowspan="'.count($rem).'">'.e($kd).'</td>';
    $hasDet=strlen((string)$r['detail'])==$examSel['jumlah_soal'];
    for($j=0;$j<$examSel['jumlah_soal'];$j++)echo '<td class="c">'.(($hasDet&&$r['detail'][$j]==='0')?'✗':'').'</td>';
    echo '<td class="c">'.$examSel['kkm'].'</td><td>'.e($x['bentuk']??'').'</td><td class="c">'.$r['skor'].'</td><td class="c">'.($na!==null?$na:'').'</td><td class="c">'.$ket.'</td></tr>';}}
  else echo '<tr><td colspan="'.(8+$examSel['jumlah_soal']).'">Tidak ada peserta remedial 🎉</td></tr>';
  echo '</table>'.sigBlock($examSel,$set);
 }
  elseif($t=='pengayaan'){
  $pg=array_values(array_filter($res,function($r)use($examSel){return $r['skor']>=$examSel['kkm'];}));
  $tl=[];$q=$conn->query("SELECT * FROM tindak_lanjut WHERE exam_id=".$examSel['id']);while($x=$q->fetch_assoc())$tl[$x['nisn']]=$x;
  $indText=[];$q=$conn->query("SELECT indikator FROM indikator WHERE exam_id=".$examSel['id']." ORDER BY no_soal");while($x=$q->fetch_assoc()){$v=strtolower(trim($x['indikator']));if($v!==''&&!in_array($v,$indText))$indText[]=$v;}
  $kd=$indText?implode('; ',$indText).'.':'(indikator belum diisi pada lembar koreksi)';
  $BP=['Belajar Mandiri','Belajar Kelompok','Pengayaan'];
  echo '<div class="no-print" style="background:#fff;border:1px solid #2e7d32;border-radius:8px;padding:14px;margin-bottom:14px"><h2>INPUT PROGRAM PENGAYAAN — '.e($examSel['mapel']).' '.e($examSel['kelas']).'</h2>';
  echo '<div class="note">Nilai Awal otomatis dari hasil koreksi. Isi <b>Nilai Akhir</b> dan pilih <b>Bentuk Pengayaan</b>.</div>';
  echo '<form method="post"><input type="hidden" name="action" value="save_tl"><input type="hidden" name="exam_id" value="'.$examSel['id'].'">';
  echo '<table><tr><th>No</th><th>Nama Siswa</th><th>Nilai Awal</th><th>Nilai Akhir</th><th>Bentuk Pengayaan</th></tr>';
  foreach($pg as $i=>$r){$x=$tl[$r['nisn']]??null;
   echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nama']).'</td><td class="c">'.$r['skor'].'</td>';
   echo '<td class="c"><input type="number" name="tl['.e($r['nisn']).'][akhir]" value="'.e($x['nilai_akhir']??'').'" style="width:70px"></td>';
   echo '<td><select name="tl['.e($r['nisn']).'][bentuk]"><option value="">-- pilih --</option>';foreach($BP as $b)echo '<option '.(($x['bentuk']??'')===$b?'selected':'').'>'.e($b).'</option>';echo '</select></td></tr>';}
  if(!$pg)echo '<tr><td colspan="5">Tidak ada peserta pengayaan.</td></tr>';
  echo '</table><button class="btn green">💾 SIMPAN & PERBARUI LAPORAN</button></form></div>';
  echo '<div class="rhead">'.logoImg().'<div>PROGRAM PENGAYAAN HASIL PENILAIAN HARIAN BERSAMA</div><div class="pink">'.e($set['sekolah']).'</div><div>TAHUN PELAJARAN '.e($set['ta']).'</div></div>';
  echo '<table class="info"><tr><td>Mata Pelajaran</td><td>: <b>'.e($examSel['mapel']).'</b></td><td>Hari/Tanggal</td><td>: <b>'.e($examSel['tanggal']).'</b></td></tr><tr><td>Kelas/Semester</td><td>: <b>'.e($examSel['kelas']).' / '.e($examSel['smt']).'</b></td><td>Guru Mapel</td><td>: <b>'.e($examSel['guru']).'</b></td></tr></table>';
  echo '<table><tr><th>No</th><th>Nama Siswa</th><th>Kompetensi Dasar / Indikator</th><th>KKM</th><th>Nilai Awal</th><th>Nilai Akhir</th><th>Bentuk Pengayaan</th></tr>';
  if($pg){foreach($pg as $i=>$r){$x=$tl[$r['nisn']]??null;
    echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nama']).'</td>';
    if($i===0)echo '<td rowspan="'.count($pg).'">'.e($kd).'</td>';
    echo '<td class="c">'.$examSel['kkm'].'</td><td class="c">'.$r['skor'].'</td><td class="c">'.e($x['nilai_akhir']??'').'</td><td>'.e($x['bentuk']??'').'</td></tr>';}}
  else echo '<tr><td colspan="7">Tidak ada peserta pengayaan.</td></tr>';
  echo '</table>'.sigBlock($examSel,$set);
 }
  elseif($t=='penyerahan'){echo rhead($examSel,$set,'LEMBAR PENYERAHAN NILAI / TANDA TERIMA ('.e($m).')');echo '<table><tr><th>No</th><th>NISN</th><th>Nama</th><th>Nilai PH</th><th>Keterampilan</th><th>Rata-rata</th><th>Predikat</th><th>Tanggal</th><th>Tanda Tangan</th></tr>';foreach($res as $i=>$r){$kt=$conn->query("SELECT * FROM keterampilan WHERE exam_id=".$examSel['id']." AND nisn='".$conn->real_escape_string($r['nisn'])."'")->fetch_assoc();$vals=array_filter([$kt['p1']??null,$kt['p2']??null,$kt['p3']??null,$kt['p4']??null,$kt['p5']??null],function($v){return $v!==null;});$avg=$vals?array_sum($vals)/count($vals):null;$fin=$avg!==null?round(($r['skor']+$avg)/2):$r['skor'];echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nisn']).'</td><td>'.e($r['nama']).'</td><td class="c">'.$r['skor'].'</td><td class="c">'.($avg!==null?round($avg):'-').'</td><td class="c"><b>'.$fin.'</b></td><td class="c">'.predikat($conn,$fin).'</td><td></td><td></td></tr>';}echo '</table>'.sigBlock($examSel,$set);}
 elseif($t=='rdm'){echo rhead($examSel,$set,'LEMBAR SPH UNTUK RDM (RAPOR DIGITAL MADRASAH)');echo '<table><tr><th>No</th><th>NISN</th><th>Nama</th><th>Nilai</th><th>Predikat</th><th>Deskripsi (otomatis)</th></tr>';foreach($res as $i=>$r){echo '<tr><td class="c">'.($i+1).'</td><td>'.e($r['nisn']).'</td><td>'.e($r['nama']).'</td><td class="c"><b>'.$r['skor'].'</b></td><td class="c">'.predikat($conn,$r['skor']).'</td><td>'.e(deskripsiOf($r,$examSel,$conn)).'</td></tr>';}echo '</table>'.sigBlock($examSel,$set);}
 echo '</div>';
 } } } ?>
</main></body></html>