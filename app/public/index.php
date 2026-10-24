<?php
declare(strict_types=1);

require_once __DIR__ . '/../config/database.php';

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

$error = '';
try {
    $pdo = db();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $action = $_POST['action'] ?? '';

        if ($action === 'add') {
            $title = trim((string)($_POST['title'] ?? ''));
            $description = trim((string)($_POST['description'] ?? ''));

            if ($title === '') {
                $error = 'Vui lòng nhập tên công việc.';
            } elseif (mb_strlen($title) > 200) {
                $error = 'Tên công việc không được vượt quá 200 ký tự.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO tasks (title, description) VALUES (:title, :description)');
                $stmt->execute([
                    'title' => $title,
                    'description' => $description !== '' ? $description : null,
                ]);
                header('Location: /');
                exit;
            }
        } elseif ($action === 'toggle') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = $pdo->prepare('UPDATE tasks SET is_done = IF(is_done = 1, 0, 1) WHERE id = :id');
                $stmt->execute(['id' => $id]);
            }
            header('Location: /');
            exit;
        } elseif ($action === 'delete') {
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if ($id) {
                $stmt = $pdo->prepare('DELETE FROM tasks WHERE id = :id');
                $stmt->execute(['id' => $id]);
            }
            header('Location: /');
            exit;
        }
    }

    $tasks = $pdo->query('SELECT id, title, description, is_done, created_at FROM tasks ORDER BY id DESC')->fetchAll();
} catch (Throwable $exception) {
    http_response_code(500);
    $tasks = [];
    $error = 'Chưa kết nối được database. Hãy kiểm tra trạng thái container và log. Chi tiết kỹ thuật: '
        . $exception->getMessage();
}
?>
<!doctype html>
<html lang="vi">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Task Manager — Lab Nhập môn CNPM</title>
  <style>
    :root { color-scheme: light; --ink:#172033; --muted:#64748b; --line:#e2e8f0; --brand:#2563eb; }
    * { box-sizing: border-box; }
    body { margin:0; font-family:system-ui,-apple-system,Segoe UI,sans-serif; background:#f1f5f9; color:var(--ink); }
    main { width:min(900px, calc(100% - 32px)); margin:42px auto; }
    header { margin-bottom:24px; }
    h1 { margin:0 0 8px; font-size:clamp(1.7rem,4vw,2.4rem); }
    .sub { color:var(--muted); margin:0; }
    .panel { background:white; border:1px solid var(--line); border-radius:16px; padding:22px; margin-bottom:18px; box-shadow:0 8px 24px #0f172a0a; }
    label { display:block; font-weight:650; margin:0 0 6px; }
    input, textarea { width:100%; padding:11px 12px; border:1px solid #cbd5e1; border-radius:9px; font:inherit; margin-bottom:14px; }
    textarea { min-height:76px; resize:vertical; }
    button { border:0; border-radius:9px; padding:10px 14px; font:inherit; font-weight:650; cursor:pointer; background:var(--brand); color:white; }
    button.secondary { background:#e2e8f0; color:#172033; }
    button.danger { background:#fee2e2; color:#991b1b; }
    .task { display:flex; justify-content:space-between; gap:16px; align-items:flex-start; border-bottom:1px solid var(--line); padding:16px 0; }
    .task:last-child { border-bottom:0; padding-bottom:0; }
    .task:first-child { padding-top:0; }
    .task h3 { margin:0 0 5px; font-size:1.05rem; overflow-wrap:anywhere; }
    .task p { margin:0; color:var(--muted); white-space:pre-wrap; overflow-wrap:anywhere; }
    .done h3 { text-decoration:line-through; color:var(--muted); }
    .actions { display:flex; gap:8px; flex-wrap:wrap; flex-shrink:0; }
    .error { background:#fef2f2; border:1px solid #fecaca; color:#991b1b; border-radius:10px; padding:12px; margin-bottom:16px; overflow-wrap:anywhere; }
    .empty { color:var(--muted); text-align:center; padding:20px 0; }
    footer { color:var(--muted); font-size:.9rem; text-align:center; padding:10px; }
    @media (max-width:600px) { .task { flex-direction:column; } }
  </style>
</head>
<body>
<main>
  <header>
    <h1>📚 Task Manager</h1>
    <p class="sub">Bài lab Nhập môn Công nghệ phần mềm · PHP + MySQL + Docker</p>
  </header>

  <?php if ($error !== ''): ?>
    <div class="error"><?= e($error) ?></div>
  <?php endif; ?>

  <section class="panel">
    <h2>Thêm công việc</h2>
    <form method="post">
      <input type="hidden" name="action" value="add">
      <label for="title">Tên công việc *</label>
      <input id="title" name="title" maxlength="200" required placeholder="Ví dụ: Hoàn thành bài lab Docker">
      <label for="description">Mô tả</label>
      <textarea id="description" name="description" placeholder="Mô tả ngắn (không bắt buộc)"></textarea>
      <button type="submit">+ Thêm công việc</button>
    </form>
  </section>

  <section class="panel">
    <h2>Danh sách công việc (<?= count($tasks) ?>)</h2>
    <?php if (!$tasks): ?>
      <p class="empty">Chưa có công việc nào.</p>
    <?php else: ?>
      <?php foreach ($tasks as $task): ?>
        <article class="task <?= (int)$task['is_done'] === 1 ? 'done' : '' ?>">
          <div>
            <h3><?= e((string)$task['title']) ?></h3>
            <?php if (!empty($task['description'])): ?>
              <p><?= e((string)$task['description']) ?></p>
            <?php endif; ?>
            <p><small>Tạo lúc: <?= e((string)$task['created_at']) ?></small></p>
          </div>
          <div class="actions">
            <form method="post">
              <input type="hidden" name="action" value="toggle">
              <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
              <button class="secondary" type="submit"><?= (int)$task['is_done'] === 1 ? 'Chưa xong' : 'Hoàn thành' ?></button>
            </form>
            <form method="post" onsubmit="return confirm('Bạn có chắc muốn xóa công việc này?')">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$task['id'] ?>">
              <button class="danger" type="submit">Xóa</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    <?php endif; ?>
  </section>
  <footer>Thực hành quản lý source code và triển khai ứng dụng bằng Docker Compose</footer>
</main>
</body>
</html>
