<?php
require_once 'core/init.php';
require_once 'classes/Guard.php';
$user = Guard::requireAdmin();
$db = DB::getInstance();

$filterRoleId = (int)Input::get('role_filter');
$filterStatus = trim(Input::get('status_filter'));
$q = trim(Input::get('q'));

$whereClauses = [];
$params = [];
if ($filterRoleId > 0) { $whereClauses[] = 'u.role_id = ?'; $params[] = $filterRoleId; }
if ($filterStatus === 'active' || $filterStatus === 'inactive') { $whereClauses[] = 'u.status = ?'; $params[] = $filterStatus; }
if ($q !== '') {
  $whereClauses[] = '(u.username LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.identification_number LIKE ?)';
  $like = '%'.$q.'%';
  array_push($params, $like, $like, $like, $like);
}
$whereSql = count($whereClauses) ? ('WHERE '.implode(' AND ', $whereClauses)) : '';

$sql = 'SELECT u.id, u.username, u.name, u.email, r.name AS role_name, u.identification_number, u.status, u.created_at
        FROM users u JOIN roles r ON r.id = u.role_id '.$whereSql.' ORDER BY u.created_at DESC';
$rows = $db->query($sql, $params)->results();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="users_export_'.date('Ymd_His').'.csv"');
$out = fopen('php://output', 'w');
fputcsv($out, ['id','username','name','email','role','identification_number','status','created_at']);
foreach ($rows as $r) {
  fputcsv($out, [(int)$r->id, $r->username, $r->name, $r->email, $r->role_name, $r->identification_number, $r->status, $r->created_at]);
}
fclose($out);
exit;