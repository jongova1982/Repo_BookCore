<?php
require_once __DIR__ . '/bootstrap.php';
require_login();
$tipo=$_GET['tipo']??'';$pdo=db();
$map=[
 'usuarios'=>['SELECT nombre,cedula,telefono,CASE WHEN activo=1 THEN "ACTIVO" ELSE "INACTIVO" END estado,created_at FROM usuarios ORDER BY nombre',['Nombre','Cédula','Teléfono','Estado','Creado']],
 'libros'=>['SELECT codigo,titulo,autor,unidades,CASE WHEN activo=1 THEN "ACTIVO" ELSE "INACTIVO" END estado,created_at FROM libros ORDER BY titulo',['Código','Título','Autor','Unidades','Estado','Creado']],
 'prestamos'=>['SELECT p.id,u.nombre usuario,p.fecha_prestamo,p.fecha_vencimiento,p.fecha_devolucion,p.estado,GROUP_CONCAT(CONCAT(l.titulo," x",d.cantidad) ORDER BY l.titulo SEPARATOR "; ") libros FROM prestamos p JOIN usuarios u ON u.id=p.usuario_id JOIN prestamo_detalle d ON d.prestamo_id=p.id JOIN libros l ON l.id=d.libro_id GROUP BY p.id ORDER BY p.id DESC',['ID','Usuario','Préstamo','Vencimiento','Devolución','Estado','Libros']]
];
if(!isset($map[$tipo])){http_response_code(404);exit('Exportación no disponible.');}$data=$pdo->query($map[$tipo][0]);$filename='biblioteca_'.$tipo.'_'.date('Ymd_His').'.csv';header('Content-Type:text/csv; charset=utf-8');header('Content-Disposition:attachment; filename="'.$filename.'"');$out=fopen('php://output','w');fwrite($out,"\xEF\xBB\xBF");fputcsv($out,$map[$tipo][1],';');foreach($data as $row)fputcsv($out,array_values($row),';');fclose($out);exit;
