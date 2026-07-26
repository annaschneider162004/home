<?php
require_once __DIR__ . '/bootstrap.php';
requireLogin();
require_once __DIR__ . '/layout.php';

$totalCourses  = (int)Database::fetchScalar("SELECT COUNT(*) FROM courses WHERE status='active'");
$totalTeachers = (int)Database::fetchScalar("SELECT COUNT(*) FROM teachers WHERE status='active'");
$totalStudents = (int)Database::fetchScalar("SELECT COUNT(*) FROM students");
$totalPosts    = (int)Database::fetchScalar("SELECT COUNT(*) FROM posts WHERE status='published'");

$recentStudents = Database::fetchAll("SELECT s.*, c.title AS course_title FROM students s LEFT JOIN courses c ON s.course_id = c.id ORDER BY s.enrolled_at DESC LIMIT 5");

adminHead('Dashboard');
?>

<div class="page-head">
  <div>
    <h1>Dashboard</h1>
    <div class="breadcrumb">Tổng quan hệ thống</div>
  </div>
  <a href="<?= e(ADMIN_URL) ?>/courses.php?action=create" class="btn btn-primary">+ Thêm Khóa Học</a>
</div>

<?php renderFlash(); ?>

<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-icon">🎹</div>
    <div class="stat-info">
      <strong><?= $totalCourses ?></strong>
      <span>Khóa Học Đang Mở</span>
    </div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon">👨‍🏫</div>
    <div class="stat-info">
      <strong><?= $totalTeachers ?></strong>
      <span>Giảng Viên</span>
    </div>
  </div>
  <div class="stat-card gold">
    <div class="stat-icon">🎓</div>
    <div class="stat-info">
      <strong><?= $totalStudents ?></strong>
      <span>Học Viên</span>
    </div>
  </div>
  <div class="stat-card purple">
    <div class="stat-icon">📝</div>
    <div class="stat-info">
      <strong><?= $totalPosts ?></strong>
      <span>Bài Viết Đã Đăng</span>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header">
    <h3>🎓 Học Viên Đăng Ký Gần Đây</h3>
    <a href="<?= e(ADMIN_URL) ?>/students.php" class="btn btn-secondary btn-sm">Xem Tất Cả</a>
  </div>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Họ Tên</th>
          <th>Email</th>
          <th>Khóa Học</th>
          <th>Trạng Thái</th>
          <th>Ngày Đăng Ký</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($recentStudents)): ?>
        <tr><td colspan="6" style="text-align:center;color:var(--text-light)">Chưa có học viên nào.</td></tr>
        <?php else: foreach ($recentStudents as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td><strong><?= e($s['name']) ?></strong></td>
          <td><?= e($s['email']) ?></td>
          <td><?= e($s['course_title'] ?? '—') ?></td>
          <td>
            <?php
              $statusMap = ['active'=>['green','Đang học'],'inactive'=>['gray','Tạm dừng'],'graduated'=>['blue','Tốt nghiệp']];
              [$cls,$lbl] = $statusMap[$s['status']] ?? ['gray',$s['status']];
            ?>
            <span class="badge badge-<?= $cls ?>"><?= $lbl ?></span>
          </td>
          <td><?= date('d/m/Y', strtotime($s['enrolled_at'])) ?></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php adminFoot(); ?>
