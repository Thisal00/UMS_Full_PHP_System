<?php
require_once "../includes/auth.php";
require_role('admin');
require_once "../db.php";

$msg = "";
$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    die("Invalid user ID");
}

// LOAD USER
$stmt = $mysqli->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User not found");
}

// UPDATE USER
if (isset($_POST['update_user'])) {

    $full_name = trim($_POST['full_name']);
    $email     = trim($_POST['email']);
    $role      = trim($_POST['role']);
    $status    = trim($_POST['status']);

    $stmt = $mysqli->prepare("
        UPDATE users 
        SET full_name=?, email=?, role=?, status=? 
        WHERE id=?
    ");
    $stmt->bind_param("ssssi", $full_name, $email, $role, $status, $id);

    if ($stmt->execute()) {

        // UPDATE JSON API
        $apiURL = "http://localhost/UMS_Full_PHP_System/user_api/save_user.php";
        $payload = [
            "id"     => $id,
            "name"   => $full_name,
            "email"  => $email,
            "role"   => $role,
            "status" => $status
        ];

        $ch = curl_init($apiURL);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        curl_close($ch);

        $msg = "✔ User updated successfully";
    } else {
        $msg = "❌ Error: " . $mysqli->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Edit User</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container mt-4">

<h2 class="fw-bold">Edit User</h2>

<?php if ($msg): ?>
<div class="alert alert-info"><?= $msg ?></div>
<?php endif; ?>

<div class="card p-4 shadow-sm">

<form method="POST" class="row g-3">

    <div class="col-md-6">
        <label class="form-label fw-bold">Full Name</label>
        <input type="text" name="full_name" class="form-control" 
               value="<?= $user['full_name'] ?>" required>
    </div>

    <div class="col-md-6">
        <label class="form-label fw-bold">Email</label>
        <input type="email" name="email" class="form-control"
               value="<?= $user['email'] ?>" required>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Role</label>
        <select name="role" class="form-select">
            <option value="admin"   <?= $user['role']=='admin'?'selected':'' ?>>Admin</option>
            <option value="cashier" <?= $user['role']=='cashier'?'selected':'' ?>>Cashier</option>
            <option value="reader"  <?= $user['role']=='reader'?'selected':'' ?>>Meter Reader</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label fw-bold">Status</label>
        <select name="status" class="form-select">
            <option value="active"   <?= $user['status']=='active'?'selected':'' ?>>Active</option>
            <option value="disabled" <?= $user['status']=='disabled'?'selected':'' ?>>Disabled</option>
        </select>
    </div>

    <div class="col-12">
        <button name="update_user" class="btn btn-primary w-100 fw-bold">
            Save Changes
        </button>
    </div>

</form>

<a href="../users.php" class="btn btn-secondary mt-3">Back</a>

</div>
</div>

</body>
</html>
