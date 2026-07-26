<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
require_once __DIR__ . '/layout.php';

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

// Handle POST (create/edit/delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $postAction = $_POST['action'] ?? '';

    if ($postAction === 'delete') {
        $delId = (int)($_POST['id'] ?? 0);
        Database::delete('courses', 'id = ?', [$delId]);
        setFlash('success', 'Đã xóa khóa học.');
        redirect(ADMIN_URL . '/courses.php');
    }

    // Create / Edit
    $data = [
        'title'        => trim($_POST['title'] ?? ''),
        'slug'         => trim($_POST['slug'] ?? ''),
        'description'  => trim($_POST['description'] ?? ''),
        'short_desc'   => trim($_POST['short_desc'] ?? ''),
        'level'        => $_POST['level'] ?? 'co_ban',
        'teacher_id'   => (int)($_POST['teacher_id'] ?? 0) ?: null,
        'price'        => (float)($_POST['price'] ?? 0),
        'duration_weeks'    => (int)($_POST['duration_weeks'] ?? 0) ?: null,
        'lessons_per_week'  => (int)($_POST['lessons_per_week'] ?? 2),
        'image'        => trim($_POST['image'] ?? ''),
        'featured'     => isset($_POST['featured']) ? 1 : 0,
        'status'       => $_POST['status'] ?? 'active',
    ];

    if ($data['title'] === '') $errors[] = 'Tên khóa học không được để trống.';
    if ($data['slug'] === '') $data['slug'] = makeSlug($data['title']);

    if (empty($errors)) {
        if ($postAction === 'edit' && $id > 0) {
            Database::update('courses', $data, 'id = :id', ['id' => $id]);
            setFlash('success', 'Đã cập nhật khóa học.');
        } else {
            Database::insert('courses', $data);
            setFlash('success', 'Đã thêm khóa học mới.');
        }
        redirect(ADMIN_URL . '/courses.php');
    }
}

// Fetch data for forms/list
$teachers = Database::fetchAll("SELECT id, name FROM teachers WHERE status='active' ORDER BY name");
$course   = $id > 0 ? Database::fetchOne("SELECT * FROM courses WHERE id = ?", [$id]) : null;

// List with search & pagination
$search = trim($_GET['q'] ?? '');
$where  = $search ? "title LIKE :q" : "1";
$params = $search ? ['q' => "%$search%"] : [];
$pg     = paginate('courses', $where, $params, (int)($_GET['p'] ?? 1), 12);
$courses = Database::fetchAll(
    "SELECT c.*, t.name AS teacher_name FROM courses c LEFT JOIN teachers t ON c.teacher_id = t.id
     WHERE $where ORDER BY c.id DESC LIMIT {$pg['perPage']} OFFSET {$pg['offset']}",
    $params
);

adminHead('Quản Lý Khóa Học');
?>

<div class="page-head">
  <div>
    <h1>Khóa Học</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › Khóa Học</div>
  </div>
  <?php if ($action === 'list'): ?>
  <a href="?action=create" class="btn btn-primary">+ Thêm Khóa Học</a>
  <?php else: ?>
  <a href="<?= e(ADMIN_URL) ?>/courses.php" class="btn btn-secondary">← Quay Lại</a>
  <?php endif; ?>
</div>

<?php renderFlash(); ?>

