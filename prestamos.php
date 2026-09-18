<?php
require_once __DIR__ . '/bootstrap.php';
$page_title='Préstamos'; $pdo=db();

if($_SERVER['REQUEST_METHOD']==='POST'){
 check_csrf(); $action=$_POST['action']??'';
 try{
  if($action==='create'){
   $usuario=(int)($_POST['usuario_id']??0);$fecha=trim($_POST['fecha_prestamo']??date('Y-m-d'));$venc=trim($_POST['fecha_vencimiento']??'');$obs=trim($_POST['observaciones']??'');
   $items=$_POST['libro_id']??[];$qty=$_POST['cantidad']??[];
   if(!$usuario||!$venc||strtotime($venc)<strtotime($fecha)) throw new RuntimeException('Revisa el usuario y las fechas del préstamo.');
   $cart=[]; foreach((array)$items as $i=>$bookId){$bookId=(int)$bookId;$q=(int)($qty[$i]??1);if($bookId&&$q>0)$cart[$bookId]=($cart[$bookId]??0)+$q;}
   if(!$cart) throw new RuntimeException('Selecciona al menos un libro.');
   $pdo->beginTransaction();
   $u=$pdo->prepare('SELECT id FROM usuarios WHERE id=? AND activo=1');$u->execute([$usuario]);if(!$u->fetch())throw new RuntimeException('El usuario no está activo.');
   foreach($cart as $bookId=>$q){$s=$pdo->prepare('SELECT id,unidades FROM libros WHERE id=? AND activo=1 FOR UPDATE');$s->execute([$bookId]);$book=$s->fetch();if(!$book)throw new RuntimeException('Uno de los libros no está disponible.');if((int)$book['unidades']<$q)throw new RuntimeException('No hay suficientes unidades para el libro ID '.$bookId);}
   $s=$pdo->prepare("INSERT INTO prestamos(usuario_id,fecha_prestamo,fecha_vencimiento,observaciones) VALUES(?,?,?,?)");$s->execute([$usuario,$fecha,$venc,$obs]);$loanId=(int)$pdo->lastInsertId();
   $detail=$pdo->prepare('INSERT INTO prestamo_detalle(prestamo_id,libro_id,cantidad) VALUES(?,?,?)');$dec=$pdo->prepare('UPDATE libros SET unidades=unidades-? WHERE id=?');
   foreach($cart as $bookId=>$q){$detail->execute([$loanId,$bookId,$q]);$dec->execute([$q,$bookId]);}
   $pdo->commit();flash('success','Préstamo #'.$loanId.' registrado y stock actualizado.');
  }elseif($action==='return'){
   $loanId=(int)$_POST['id'];$pdo->beginTransaction();$s=$pdo->prepare("SELECT * FROM prestamos WHERE id=? AND estado IN ('ACTIVO','VENCIDO') FOR UPDATE");$s->execute([$loanId]);$loan=$s->fetch();if(!$loan)throw new RuntimeException('El préstamo no está pendiente de devolución.');
   $details=$pdo->prepare('SELECT libro_id,cantidad FROM prestamo_detalle WHERE prestamo_id=?');$details->execute([$loanId]);$inc=$pdo->prepare('UPDATE libros SET unidades=unidades+? WHERE id=?');foreach($details as $d)$inc->execute([(int)$d['cantidad'],(int)$d['libro_id']]);
   $upd=$pdo->prepare("UPDATE prestamos SET estado='DEVUELTO',fecha_devolucion=CURDATE() WHERE id=?");$upd->execute([$loanId]);$pdo->commit();flash('success','Préstamo devuelto y existencias reintegradas.');
  }
  redirect('prestamos.php');
 }catch(Throwable $e){if($pdo->inTransaction())$pdo->rollBack();flash('error',$e->getMessage());redirect('prestamos.php');}
}
$users=$pdo->query("SELECT id,nombre,cedula FROM usuarios WHERE activo=1 ORDER BY nombre")->fetchAll();
$books=$pdo->query("SELECT id,codigo,titulo,unidades FROM libros WHERE activo=1 AND unidades>0 ORDER BY titulo")->fetchAll();
$status=$_GET['estado']??'TODOS';$allowed=['TODOS','ACTIVO','VENCIDO','DEVUELTO'];if(!in_array($status,$allowed,true))$status='TODOS';
$sql="SELECT p.*,u.nombre usuario, GROUP_CONCAT(CONCAT(l.titulo,' x',d.cantidad) ORDER BY l.titulo SEPARATOR ', ') libros FROM prestamos p JOIN usuarios u ON u.id=p.usuario_id JOIN prestamo_detalle d ON d.prestamo_id=p.id JOIN libros l ON l.id=d.libro_id ";$params=[];if($status!=='TODOS'){$sql.=' WHERE p.estado=? ';$params[]=$status;}$sql.=' GROUP BY p.id ORDER BY p.id DESC LIMIT 100';$s=$pdo->prepare($sql);$s->execute($params);$loans=$s->fetchAll();
include __DIR__.'/header.php';
?>
<section class="card"><div class="card-head"><div><h2>Registrar préstamo</h2><p>Puedes agregar varios libros al mismo préstamo.</p></div></div>
<form method="post" class="loan-form" id="loanForm"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="create">
<div class="loan-row"><label>Usuario<select name="usuario_id" required><option value="">Selecciona...</option><?php foreach($users as $u): ?><option value="<?= $u['id'] ?>"><?= e($u['nombre'].' — '.$u['cedula']) ?></option><?php endforeach; ?></select></label><label>Fecha préstamo<input type="date" name="fecha_prestamo" value="<?= date('Y-m-d') ?>" required></label><label>Fecha vencimiento<input type="date" name="fecha_vencimiento" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" required></label></div>
<div class="items" id="items"><div class="loan-item"><select name="libro_id[]" required><option value="">Selecciona libro...</option><?php foreach($books as $b): ?><option value="<?= $b['id'] ?>"><?= e($b['codigo'].' · '.$b['titulo'].' (disp. '.$b['unidades'].')') ?></option><?php endforeach; ?></select><input type="number" name="cantidad[]" min="1" value="1" required><button type="button" class="icon-btn remove-item">Quitar</button></div></div>
<div class="loan-bottom"><button type="button" class="btn" id="addItem">+ Agregar libro</button><label>Observaciones<textarea name="observaciones" maxlength="500" placeholder="Notas opcionales"></textarea></label><button class="btn primary" type="submit">Registrar préstamo</button></div>
</form></section>
<section class="card"><div class="card-head"><div><h2>Historial</h2><p>Últimos 100 préstamos</p></div><form class="filters" method="get"><label>Estado<select name="estado" class="auto-submit"><option <?= $status==='TODOS'?'selected':'' ?>>TODOS</option><option <?= $status==='ACTIVO'?'selected':'' ?>>ACTIVO</option><option <?= $status==='VENCIDO'?'selected':'' ?>>VENCIDO</option><option <?= $status==='DEVUELTO'?'selected':'' ?>>DEVUELTO</option></select></label></form></div>
<div class="table-wrap"><table><thead><tr><th>#</th><th>Usuario</th><th>Libros</th><th>Préstamo</th><th>Vence</th><th>Estado</th><th>Acción</th></tr></thead><tbody><?php foreach($loans as $l): ?><tr><td>#<?= $l['id'] ?></td><td><?= e($l['usuario']) ?></td><td><?= e($l['libros']) ?></td><td><?= e($l['fecha_prestamo']) ?></td><td><?= e($l['fecha_vencimiento']) ?></td><td><span class="badge <?= strtolower($l['estado']) ?>"><?= e($l['estado']) ?></span></td><td><?php if(in_array($l['estado'],['ACTIVO','VENCIDO'],true)): ?><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="return"><input type="hidden" name="id" value="<?= $l['id'] ?>"><button class="btn small" type="submit" data-confirm="¿Confirmas la devolución del préstamo #<?= $l['id'] ?>?">Devolver</button></form><?php else: ?><span class="muted">Completado</span><?php endif; ?></td></tr><?php endforeach; ?><?php if(!$loans): ?><tr><td colspan="7" class="empty">No hay préstamos para este filtro.</td></tr><?php endif; ?></tbody></table></div></section>
<script>
const items=document.getElementById('items');
document.getElementById('addItem').addEventListener('click',()=>{const source=items.querySelector('.loan-item');const clone=source.cloneNode(true);clone.querySelector('select').value='';clone.querySelector('input').value=1;items.appendChild(clone);});
items.addEventListener('click',e=>{if(e.target.classList.contains('remove-item') && items.querySelectorAll('.loan-item').length>1)e.target.closest('.loan-item').remove();});
</script>
<?php include __DIR__.'/footer.php'; ?>
