<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
require_once __DIR__ . '/layout.php';

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        Database::delete('students', 'id = ?', [(int)$_POST['id']]);
        setFlash('success', 'Đã xóa học viên.');
        redirect(ADMIN_URL . '/students.php');
    }

    $data = [
        'name'          => trim($_POST['name'] ?? ''),
        'email'         => trim($_POST['email'] ?? ''),
        'phone'         => trim($_POST['phone'] ?? ''),
        'date_of_birth' => trim($_POST['date_of_birth'] ?? '') ?: null,
        'address'       => trim($_POST['address'] ?? ''),
        'course_id'     => (int)($_POST['course_id'] ?? 0) ?: null,
        'status'        => $_POST['status'] ?? 'active',
        'notes'         => trim($_POST['notes'] ?? ''),
    ];
    if ($data['name'] === '')  $errors[] = 'Họ tên không được để trống.';
    if ($data['email'] === '') $errors[] = 'Email không được để trống.';

    if (empty($errors)) {
        if ($postAction === 'edit' && $id > 0) {
            Database::update('students', $data, 'id = :id', ['id' => $id]);
            setFlash('success', 'Đã cập nhật học viên.');
        } else {
            try {
                Database::insert('students', $data);
                setFlash('success', 'Đã thêm học viên mới.');
            } catch (PDOException $e) {
                $errors[] = 'Email này đã được đăng ký.';
            }
        }
        if (empty($errors)) redirect(ADMIN_URL . '/students.php');
    }
}

$courses = Database::fetchAll("SELECT id, title FROM courses WHERE status='active' ORDER BY title");
$student = $id > 0 ? Database::fetchOne("SELECT * FROM students WHERE id = ?", [$id]) : null;
$search  = trim($_GET['q'] ?? '');
$where   = $search ? "(name LIKE :q OR email LIKE :q2)" : "1";
$params  = $search ? ['q' => "%$search%", 'q2' => "%$search%"] : [];
$pg      = paginate('students', $where, $params, (int)($_GET['p'] ?? 1), 12);
$students = Database::fetchAll(
    "SELECT s.*, c.title AS course_title FROM students s
     LEFT JOIN courses c ON s.course_id = c.id
     WHERE $where ORDER BY s.enrolled_at DESC LIMIT {$pg['perPage']} OFFSET {$pg['offset']}",
    $params
);

adminHead('Quản Lý Học Viên');
?>

<div class="page-head">
  <div>
    <h1>Học Viên</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › Học Viên</div>
  </div>
  <?php if ($action === 'list'): ?>
    <a href="?action=create" class="btn btn-primary">+ Thêm Học Viên</a>
  <?php else: ?>
    <a href="<?= e(ADMIN_URL) ?>/students.php" class="btn btn-secondary">← Quay Lại</a>
  <?php endif; ?>
</div>

<?php renderFlash(); ?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header"><h3>Danh Sách Học Viên (<?= $pg['total'] ?>)</h3></div>
  <div class="card-body">
    <form class="search-bar" method="GET">
      <div class="search-input-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Tìm tên hoặc email...">
      </div>
      <button type="submit" class="btn btn-secondary">Tìm</button>
      <?php if ($search): ?><a href="?" class="btn btn-secondary">Xóa lọc</a><?php endif; ?>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Họ Tên</th><th>Email</th><th>SĐT</th><th>Khóa Học</th><th>Trạng Thái</th><th>Ngày Đăng Ký</th><th>Thao Tác</th></tr></thead>
        <tbody>
          <?php if (empty($students)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-light)">Không có học viên nào.</td></tr>
          <?php else: foreach ($students as $s):
            $statusMap = ['active'=>['green','Đang học'],'inactive'=>['gray','Tạm dừng'],'graduated'=>['blue','Tốt nghiệp']];
            [$cls,$lbl] = $statusMap[$s['status']] ?? ['gray','—'];
          ?>
          <tr>
            <td><?= $s['id'] ?></td>
            <td><strong><?= e($s['name']) ?></strong></td>
            <td><?= e($s['email']) ?></td>
            <td><?= e($s['phone'] ?? '—') ?></td>
            <td><?= e($s['course_title'] ?? '—') ?></td>
            <td><span class="badge badge-<?= $cls ?>"><?= $lbl ?></span></td>
            <td><?= date('d/m/Y', strtotime($s['enrolled_at'])) ?></td>
            <td>
              <div class="table-actions">
                <a href="?action=edit&id=<?= $s['id'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Xác nhận xóa?')">
                  <?php csrfField(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $s['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
    <?php if ($pg['totalPages'] > 1): ?>
    <div class="pagination">
      <?php for ($p=1;$p<=$pg['totalPages'];$p++): ?>
        <a href="?p=<?= $p ?><?= $search?'&q='.urlencode($search):'' ?>" class="<?= $p==$pg['page']?'active':'' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
<div class="card">
  <div class="card-header"><h3><?= $action==='edit'?'✏️ Chỉnh Sửa Học Viên':'+ Thêm Học Viên Mới' ?></h3></div>
  <div class="card-body">
    <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
    <form method="POST">
      <?php csrfField(); ?>
      <input type="hidden" name="action" value="<?= $action==='edit'?'edit':'create' ?>">
      <?php if ($action==='edit'): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Họ Tên <span class="required">*</span></label>
          <input type="text" name="name" class="form-control" required value="<?= e($student['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Email <span class="required">*</span></label>
          <input type="email" name="email" class="form-control" required value="<?= e($student['email'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row cols-3">
        <div class="form-group">
          <label class="form-label">Số Điện Thoại</label>
          <input type="text" name="phone" class="form-control" value="<?= e($student['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Ngày Sinh</label>
          <input type="date" name="date_of_birth" class="form-control" value="<?= e($student['date_of_birth'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Khóa Học Đăng Ký</label>
          <select name="course_id" class="form-control">
            <option value="">— Chọn khóa học —</option>
            <?php foreach ($courses as $c): ?>
            <option value="<?= $c['id'] ?>" <?= ($student['course_id'] ?? 0) == $c['id'] ? 'selected' : '' ?>><?= e($c['title']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Địa Chỉ</label>
        <input type="text" name="address" class="form-control" value="<?= e($student['address'] ?? '') ?>">
      </div>
      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Trạng Thái</label>
          <select name="status" class="form-control">
            <option value="active" <?= ($student['status'] ?? 'active')==='active'?'selected':'' ?>>Đang Học</option>
            <option value="inactive" <?= ($student['status']??'')==='inactive'?'selected':'' ?>>Tạm Dừng</option>
            <option value="graduated" <?= ($student['status']??'')==='graduated'?'selected':'' ?>>Đã Tốt Nghiệp</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Ghi Chú</label>
          <textarea name="notes" class="form-control" rows="2"><?= e($student['notes'] ?? '') ?></textarea>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu</button>
        <a href="<?= e(ADMIN_URL) ?>/students.php" class="btn btn-secondary">Hủy</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php adminFoot(); ?>
