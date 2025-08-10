<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Audit Logs</title>
</head>
<body>
<?php
require_once '../../app/Core/init.php';
$user = new User();
if(!$user->isLoggedIn()) { Redirect::to('../auth/login.php'); }
$roleId = isset($user->data()->role_id) ? (int)$user->data()->role_id : (int)$user->data()->roles;
if ($roleId !== 3) { die('Access denied'); }

$db = DB::getInstance();

$actor = trim(Input::get('actor'));
$target = trim(Input::get('target'));
$action = trim(Input::get('action'));
$page = (int)Input::get('page'); if ($page < 1) $page = 1;
$perPage = (int)Input::get('per_page'); if ($perPage < 1) $perPage = 25; if ($perPage > 200) $perPage = 200;
$offset = ($page - 1) * $perPage;

$where = [];
$params = [];
if ($actor !== '') { $where[] = 'l.actor_user_id = ?'; $params[] = (int)$actor; }
if ($target !== '') { $where[] = 'l.target_user_id = ?'; $params[] = (int)$target; }
if ($action !== '') { $where[] = 'l.action = ?'; $params[] = $action; }
$whereSql = count($where) ? ('WHERE '.implode(' AND ', $where)) : '';

$countSql = 'SELECT COUNT(*) AS total FROM audit_logs l '.$whereSql;
$total = (int)$db->query($countSql, $params)->first()->total;
$totalPages = $total ? (int)ceil($total / $perPage) : 1;
if ($page > $totalPages) { $page = $totalPages; $offset = ($page - 1) * $perPage; }

$sql = 'SELECT l.*, a.username AS actor_username, t.username AS target_username
        FROM audit_logs l
        LEFT JOIN users a ON a.id = l.actor_user_id
        LEFT JOIN users t ON t.id = l.target_user_id
        '.$whereSql.'
        ORDER BY l.created_at DESC
        LIMIT '.$perPage.' OFFSET '.$offset;
$logs = $db->query($sql, $params)->results();

function buildQueryLogs($overrides = []) {
  $base = [
    'actor' => Input::get('actor'),
    'target' => Input::get('target'),
    'action' => Input::get('action'),
    'per_page' => Input::get('per_page'),
  ];
  $all = array_merge($base, $overrides);
  $pairs = [];
  foreach ($all as $k => $v) { if ($v === '' || $v === null) continue; $pairs[] = urlencode($k).'='.urlencode($v); }
  return count($pairs) ? ('?'.implode('&', $pairs)) : '';
}
?>
<h1>Audit Logs</h1>
<p><a href="admin_users.php">Back to Manage Users</a></p>

<form method="get" style="margin-bottom: 12px;">
  <label>Actor User ID</label>
  <input type="number" name="actor" value="<?php echo htmlspecialchars($actor); ?>">
  <label>Target User ID</label>
  <input type="number" name="target" value="<?php echo htmlspecialchars($target); ?>">
  <label>Action</label>
  <input type="text" name="action" value="<?php echo htmlspecialchars($action); ?>" placeholder="e.g., create_user">
  <label>Per Page</label>
  <select name="per_page">
    <?php foreach ([25,50,100,200] as $pp): ?>
      <option value="<?php echo $pp; ?>" <?php echo ($perPage === $pp) ? 'selected' : ''; ?>><?php echo $pp; ?></option>
    <?php endforeach; ?>
  </select>
  <button type="submit">Filter</button>
</form>

<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>ID</th><th>When</th><th>Action</th><th>Actor</th><th>Target</th><th>Details</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($logs as $l): ?>
      <tr>
        <td><?php echo (int)$l->id; ?></td>
        <td><?php echo htmlspecialchars($l->created_at); ?></td>
        <td><?php echo htmlspecialchars($l->action); ?></td>
        <td><?php echo (int)$l->actor_user_id; ?> (<?php echo htmlspecialchars($l->actor_username); ?>)</td>
        <td><?php echo $l->target_user_id !== null ? (int)$l->target_user_id : '-'; ?> (<?php echo htmlspecialchars($l->target_username ?? '-'); ?>)</td>
        <td><pre style="margin:0; white-space:pre-wrap;"><?php echo htmlspecialchars($l->details); ?></pre></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div style="margin-top:12px;">
  <?php if ($page > 1): ?>
    <a href="admin_audit_logs.php<?php echo buildQueryLogs(['page' => $page - 1]); ?>">« Prev</a>
  <?php endif; ?>
  <span style="margin:0 8px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $total; ?> logs)</span>
  <?php if ($page < $totalPages): ?>
    <a href="admin_audit_logs.php<?php echo buildQueryLogs(['page' => $page + 1]); ?>">Next »</a>
  <?php endif; ?>
</div>
</body>
</html>