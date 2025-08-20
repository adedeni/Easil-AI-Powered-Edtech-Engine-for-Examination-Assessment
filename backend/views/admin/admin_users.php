<?php
require_once '../../app/Core/init.php';

$user = Guard::requireAdmin();

$db = DB::getInstance();
$successMessage = '';
$errorMessage = '';

include '../../app/Core/constants.php';

if (!class_exists('Audit')) {
  require_once '../../app/Classes/Audit.php';
}

// Handle actions (POST)
if (Input::exists()) {
  if (Token::check(Input::get('token'))) {
    // Create user
    if (isset($_POST['action']) && $_POST['action'] === 'create') {
      $name = trim(Input::get('name'));
      $email = trim(Input::get('email'));
      $new_role_id = (int) Input::get('role_id');
      $ident = trim(Input::get('identification_number'));
      $department = trim(Input::get('department')); // Add this line

      $isCurrentSuper = ((int) $user->data()->id === $SUPER_ADMIN_ID);
      if ($new_role_id === 3 && !$isCurrentSuper) {
        $errorMessage = 'Only the Super Admin can create administrator accounts.';
      } else if ($name && $new_role_id && $ident) {
        $username = $ident;
        // Explicit duplicate checks
        if ($db->query('SELECT id FROM users WHERE username = ? LIMIT 1', [$username])->count() > 0) {
          $errorMessage = 'This identification number is already used as a username.';
        } else if ($email && $db->query('SELECT id FROM users WHERE email = ? LIMIT 1', [$email])->count() > 0) {
          $errorMessage = 'This email address is already in use.';
        } else if ($db->query('SELECT id FROM users WHERE role_id = ? AND identification_number = ? LIMIT 1', [$new_role_id, $ident])->count() > 0) {
          $errorMessage = 'This identification number already exists for the selected role.';
        } else {
          $salt = Hash::salt(32);
          $defaultPassword = (new User())->generateRandomPassword(12);
          $hashed = Hash::make($defaultPassword, $salt);
          try {
            $created = $user->create([
              'name' => $name,
              'email' => $email ?: null,
              'username' => $username,
              'password' => $hashed,
              'salt' => $salt,
              'role_id' => $new_role_id,
              'identification_number' => $ident,
              'department' => $department, // Add this line
              'created_at' => date('Y-m-d H:i:s'),
              'force_password_change' => 1,
              'status' => 'active'
            ]);
            if ($created) {
              $newUserId = $db->query('SELECT LAST_INSERT_ID() AS id')->first()->id ?? null;
              Audit::log((int) $user->data()->id, 'create_user', $newUserId ? (int) $newUserId : null, [
                'name' => $name,
                'role_id' => $new_role_id,
                'identification_number' => $ident
              ]);
              $successMessage = 'User created successfully. TEMP PASSWORD: ' . htmlspecialchars($defaultPassword);
              // NEW: Email notification for manual user creation
              if ($email) {
                $emailHandler = new Email();
                if ($emailHandler->sendWelcomeEmail($email, $name, $username, $defaultPassword)) {
                  $successMessage .= ' (Notification email sent)';
                } else {
                  $successMessage .= ' (Email notification failed)';
                }
              }
            } else {
              $errorMessage = 'Failed to create user: ' . htmlspecialchars($user->getLastError());
            }
          } catch (Exception $e) {
            $errorMessage = 'Failed to create user: ' . htmlspecialchars($e->getMessage());
          }
        }
      } else {
        if (!$errorMessage) {
          $errorMessage = 'Missing required fields.';
        }
      }
    }

    // Toggle status
    if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
      $targetId = (int) Input::get('user_id');
      if ($targetId) {
        if ($targetId === $SUPER_ADMIN_ID) {
          $errorMessage = 'The Super Admin account is protected and cannot be deactivated.';
        } else {
          $row = $db->get('users', ['id', '=', $targetId]);
          if ($row->count()) {
            $curr = $row->first();
            $newStatus = ($curr->status === 'active') ? 'inactive' : 'active';
            $user->update(['status' => $newStatus], $targetId);
            if ($newStatus === 'inactive') {
              $db->delete('users_session', ['users_id', '=', $targetId]);
            }
            Audit::log((int) $user->data()->id, 'toggle_status', $targetId, ['new_status' => $newStatus]);
            $successMessage = 'User status updated to ' . $newStatus;
          } else {
            $errorMessage = 'User not found.';
          }
        }
      } else {
        $errorMessage = 'Invalid user.';
      }
    }

    // Reset password (append email)
    if (isset($_POST['action']) && $_POST['action'] === 'reset_password') {
      $targetId = (int) Input::get('user_id');
      if ($targetId) {
        if ($targetId === $SUPER_ADMIN_ID) {
          $errorMessage = 'The Super Admin password cannot be reset from this page.';
        } else {
          $row = $db->get('users', ['id', '=', $targetId]);
          if ($row->count()) {
            $target = $row->first();
            $salt = Hash::salt(32);
            $newPwd = (new User())->generateRandomPassword(12);
            $user->update([
              'password' => Hash::make($newPwd, $salt),
              'salt' => $salt,
              'force_password_change' => 1
            ], $targetId);
            $db->delete('users_session', ['users_id', '=', $targetId]);
            Audit::log((int) $user->data()->id, 'reset_password', $targetId);
            $successMessage = 'Password reset. TEMP PASSWORD: ' . htmlspecialchars($newPwd);
            if (!empty($target->email)) {
              $emailHandler = new Email();
              if ($emailHandler->sendPasswordResetEmail($target->email, $target->name, $newPwd)) {
                $successMessage .= ' (Notification email sent)';
              } else {
                $successMessage .= ' (Email notification failed)';
              }
            }
          }
        }
      } else {
        $errorMessage = 'Invalid user.';
      }
    }

    // Edit user (name/email)
    if (isset($_POST['action']) && $_POST['action'] === 'edit_user') {
      $targetId = (int) Input::get('user_id');
      $name = trim(Input::get('edit_name'));
      $email = trim(Input::get('edit_email'));
      if ($targetId && $name) {
        try {
          $updateFields = ['name' => $name, 'email' => $email ?: null];
          $user->update($updateFields, $targetId);
          Audit::log((int) $user->data()->id, 'edit_user', $targetId, $updateFields);
          $successMessage = 'User details updated.';
        } catch (Exception $e) {
          $errorMessage = 'Failed to update user: ' . htmlspecialchars($e->getMessage());
        }
      } else {
        $errorMessage = 'Name is required.';
      }
    }

    // Unlock account
    if (isset($_POST['action']) && $_POST['action'] === 'unlock_user') {
      $targetId = (int) Input::get('user_id');
      if ($targetId) {
        if ($targetId === $SUPER_ADMIN_ID) {
          $errorMessage = 'The Super Admin account cannot be unlocked here.';
        } else {
          try {
            $user->update([
              'failed_login_attempts' => 0,
              'lock_until' => null,
            ], $targetId);
            Audit::log((int) $user->data()->id, 'unlock_user', $targetId);
            $successMessage = 'User account unlocked.';
          } catch (Exception $e) {
            $errorMessage = 'Failed to unlock account: ' . htmlspecialchars($e->getMessage());
          }
        }
      } else {
        $errorMessage = 'Invalid user.';
      }
    }

    // Import CSV (students/lecturers)
    if (isset($_POST['action']) && $_POST['action'] === 'import_csv') {
      $import_role_id = (int) Input::get('import_role_id');
      $doCommit = (Input::get('commit') === '1');

      // Only allow students (1) or lecturers (2)
      if (!in_array($import_role_id, [1, 2], true)) {
        $errorMessage = 'Only students or lecturers can be imported.';
      } else if (!isset($_FILES['csv_file']) || $_FILES['csv_file']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage = 'CSV file upload failed.';
      } else {
        $tmpPath = $_FILES['csv_file']['tmp_name'];
        $handle = fopen($tmpPath, 'r');
        if (!$handle) {
          $errorMessage = 'Unable to read uploaded file.';
        } else {
          $lineNo = 0;
          $headers = null;
          $rows = [];
          while (($data = fgetcsv($handle)) !== false) {
            $lineNo++;
            // Expect header in first row: name,identification_number,email
            if ($lineNo === 1) {
              $headers = array_map('strtolower', $data);
              continue;
            }
            $row = array_combine($headers, $data);
            if ($row === false) {
              continue;
            }
            $rows[] = $row;
          }
          fclose($handle);

          $errors = [];
          $validRows = [];
          $created = [];
          foreach ($rows as $idx => $r) {
            $rowNum = $idx + 2;  // account for header row
            $name = isset($r['name']) ? trim($r['name']) : '';
            $ident = isset($r['identification_number']) ? trim($r['identification_number']) : '';
            $email = isset($r['email']) ? trim($r['email']) : '';

            if ($name === '' || $ident === '') {
              $errors[] = "Row {$rowNum}: Missing required fields (name/identification_number).";
              continue;
            }
            // Username policy: equals identification_number
            $username = $ident;
            // Uniqueness checks
            $existsUser = $db->query('SELECT id FROM users WHERE username = ? LIMIT 1', [$username])->count() > 0;
            if ($existsUser) {
              $errors[] = "Row {$rowNum}: Username already exists (" . htmlspecialchars($username) . ').';
              continue;
            }
            $existsRoleIdent = $db->query('SELECT id FROM users WHERE role_id = ? AND identification_number = ? LIMIT 1', [$import_role_id, $ident])->count() > 0;
            if ($existsRoleIdent) {
              $errors[] = "Row {$rowNum}: Identification already exists for this role (" . htmlspecialchars($ident) . ').';
              continue;
            }
            if ($email !== '') {
              $existsEmail = $db->query('SELECT id FROM users WHERE email = ? LIMIT 1', [$email])->count() > 0;
              if ($existsEmail) {
                $errors[] = "Row {$rowNum}: Email already exists (" . htmlspecialchars($email) . ').';
                continue;
              }
            }
            $validRows[] = [
              'name' => $name,
              'email' => ($email !== '') ? $email : null,
              'username' => $username,
              'role_id' => $import_role_id,
              'identification_number' => $ident,
            ];
          }

          if (!$doCommit) {
            // Dry-run summary
            if (count($errors)) {
              $errorMessage = 'Dry-run: Found ' . count($errors) . ' issue(s). Please fix and retry.';
            } else {
              $successMessage = 'Dry-run passed. ' . count($validRows) . ' row(s) ready to import.';
            }
          } else {
            if (count($errors)) {
              $errorMessage = 'Import aborted. Resolve errors found during dry-run.';
            } else {
              // Commit inserts
              foreach ($validRows as $vr) {
                $salt = Hash::salt(32);
                $tempPwd = (new User())->generateRandomPassword(12);
                $hashed = Hash::make($tempPwd, $salt);
                $ok = $user->create([
                  'name' => $vr['name'],
                  'email' => $vr['email'],
                  'username' => $vr['username'],
                  'password' => $hashed,
                  'salt' => $salt,
                  'role_id' => $vr['role_id'],
                  'identification_number' => $vr['identification_number'],
                  'created_at' => date('Y-m-d H:i:s'),
                  'force_password_change' => 1,
                  'status' => 'active'
                ]);
                if ($ok) {
                  Audit::log((int) $user->data()->id, 'import_create_user', null, [
                    'name' => $vr['name'],
                    'role_id' => $vr['role_id'],
                    'identification_number' => $vr['identification_number']
                  ]);
                  $created[] = [
                    'username' => $vr['username'],
                    'name' => $vr['name'],
                    'email' => $vr['email'],
                    'role_id' => $vr['role_id'],
                    'identification_number' => $vr['identification_number'],
                    'temp_password' => $tempPwd,
                  ];
                  // NEW: Send welcome email to imported users
                  if ($vr['email']) {
                    $emailHandler->sendWelcomeEmail($vr['email'], $vr['name'], $vr['username'], $tempPwd);
                  }
                }
              }
              $successMessage = 'Import complete. Created ' . count($created) . ' user(s).';
              if (count($created) > 0) {
                $successMessage .= ' (Welcome emails sent to users with an email address)';
              }
            }
          }

          // Store last dry-run errors/created in session for display below
          Session::put('import_errors', $errors);
          Session::put('import_created', $created);
        }
      }
    }
  } else {
    $errorMessage = 'Invalid or expired form token. Please try again.';
  }
}