<?php if ($action === 'list'): ?>
<!-- ===================== LIST ===================== -->
<div class="card">
  <div class="card-header">
    <h3>Danh Sách Khóa Học (<?= $pg['total'] ?>)</h3>
  </div>
  <div class="card-body">
    <form class="search-bar" method="GET">
      <div class="search-input-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Tìm khóa học...">
      </div>
      <button type="submit" class="btn btn-secondary">Tìm</button>
      <?php if ($search): ?><a href="?" class="btn btn-secondary">Xóa lọc</a><?php endif; ?>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Tên Khóa Học</th><th>Cấp Độ</th><th>Giảng Viên</th><th>Giá</th><th>Nổi Bật</th><th>Trạng Thái</th><th>Thao Tác</th></tr></thead>
        <tbody>
          <?php if (empty($courses)): ?>
            <tr><td colspan="8" style="text-align:center;color:var(--text-light)">Không có khóa học nào.</td></tr>
          <?php else: foreach ($courses as $c): ?>
          <tr>
            <td><?= $c['id'] ?></td>
            <td><strong><?= e($c['title']) ?></strong><br><small style="color:var(--text-light)"><?= e($c['slug']) ?></small></td>
            <td><span class="badge badge-<?= levelColor($c['level']) ?>"><?= levelLabel($c['level']) ?></span></td>
            <td><?= e($c['teacher_name'] ?? '—') ?></td>
            <td><?= formatPrice((float)$c['price']) ?></td>
            <td><?= $c['featured'] ? '⭐' : '—' ?></td>
            <td><span class="badge <?= $c['status']==='active'?'badge-green':'badge-gray' ?>"><?= $c['status']==='active'?'Đang mở':'Ẩn' ?></span></td>
            <td>
              <div class="table-actions">
                <a href="?action=edit&id=<?= $c['id'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Xác nhận xóa?')">
                  <?php csrfField(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $c['id'] ?>">
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
      <?php for ($p = 1; $p <= $pg['totalPages']; $p++): ?>
        <a href="?p=<?= $p ?><?= $search ? '&q='.urlencode($search) : '' ?>" class="<?= $p == $pg['page'] ? 'active' : '' ?>"><?= $p ?></a>
      <?php endfor; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<?php elseif ($action === 'create' || $action === 'edit'): ?>
<!-- ===================== FORM ===================== -->
<div class="card">
  <div class="card-header">
    <h3><?= $action === 'edit' ? '✏️ Chỉnh Sửa Khóa Học' : '+ Thêm Khóa Học Mới' ?></h3>
  </div>
  <div class="card-body">
    <?php foreach ($errors as $e): ?>
      <div class="alert alert-danger"><?= e($e) ?></div>
    <?php endforeach; ?>
    <form method="POST">
      <?php csrfField(); ?>
      <input type="hidden" name="action" value="<?= $action === 'edit' ? 'edit' : 'create' ?>">
      <?php if ($action === 'edit'): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Tên Khóa Học <span class="required">*</span></label>
          <input type="text" name="title" class="form-control" required
                 value="<?= e($course['title'] ?? ($_POST['title'] ?? '')) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Slug (URL)</label>
          <input type="text" name="slug" class="form-control"
                 value="<?= e($course['slug'] ?? ($_POST['slug'] ?? '')) ?>"
                 placeholder="tu-dong-tao-tu-ten">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label">Mô Tả Ngắn</label>
        <input type="text" name="short_desc" class="form-control" maxlength="500"
               value="<?= e($course['short_desc'] ?? ($_POST['short_desc'] ?? '')) ?>">
      </div>

      <div class="form-group">
        <label class="form-label">Mô Tả Đầy Đủ</label>
        <textarea name="description" class="form-control" rows="5"><?= e($course['description'] ?? ($_POST['description'] ?? '')) ?></textarea>
      </div>

      <div class="form-row cols-3">
        <div class="form-group">
          <label class="form-label">Cấp Độ</label>
          <select name="level" class="form-control">
            <?php foreach (['co_ban'=>'Cơ Bản','trung_cap'=>'Trung Cấp','nang_cao'=>'Nâng Cao','chuyen_nghiep'=>'Chuyên Nghiệp'] as $v=>$l): ?>
            <option value="<?= $v ?>" <?= ($course['level'] ?? 'co_ban') === $v ? 'selected' : '' ?>><?= $l ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Giảng Viên</label>
          <select name="teacher_id" class="form-control">
            <option value="">— Chọn giảng viên —</option>
            <?php foreach ($teachers as $t): ?>
            <option value="<?= $t['id'] ?>" <?= ($course['teacher_id'] ?? 0) == $t['id'] ? 'selected' : '' ?>><?= e($t['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Học Phí (₫)</label>
          <input type="number" name="price" class="form-control" min="0" step="1000"
                 value="<?= $course['price'] ?? 0 ?>">
        </div>
      </div>

      <div class="form-row cols-3">
        <div class="form-group">
          <label class="form-label">Số Tuần Học</label>
          <input type="number" name="duration_weeks" class="form-control" min="1"
                 value="<?= $course['duration_weeks'] ?? '' ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Buổi / Tuần</label>
          <input type="number" name="lessons_per_week" class="form-control" min="1" max="7"
                 value="<?= $course['lessons_per_week'] ?? 2 ?>">
        </div>
        <div class="form-group">
          <label class="form-label">URL Ảnh Đại Diện</label>
          <input type="text" name="image" class="form-control"
                 value="<?= e($course['image'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Trạng Thái</label>
          <select name="status" class="form-control">
            <option value="active" <?= ($course['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Đang Mở</option>
            <option value="inactive" <?= ($course['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Ẩn</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">&nbsp;</label>
          <label style="display:flex;align-items:center;gap:.5rem;padding:.6rem 0;cursor:pointer">
            <input type="checkbox" name="featured" value="1" <?= ($course['featured'] ?? 0) ? 'checked' : '' ?>>
            ⭐ Khóa học nổi bật (hiển thị trang chủ)
          </label>
        </div>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu Khóa Học</button>
        <a href="<?= e(ADMIN_URL) ?>/courses.php" class="btn btn-secondary">Hủy</a>
      </div>
    </form>
  </div>
</div>

<?php endif; ?>

<?php adminFoot(); ?>
