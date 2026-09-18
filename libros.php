<?php
require_once __DIR__ . '/bootstrap.php';
$page_title='Libros'; $pdo=db();
if ($_SERVER['REQUEST_METHOD']==='POST') {
 check_csrf(); $action=$_POST['action']??'';
 try {
  if($action==='save'){
   $id=(int)($_POST['id']??0); $codigo=trim($_POST['codigo']??''); $titulo=trim($_POST['titulo']??''); $autor=trim($_POST['autor']??''); $unidades=money_or_int($_POST['unidades']??0);
   if($codigo===''||$titulo===''||$autor==='') throw new RuntimeException('Completa código, título y autor.');
   if($id){$s=$pdo->prepare('UPDATE libros SET codigo=?,titulo=?,autor=?,unidades=? WHERE id=?');$s->execute([$codigo,$titulo,$autor,$unidades,$id]);flash('success','Libro actualizado correctamente.');}
   else{$s=$pdo->prepare('INSERT INTO libros(codigo,titulo,autor,unidades) VALUES(?,?,?,?)');$s->execute([$codigo,$titulo,$autor,$unidades]);flash('success','Libro creado correctamente.');}
  } elseif($action==='toggle'){$id=(int)$_POST['id'];$s=$pdo->prepare('UPDATE libros SET activo=1-activo WHERE id=?');$s->execute([$id]);flash('success','Estado del libro actualizado.');}
  redirect('libros.php');
 }catch(Throwable $e){flash('error',$e->getCode()===23000?'El código del libro ya está registrado.':$e->getMessage());redirect('libros.php');}
}
$q=trim($_GET['q']??''); $s=$pdo->prepare("SELECT * FROM libros WHERE codigo LIKE :q OR titulo LIKE :q OR autor LIKE :q ORDER BY titulo");$s->execute(['q'=>'%'.$q.'%']);$books=$s->fetchAll();
$edit=null;if(isset($_GET['editar'])){$s=$pdo->prepare('SELECT * FROM libros WHERE id=?');$s->execute([(int)$_GET['editar']]);$edit=$s->fetch();}
include __DIR__.'/header.php';
?>
<div class="grid-2">
<section class="card"><div class="card-head"><div><h2><?= $edit?'Editar libro':'Nuevo libro' ?></h2><p>Inventario de ejemplares</p></div></div>
<form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
<label>Código<input name="codigo" maxlength="50" required value="<?= e($edit['codigo']??'') ?>"></label><label>Título<input name="titulo" maxlength="200" required value="<?= e($edit['titulo']??'') ?>"></label><label>Autor<input name="autor" maxlength="180" required value="<?= e($edit['autor']??'') ?>"></label><label>Unidades disponibles<input type="number" min="0" name="unidades" required value="<?= (int)($edit['unidades']??0) ?>"></label>
<div class="actions"><button class="btn primary" type="submit">Guardar libro</button><?php if($edit): ?><a class="btn" href="libros.php">Cancelar</a><?php endif; ?></div></form></section>
<section class="card"><div class="card-head"><div><h2>Catálogo</h2><p><?= count($books) ?> registro(s)</p></div><form class="search" method="get"><input name="q" placeholder="Código, título o autor..." value="<?= e($q) ?>"><button class="btn" type="submit">Buscar</button></form></div>
<div class="table-wrap"><table><thead><tr><th>Código</th><th>Título</th><th>Autor</th><th>Unidades</th><th>Estado</th><th></th></tr></thead><tbody><?php foreach($books as $b): ?><tr><td><strong><?= e($b['codigo']) ?></strong></td><td><?= e($b['titulo']) ?></td><td><?= e($b['autor']) ?></td><td><span class="stock <?= $b['unidades']>0?'ok':'zero' ?>"><?= (int)$b['unidades'] ?></span></td><td><span class="badge <?= $b['activo']?'activo':'inactivo' ?>"><?= $b['activo']?'ACTIVO':'INACTIVO' ?></span></td><td class="row-actions"><a class="icon-btn" href="libros.php?editar=<?= $b['id'] ?>">Editar</a><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $b['id'] ?>"><button class="icon-btn" type="submit"><?= $b['activo']?'Desactivar':'Activar' ?></button></form></td></tr><?php endforeach; ?><?php if(!$books): ?><tr><td colspan="6" class="empty">No se encontraron libros.</td></tr><?php endif; ?></tbody></table></div></section></div>
<?php include __DIR__.'/footer.php'; ?>