// Generate ONE token for all forms in this render
$formToken = Token::generate();
$importErrors = Session::exists('import_errors') ? Session::get('import_errors') : [];
$importCreated = Session::exists('import_created') ? Session::get('import_created') : [];
Session::delete('import_errors');
Session::delete('import_created');

// Filters (GET)
$filterRoleId = (int) Input::get('role_filter');
$filterStatus = trim(Input::get('status_filter'));
$q = trim(Input::get('q'));
$page = (int) Input::get('page');
if ($page < 1) {
  $page = 1;
}
$perPage = (int) Input::get('per_page');
if ($perPage < 1) {
  $perPage = 10;
}
if ($perPage > 100) {
  $perPage = 100;
}
$offset = ($page - 1) * $perPage;

// Fetch roles for filters and create form
$roles = $db->query('SELECT id, name FROM roles')->results();

// Build WHERE clause
$whereClauses = [];
$params = [];
if ($filterRoleId > 0) {
  $whereClauses[] = 'u.role_id = ?';
  $params[] = $filterRoleId;
}
if ($filterStatus === 'active' || $filterStatus === 'inactive') {
  $whereClauses[] = 'u.status = ?';
  $params[] = $filterStatus;
}
if ($q !== '') {
  $whereClauses[] = '(u.username LIKE ? OR u.name LIKE ? OR u.email LIKE ? OR u.identification_number LIKE ?)';
  $like = '%' . $q . '%';
  array_push($params, $like, $like, $like, $like);
}
$whereSql = count($whereClauses) ? ('WHERE ' . implode(' AND ', $whereClauses)) : '';

