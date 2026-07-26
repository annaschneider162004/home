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
        Database::delete('teachers', 'id = ?', [(int)$_POST['id']]);
        setFlash('success', 'Đã xóa giảng viên.');
        redirect(ADMIN_URL . '/teachers.php');
    }

    $data = [
        'name'             => trim($_POST['name'] ?? ''),
        'email'            => trim($_POST['email'] ?? ''),
        'phone'            => trim($_POST['phone'] ?? ''),
        'bio'              => trim($_POST['bio'] ?? ''),
        'speciality'       => trim($_POST['speciality'] ?? ''),
        'image'            => trim($_POST['image'] ?? ''),
        'experience_years' => (int)($_POST['experience_years'] ?? 0),
        'status'           => $_POST['status'] ?? 'active',
    ];
    if ($data['name'] === '') $errors[] = 'Tên giảng viên không được để trống.';

    if (empty($errors)) {
        if ($postAction === 'edit' && $id > 0) {
            Database::update('teachers', $data, 'id = :id', ['id' => $id]);
            setFlash('success', 'Đã cập nhật giảng viên.');
        } else {
            Database::insert('teachers', $data);
            setFlash('success', 'Đã thêm giảng viên mới.');
        }
        redirect(ADMIN_URL . '/teachers.php');
    }
}

$teacher = $id > 0 ? Database::fetchOne("SELECT * FROM teachers WHERE id = ?", [$id]) : null;
$search  = trim($_GET['q'] ?? '');
$where   = $search ? "name LIKE :q" : "1";
$params  = $search ? ['q' => "%$search%"] : [];
$pg      = paginate('teachers', $where, $params, (int)($_GET['p'] ?? 1), 10);
$teachers = Database::fetchAll("SELECT * FROM teachers WHERE $where ORDER BY name LIMIT {$pg['perPage']} OFFSET {$pg['offset']}", $params);

adminHead('Quản Lý Giảng Viên');
?>

<div class="page-head">
  <div>
    <h1>Giảng Viên</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › Giảng Viên</div>
  </div>
  <?php if ($action === 'list'): ?>
    <a href="?action=create" class="btn btn-primary">+ Thêm Giảng Viên</a>
  <?php else: ?>
    <a href="<?= e(ADMIN_URL) ?>/teachers.php" class="btn btn-secondary">← Quay Lại</a>
  <?php endif; ?>
</div>

<?php renderFlash(); ?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header"><h3>Danh Sách Giảng Viên (<?= $pg['total'] ?>)</h3></div>
  <div class="card-body">
    <form class="search-bar" method="GET">
      <div class="search-input-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Tìm giảng viên...">
      </div>
      <button type="submit" class="btn btn-secondary">Tìm</button>
      <?php if ($search): ?><a href="?" class="btn btn-secondary">Xóa lọc</a><?php endif; ?>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Họ Tên</th><th>Chuyên Môn</th><th>Email</th><th>Kinh Nghiệm</th><th>Trạng Thái</th><th>Thao Tác</th></tr></thead>
        <tbody>
          <?php if (empty($teachers)): ?>
            <tr><td colspan="7" style="text-align:center;color:var(--text-light)">Không có giảng viên nào.</td></tr>
          <?php else: foreach ($teachers as $t): ?>
          <tr>
            <td><?= $t['id'] ?></td>
            <td><strong><?= e($t['name']) ?></strong></td>
            <td><?= e($t['speciality'] ?? '—') ?></td>
            <td><?= e($t['email'] ?? '—') ?></td>
            <td><?= $t['experience_years'] ?> năm</td>
            <td><span class="badge <?= $t['status']==='active'?'badge-green':'badge-gray' ?>"><?= $t['status']==='active'?'Đang dạy':'Không hoạt động' ?></span></td>
            <td>
              <div class="table-actions">
                <a href="?action=edit&id=<?= $t['id'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Xác nhận xóa?')">
                  <?php csrfField(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $t['id'] ?>">
                  <button type="submit" class="btn btn-danger btn-sm">🗑️ Xóa</button>
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
      <?php for ($p=1; $p<=$pg['totalPages']; $p++): ?>
        <a href="?p=<?= $p ?><?= $search?'&q='.urlencode($search):'' ?>" class="<?= $p==$pg['page']?'active':'' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
<div class="card">
  <div class="card-header"><h3><?= $action==='edit'?'✏️ Chỉnh Sửa Giảng Viên':'+ Thêm Giảng Viên Mới' ?></h3></div>
  <div class="card-body">
    <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
    <form method="POST">
      <?php csrfField(); ?>
      <input type="hidden" name="action" value="<?= $action==='edit'?'edit':'create' ?>">
      <?php if ($action==='edit'): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Họ Tên <span class="required">*</span></label>
          <input type="text" name="name" class="form-control" required value="<?= e($teacher['name'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Email</label>
          <input type="email" name="email" class="form-control" value="<?= e($teacher['email'] ?? '') ?>">
        </div>
      </div>
      <div class="form-row cols-3">
        <div class="form-group">
          <label class="form-label">Số Điện Thoại</label>
          <input type="text" name="phone" class="form-control" value="<?= e($teacher['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Chuyên Môn</label>
          <input type="text" name="speciality" class="form-control" value="<?= e($teacher['speciality'] ?? '') ?>" placeholder="Piano, Guitar...">
        </div>
        <div class="form-group">
          <label class="form-label">Kinh Nghiệm (năm)</label>
          <input type="number" name="experience_years" class="form-control" min="0" value="<?= $teacher['experience_years'] ?? 0 ?>">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Tiểu Sử</label>
        <textarea name="bio" class="form-control" rows="4"><?= e($teacher['bio'] ?? '') ?></textarea>
      </div>
      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">URL Ảnh</label>
          <input type="text" name="image" class="form-control" value="<?= e($teacher['image'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Trạng Thái</label>
          <select name="status" class="form-control">
            <option value="active" <?= ($teacher['status'] ?? 'active')==='active'?'selected':'' ?>>Đang Dạy</option>
            <option value="inactive" <?= ($teacher['status'] ?? '')==='inactive'?'selected':'' ?>>Không Hoạt Động</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu</button>
        <a href="<?= e(ADMIN_URL) ?>/teachers.php" class="btn btn-secondary">Hủy</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php adminFoot(); ?>
