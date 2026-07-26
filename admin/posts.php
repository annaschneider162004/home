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
        Database::delete('posts', 'id = ?', [(int)$_POST['id']]);
        setFlash('success', 'Đã xóa bài viết.');
        redirect(ADMIN_URL . '/posts.php');
    }

    $data = [
        'title'   => trim($_POST['title'] ?? ''),
        'slug'    => trim($_POST['slug'] ?? ''),
        'excerpt' => trim($_POST['excerpt'] ?? ''),
        'content' => trim($_POST['content'] ?? ''),
        'image'   => trim($_POST['image'] ?? ''),
        'type'    => $_POST['type'] ?? 'bai_viet',
        'status'  => $_POST['status'] ?? 'draft',
    ];
    if ($data['title'] === '') $errors[] = 'Tiêu đề không được để trống.';
    if ($data['slug'] === '') $data['slug'] = makeSlug($data['title']);

    if (empty($errors)) {
        if ($postAction === 'edit' && $id > 0) {
            Database::update('posts', $data, 'id = :id', ['id' => $id]);
            setFlash('success', 'Đã cập nhật bài viết.');
        } else {
            Database::insert('posts', $data);
            setFlash('success', 'Đã thêm bài viết mới.');
        }
        redirect(ADMIN_URL . '/posts.php');
    }
}

$post   = $id > 0 ? Database::fetchOne("SELECT * FROM posts WHERE id = ?", [$id]) : null;
$search = trim($_GET['q'] ?? '');
$where  = $search ? "title LIKE :q" : "1";
$params = $search ? ['q' => "%$search%"] : [];
$pg     = paginate('posts', $where, $params, (int)($_GET['p'] ?? 1), 12);
$posts  = Database::fetchAll("SELECT * FROM posts WHERE $where ORDER BY id DESC LIMIT {$pg['perPage']} OFFSET {$pg['offset']}", $params);

adminHead('Quản Lý Bài Viết');
?>

<div class="page-head">
  <div>
    <h1>Bài Viết &amp; Thư Viện</h1>
    <div class="breadcrumb"><a href="<?= e(ADMIN_URL) ?>/">Dashboard</a> › Bài Viết</div>
  </div>
  <?php if ($action === 'list'): ?>
    <a href="?action=create" class="btn btn-primary">+ Thêm Bài Viết</a>
  <?php else: ?>
    <a href="<?= e(ADMIN_URL) ?>/posts.php" class="btn btn-secondary">← Quay Lại</a>
  <?php endif; ?>
</div>

<?php renderFlash(); ?>

<?php if ($action === 'list'): ?>
<div class="card">
  <div class="card-header"><h3>Danh Sách Bài Viết (<?= $pg['total'] ?>)</h3></div>
  <div class="card-body">
    <form class="search-bar" method="GET">
      <div class="search-input-wrap">
        <span class="search-icon">🔍</span>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Tìm bài viết...">
      </div>
      <button type="submit" class="btn btn-secondary">Tìm</button>
      <?php if ($search): ?><a href="?" class="btn btn-secondary">Xóa lọc</a><?php endif; ?>
    </form>
    <div class="table-wrap">
      <table>
        <thead><tr><th>#</th><th>Tiêu Đề</th><th>Loại</th><th>Trạng Thái</th><th>Ngày Tạo</th><th>Thao Tác</th></tr></thead>
        <tbody>
          <?php if (empty($posts)): ?>
            <tr><td colspan="6" style="text-align:center;color:var(--text-light)">Chưa có bài viết nào.</td></tr>
          <?php else: foreach ($posts as $p): ?>
          <tr>
            <td><?= $p['id'] ?></td>
            <td>
              <strong><?= e($p['title']) ?></strong>
              <br><small style="color:var(--text-light)"><?= e($p['slug']) ?></small>
            </td>
            <td><span class="badge <?= $p['type']==='bai_viet'?'badge-blue':'badge-purple' ?>"><?= $p['type']==='bai_viet'?'Bài Viết':'Thư Viện' ?></span></td>
            <td><span class="badge <?= $p['status']==='published'?'badge-green':'badge-yellow' ?>"><?= $p['status']==='published'?'Đã Đăng':'Nháp' ?></span></td>
            <td><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
            <td>
              <div class="table-actions">
                <a href="?action=edit&id=<?= $p['id'] ?>" class="btn btn-warning btn-sm">✏️ Sửa</a>
                <form method="POST" style="display:inline" onsubmit="return confirm('Xác nhận xóa?')">
                  <?php csrfField(); ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= $p['id'] ?>">
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
  <div class="card-header"><h3><?= $action==='edit'?'✏️ Chỉnh Sửa Bài Viết':'+ Thêm Bài Viết Mới' ?></h3></div>
  <div class="card-body">
    <?php foreach ($errors as $err): ?><div class="alert alert-danger"><?= e($err) ?></div><?php endforeach; ?>
    <form method="POST">
      <?php csrfField(); ?>
      <input type="hidden" name="action" value="<?= $action==='edit'?'edit':'create' ?>">
      <?php if ($action==='edit'): ?><input type="hidden" name="id" value="<?= $id ?>"><?php endif; ?>

      <div class="form-row cols-2">
        <div class="form-group">
          <label class="form-label">Tiêu Đề <span class="required">*</span></label>
          <input type="text" name="title" class="form-control" required value="<?= e($post['title'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Slug (URL)</label>
          <input type="text" name="slug" class="form-control" value="<?= e($post['slug'] ?? '') ?>" placeholder="tu-dong-tao-tu-tieu-de">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Tóm Tắt</label>
        <textarea name="excerpt" class="form-control" rows="2"><?= e($post['excerpt'] ?? '') ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label">Nội Dung</label>
        <textarea name="content" class="form-control" rows="10"><?= e($post['content'] ?? '') ?></textarea>
        <div class="form-hint">Hỗ trợ HTML cơ bản.</div>
      </div>
      <div class="form-row cols-3">
        <div class="form-group">
          <label class="form-label">URL Ảnh Đại Diện</label>
          <input type="text" name="image" class="form-control" value="<?= e($post['image'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Loại</label>
          <select name="type" class="form-control">
            <option value="bai_viet" <?= ($post['type']??'bai_viet')==='bai_viet'?'selected':'' ?>>Bài Viết</option>
            <option value="thu_vien" <?= ($post['type']??'')==='thu_vien'?'selected':'' ?>>Thư Viện (Sheet nhạc...)</option>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Trạng Thái</label>
          <select name="status" class="form-control">
            <option value="published" <?= ($post['status']??'draft')==='published'?'selected':'' ?>>Đăng Ngay</option>
            <option value="draft" <?= ($post['status']??'draft')==='draft'?'selected':'' ?>>Lưu Nháp</option>
          </select>
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Lưu Bài Viết</button>
        <a href="<?= e(ADMIN_URL) ?>/posts.php" class="btn btn-secondary">Hủy</a>
      </div>
    </form>
  </div>
</div>
<?php endif; ?>

<?php adminFoot(); ?>
