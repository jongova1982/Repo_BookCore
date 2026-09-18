<?php
require_once __DIR__ . '/bootstrap.php';
$page_title = 'Usuarios'; $pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  check_csrf(); $action = $_POST['action'] ?? '';
  try {
    if ($action === 'save') {
      $id=(int)($_POST['id']??0); $nombre=trim($_POST['nombre']??''); $cedula=trim($_POST['cedula']??''); $telefono=normalize_phone($_POST['telefono']??'');
      if ($nombre==='' || $cedula==='' || $telefono==='') throw new RuntimeException('Completa todos los campos obligatorios.');
      if ($id) { $s=$pdo->prepare('UPDATE usuarios SET nombre=?,cedula=?,telefono=? WHERE id=?'); $s->execute([$nombre,$cedula,$telefono,$id]); flash('success','Usuario actualizado correctamente.'); }
      else { $s=$pdo->prepare('INSERT INTO usuarios(nombre,cedula,telefono) VALUES(?,?,?)'); $s->execute([$nombre,$cedula,$telefono]); flash('success','Usuario creado correctamente.'); }
    } elseif ($action === 'toggle') {
      $id=(int)$_POST['id']; $s=$pdo->prepare('UPDATE usuarios SET activo=1-activo WHERE id=?'); $s->execute([$id]); flash('success','Estado del usuario actualizado.');
    }
    redirect('usuarios.php');
  } catch (Throwable $e) { flash('error', $e->getCode()===23000 ? 'La cédula ya está registrada.' : $e->getMessage()); redirect('usuarios.php'); }
}
$q=trim($_GET['q']??'');
$stmt=$pdo->prepare("SELECT * FROM usuarios
    WHERE nombre LIKE :q1
    OR cedula LIKE :q2
    OR telefono LIKE :q3
    ORDER BY nombre");

$stmt->execute([
    'q1' => '%'.$q.'%',
    'q2' => '%'.$q.'%',
    'q3' => '%'.$q.'%'
]);
 $users=$stmt->fetchAll();
$edit=null; if (isset($_GET['editar'])) { $s=$pdo->prepare('SELECT * FROM usuarios WHERE id=?'); $s->execute([(int)$_GET['editar']]); $edit=$s->fetch(); }
include __DIR__ . '/header.php';
?>
<div class="grid-2">
  <section class="card"><div class="card-head"><div><h2><?= $edit ? 'Editar usuario' : 'Nuevo usuario' ?></h2><p>Nombre, cédula y teléfono</p></div></div>
  <form method="post" class="form-grid"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int)($edit['id']??0) ?>">
    <label>Nombre completo<input name="nombre" maxlength="150" required value="<?= e($edit['nombre']??'') ?>"></label>
    <label>Cédula<input name="cedula" maxlength="30" required value="<?= e($edit['cedula']??'') ?>"></label>
    <label>Teléfono<input name="telefono" maxlength="30" required value="<?= e($edit['telefono']??'') ?>"></label>
    <div class="actions"><button class="btn primary" type="submit">Guardar usuario</button><?php if($edit): ?><a class="btn" href="usuarios.php">Cancelar</a><?php endif; ?></div>
  </form></section>
  <section class="card"><div class="card-head"><div><h2>Directorio</h2><p><?= count($users) ?> registro(s)</p></div><form class="search" method="get"><input name="q" placeholder="Buscar nombre, cédula..." value="<?= e($q) ?>"><button class="btn" type="submit">Buscar</button></form></div>
  <div class="table-wrap"><table><thead><tr><th>Nombre</th><th>Cédula</th><th>Teléfono</th><th>Estado</th><th></th></tr></thead><tbody>
  <?php foreach($users as $u): ?><tr><td><?= e($u['nombre']) ?></td><td><?= e($u['cedula']) ?></td><td><?= e($u['telefono']) ?></td><td><span class="badge <?= $u['activo']?'activo':'inactivo' ?>"><?= $u['activo']?'ACTIVO':'INACTIVO' ?></span></td><td class="row-actions"><a class="icon-btn" href="usuarios.php?editar=<?= $u['id'] ?>">Editar</a><form method="post"><input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>"><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= $u['id'] ?>"><button class="icon-btn" type="submit"><?= $u['activo']?'Desactivar':'Activar' ?></button></form></td></tr><?php endforeach; ?>
  <?php if(!$users): ?><tr><td colspan="5" class="empty">No se encontraron usuarios.</td></tr><?php endif; ?></tbody></table></div></section>
</div>
<?php include __DIR__ . '/footer.php'; ?>