// Count total for pagination
$countSql = 'SELECT COUNT(*) AS total FROM users u ' . $whereSql;
$totalRow = $db->query($countSql, $params)->first();
$total = (int) $totalRow->total;
$totalPages = $total > 0 ? (int) ceil($total / $perPage) : 1;
if ($page > $totalPages) {
  $page = $totalPages;
  $offset = ($page - 1) * $perPage;
}

// Fetch users with filters and pagination
$dataSql = 'SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id ' . $whereSql . ' ORDER BY u.created_at DESC LIMIT ' . $perPage . ' OFFSET ' . $offset;
$users = $db->query($dataSql, $params)->results();

// Helper to preserve query params
function buildQuery($overrides = [])
{
  $base = [
    'role_filter' => Input::get('role_filter'),
    'status_filter' => Input::get('status_filter'),
    'q' => Input::get('q'),
    'per_page' => Input::get('per_page'),
  ];
  $all = array_merge($base, $overrides);
  $pairs = [];
  foreach ($all as $k => $v) {
    if ($v === '' || $v === null)
      continue;
    $pairs[] = urlencode($k) . '=' . urlencode($v);
  }
  return count($pairs) ? ('?' . implode('&', $pairs)) : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin - Manage Users</title>
</head>
<body>

<h1>Manage Users</h1>
<p><a href="admin_audit_logs.php">View Audit Logs</a> | <a href="admin_users_export.php<?php echo buildQuery(); ?>" target="_blank">Export Users CSV</a></p>
<?php if ($successMessage): ?>
  <p style="color: green; font-weight: 600;">✅ <?php echo $successMessage; ?></p>
<?php endif; ?>
<?php if ($errorMessage): ?>
  <p style="color: red; font-weight: 600;">❌ <?php echo $errorMessage; ?></p>
<?php endif; ?>

<h2>Filters</h2>
<form method="get" style="margin-bottom: 16px;">
  <div>
    <label>Role</label>
    <select name="role_filter">
      <option value="">All</option>
      <?php foreach ($roles as $r): ?>
        <option value="<?php echo (int) $r->id; ?>" <?php echo ($filterRoleId === (int) $r->id) ? 'selected' : ''; ?>><?php echo htmlspecialchars($r->name); ?></option>
      <?php endforeach; ?>
    </select>
    <label>Status</label>
    <select name="status_filter">
      <option value="" <?php echo ($filterStatus === '') ? 'selected' : ''; ?>>All</option>
      <option value="active" <?php echo ($filterStatus === 'active') ? 'selected' : ''; ?>>Active</option>
      <option value="inactive" <?php echo ($filterStatus === 'inactive') ? 'selected' : ''; ?>>Inactive</option>
    </select>
    <label>Search</label>
    <input type="text" name="q" value="<?php echo htmlspecialchars($q); ?>" placeholder="name/username/email/ID">
    <label>Per Page</label>
    <select name="per_page">
      <?php foreach ([10, 25, 50, 100] as $pp): ?>
        <option value="<?php echo $pp; ?>" <?php echo ($perPage === $pp) ? 'selected' : ''; ?>><?php echo $pp; ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">Apply</button>
  </div>
</form>

<h2>Create User</h2>
<form method="post">
  <input type="hidden" name="token" value="<?php echo $formToken; ?>">
  <input type="hidden" name="action" value="create">
  <div>
    <label>Name</label>
    <input type="text" name="name" required>
  </div>
  <div>
    <label>Email</label>
    <input type="email" name="email">
  </div>
  <div>
    <label>Role</label>
    <select name="role_id" required>
      <option value="">Select role</option>
      <?php foreach ($roles as $r): ?>
        <?php if ((int) $r->id === 3 && (int) $user->data()->id !== $SUPER_ADMIN_ID) continue; ?>
        <option value="<?php echo (int) $r->id; ?>"><?php echo htmlspecialchars($r->name); ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label>Identification Number</label>
    <input type="text" name="identification_number" required>
  </div>
  <div>
    <label for="department">Department:</label>
    <input type="text" name="department" id="department">
  </div>
  <button type="submit">Create</button>
</form>

<h2>Bulk Import (CSV)</h2>
<form method="post" enctype="multipart/form-data" style="margin-bottom: 16px;">
  <input type="hidden" name="token" value="<?php echo $formToken; ?>">
  <input type="hidden" name="action" value="import_csv">
  <div>
    <label>Import as</label>
    <select name="import_role_id" required>
      <option value="1">Students</option>
      <option value="2">Lecturers</option>
    </select>
  </div>
  <div>
    <label>Upload CSV file</label>
    <input type="file" name="csv_file" accept=".csv" required>
  </div>
  <div style="margin-top:8px;">
    <label>
      <input type="checkbox" name="commit" value="1">
      Proceed with import now
    </label>
    <div style="font-size: 0.9em; color:#374151; margin-top:4px;">
      - Leave this unchecked to run a Dry Run: we will check the file for errors without creating any accounts.<br>
      - Check this box to actually create the accounts after the checks pass (Commit).
    </div>
  </div>
  <p style="margin-top:8px;">CSV columns (first row must be the header): <code>name,identification_number,email</code>. Email is optional.</p>
  <button type="submit">Start</button>
</form>
<?php if (!empty($importErrors)): ?>
  <div style="color:#b91c1c; background:#fee2e2; padding:8px; margin-bottom:8px;">
    <strong>Issues found (<?php echo count($importErrors); ?>):</strong>
    <ul>
      <?php foreach ($importErrors as $e): ?>
        <li><?php echo htmlspecialchars($e); ?></li>
      <?php endforeach; ?>
    </ul>
    <p>Please fix these and try again. If no issues are listed, you can retry with "Proceed with import now" checked to create the accounts.</p>
  </div>
<?php endif; ?>
<?php if (!empty($importCreated)): ?>
  <div style="color:#065f46; background:#d1fae5; padding:8px; margin-bottom:8px;">
    <strong>Accounts created (<?php echo count($importCreated); ?>):</strong>
    <p>Share the temporary passwords with each user. They will be asked to set a new password on first login.</p>
    <table border="1" cellpadding="6" cellspacing="0">
      <thead>
        <tr>
          <th>Username</th><th>Name</th><th>Email</th><th>Role ID</th><th>Identification</th><th>Temp Password</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($importCreated as $c): ?>
          <tr>
            <td><?php echo htmlspecialchars($c['username']); ?></td>
            <td><?php echo htmlspecialchars($c['name']); ?></td>
            <td><?php echo htmlspecialchars($c['email']); ?></td>
            <td><?php echo (int) $c['role_id']; ?></td>
            <td><?php echo htmlspecialchars($c['identification_number']); ?></td>
            <td><?php echo htmlspecialchars($c['temp_password']); ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php endif; ?>

<?php
// Edit form (GET ?edit=ID)
$editId = (int) Input::get('edit');
$editRow = null;
if ($editId) {
  foreach ($users as $uRow) {
    if ((int) $uRow->id === $editId) {
      $editRow = $uRow;
      break;
    }
  }
  if (!$editRow) {
    // fetch directly if not in current page
    $res = $db->get('users', ['id', '=', $editId]);
    if ($res->count()) {
      $editRow = $res->first();
    }
  }
}
if ($editRow):
?>
<h2>Edit User</h2>
<form method="post" style="margin-bottom: 16px;">
  <input type="hidden" name="token" value="<?php echo $formToken; ?>">
  <input type="hidden" name="action" value="edit_user">
  <input type="hidden" name="user_id" value="<?php echo (int) $editRow->id; ?>">
  <div>
    <label>Name</label>
    <input type="text" name="edit_name" value="<?php echo htmlspecialchars($editRow->name); ?>" required>
  </div>
  <div>
    <label>Email</label>
    <input type="email" name="edit_email" value="<?php echo htmlspecialchars($editRow->email); ?>">
  </div>
  <button type="submit">Save</button>
  <a href="admin_users.php<?php echo buildQuery(); ?>">Cancel</a>
</form>
<?php endif; ?>

<h2>All Users</h2>
<table border="1" cellpadding="6" cellspacing="0">
  <thead>
    <tr>
      <th>ID</th><th>Username</th><th>Name</th><th>Email</th><th>Role</th><th>Identification</th><th>Status</th><th>Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $u): ?>
      <tr>
        <td><?php echo (int) $u->id; ?></td>
        <td><?php echo htmlspecialchars($u->username); ?></td>
        <td><?php echo htmlspecialchars($u->name); ?></td>
        <td><?php echo htmlspecialchars($u->email); ?></td>
        <td><?php echo htmlspecialchars($u->role_name); ?></td>
        <td><?php echo htmlspecialchars($u->identification_number); ?></td>
        <td>
          <?php echo htmlspecialchars($u->status); ?>
          <?php if (!empty($u->lock_until) && strtotime($u->lock_until) > time()): ?>
            <br><small style="color:#b91c1c;">Locked until <?php echo htmlspecialchars($u->lock_until); ?></small>
          <?php endif; ?>
        </td>
        <td>
          <a href="admin_users.php<?php echo buildQuery(['edit' => (int) $u->id]); ?>" style="margin-right:8px;">Edit</a>
          <?php if ((int) $u->id === $SUPER_ADMIN_ID): ?>
            <em>Protected</em>
          <?php else: ?>
            <form method="post" style="display:inline-block; margin-right:8px;">
              <input type="hidden" name="token" value="<?php echo $formToken; ?>">
              <input type="hidden" name="action" value="toggle_status">
              <input type="hidden" name="user_id" value="<?php echo (int) $u->id; ?>">
              <button type="submit"><?php echo ($u->status === 'active') ? 'Deactivate' : 'Activate'; ?></button>
            </form>
            <form method="post" style="display:inline-block; margin-right:8px;">
              <input type="hidden" name="token" value="<?php echo $formToken; ?>">
              <input type="hidden" name="action" value="reset_password">
              <input type="hidden" name="user_id" value="<?php echo (int) $u->id; ?>">
              <button type="submit">Reset Password</button>
            </form>
            <?php if ((int) $u->failed_login_attempts > 0 || (!empty($u->lock_until) && strtotime($u->lock_until) > time())): ?>
              <form method="post" style="display:inline-block;">
                <input type="hidden" name="token" value="<?php echo $formToken; ?>">
                <input type="hidden" name="action" value="unlock_user">
                <input type="hidden" name="user_id" value="<?php echo (int) $u->id; ?>">
                <button type="submit">Unlock</button>
              </form>
            <?php endif; ?>
          <?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>

<div style="margin-top:12px;">
  <?php if ($page > 1): ?>
    <a href="admin_users.php<?php echo buildQuery(['page' => $page - 1]); ?>">« Prev</a>
  <?php endif; ?>
  <span style="margin:0 8px;">Page <?php echo $page; ?> of <?php echo $totalPages; ?> (<?php echo $total; ?> users)</span>
  <?php if ($page < $totalPages): ?>
    <a href="admin_users.php<?php echo buildQuery(['page' => $page + 1]); ?>">Next »</a>
  <?php endif; ?>
</div>

<p><a href="<?php echo ((int) $user->data()->id === $SUPER_ADMIN_ID) ? 'super_admin_dashboard.php' : 'admin_dashboard.php'; ?>">Back to Dashboard</a></p>
</body>
</html>