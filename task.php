<?php

require_once 'db.php';

session_start();

if (
    !isset($_COOKIE['isLoggedIn']) ||
    $_COOKIE['isLoggedIn'] !== 'true'
){
    header("Location: login.php");
    exit;
}

$username = $_COOKIE['username'];

$stmt = $pdo->prepare("
SELECT *
FROM users
WHERE username=?
LIMIT 1
");

$stmt->execute([$username]);

$user = $stmt->fetch();

$_SESSION['user_id'] = $user['id'];

$message = '';

if(isset($_GET['delete_id']))
{
    $stmt = $pdo->prepare("
    DELETE t
    FROM tasks t
    INNER JOIN projects p
    ON t.project_id = p.id
    WHERE t.id=?
    AND p.owner_id=?
    ");

    $stmt->execute([
        $_GET['delete_id'],
        $_SESSION['user_id']
    ]);

    $message = "تسک حذف شد.";
}

if(isset($_POST['save_status']))
{
    $stmt = $pdo->prepare("
    UPDATE tasks
    SET status=?
    WHERE id=?
    AND assigned_to=?
    ");

    $stmt->execute([
        $_POST['status'],
        $_POST['task_id'],
        $_SESSION['user_id']
    ]);

    $message = "وضعیت تسک ذخیره شد.";
}

$editTask = null;

if(isset($_GET['edit_id']))
{
    $stmt = $pdo->prepare("
    SELECT t.*
    FROM tasks t
    INNER JOIN projects p
    ON t.project_id=p.id
    WHERE t.id=?
    AND p.owner_id=?
    ");

    $stmt->execute([
        $_GET['edit_id'],
        $_SESSION['user_id']
    ]);

    $editTask = $stmt->fetch();
}

if(isset($_POST['save_task']))
{
    $taskId      = $_POST['task_id'] ?? '';
    $project_id  = $_POST['project_id'];
    $assigned_to = $_POST['assigned_to'];
    $description = $_POST['description'];
    $due_date    = $_POST['due_date'];

    if($taskId=='')
    {
        $stmt = $pdo->prepare("
        INSERT INTO tasks
        (
            description,
            status,
            assigned_to,
            due_date,
            project_id
        )
        VALUES
        (
            ?, 'todo', ?, ?, ?
        )
        ");

        $stmt->execute([
            $description,
            $assigned_to,
            $due_date,
            $project_id
        ]);

        $message = "تسک ثبت شد.";
    }
    else
    {
        $stmt = $pdo->prepare("
        UPDATE tasks
        SET
            description=?,
            assigned_to=?,
            due_date=?,
            project_id=?
        WHERE id=?
        ");

        $stmt->execute([
            $description,
            $assigned_to,
            $due_date,
            $project_id,
            $taskId
        ]);

        $message = "تسک ویرایش شد.";
    }
}

$stmt = $pdo->prepare("
SELECT
t.id,
p.name AS project_name,
t.description,
t.due_date,
t.status
FROM tasks t
INNER JOIN projects p
ON t.project_id=p.id
WHERE t.assigned_to=?
ORDER BY t.id DESC
");

$stmt->execute([
    $_SESSION['user_id']
]);

$myTasks = $stmt->fetchAll();

$stmt = $pdo->prepare("
SELECT
id,
name
FROM projects
WHERE owner_id=?
ORDER BY name
");

$stmt->execute([
    $_SESSION['user_id']
]);

$myProjects = $stmt->fetchAll();

$users = $pdo->query("
SELECT id,username
FROM users
ORDER BY username
")->fetchAll();

$stmt = $pdo->prepare("
SELECT
t.*,
u.username,
p.name AS project_name
FROM tasks t
INNER JOIN projects p
ON t.project_id=p.id
LEFT JOIN users u
ON t.assigned_to=u.id
WHERE p.owner_id=?
ORDER BY t.id DESC
");

$stmt->execute([
    $_SESSION['user_id']
]);

$ownerTasks = $stmt->fetchAll();

?>

<!doctype html>
<html lang="fa">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Team Sync</title>
  <link rel="stylesheet" href="style.css" />
  
</head>

<body>
  <!-- HEADER (Fixed Top Bar) -->
  <header class="header">
    <div class="brand">
        
      <div>
        <img src="logo.png" alt="LOGO" style="width: 40px; "/>
      </div>
      <div class="brand-title">
        <span class="name">Team Sync</span>
        <span class="tag" style="padding-top: 5%;">همگام با شما در کار ها</span>
      </div>
    </div>

    <div class="header-right">
      <div class="header-pill" id="@btnExit">
        <span class="badge"> خروج </span>
      </div>

      <div class="header-pill" title="وضعیت">
        <span class="dot" aria-hidden="true"></span>
        <span>حالت شب</span>
      </div>

    </div>
  </header>

  <!-- SIDEBAR (Fixed Left/Right depending on RTL) -->
  <aside class="sidebar">
    <div class="sidebar-card">
      <div class="brand">
        <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <line x1="3" y1="12" x2="21" y2="12" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round"></line>
              <line x1="3" y1="6" x2="21" y2="6" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round"></line>
              <line x1="3" y1="18" x2="21" y2="18" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round"></line>
            </svg>
        <h3>منو</h3>
      </div>

      <nav class="nav">
        <button class="menu-btn" type="button" data-target="home" id="@btnHome">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M4 10.5L12 4l8 6.5V20a1.5 1.5 0 0 1-1.5 1.5H5.5A1.5 1.5 0 0 1 4 20v-9.5Z"
                    stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
              <path d="M9.5 21.5V14.2c0-.7.6-1.2 1.2-1.2h2.6c.7 0 1.2.6 1.2 1.2v7.3"
                    stroke="rgba(255,255,255,.55)" stroke-width="1.4"/>
            </svg>
          </span>
          خانه
        </button>

    <a href="user.php">
        <button class="menu-btn" type="button" data-target="profile" id="btnuser">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
              <circle cx="12" cy="7" r="4" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></circle>
            </svg>
          </span>
          <?php echo $username; ?>
        </button>
      </a>


      <button class="menu-btn active" type="button" data-target="tasks-projects" id="btnTasksProjects">
        <span class="icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
            xmlns="http://www.w3.org/2000/svg">
          <rect x="3" y="3" width="8" height="8" rx="1.5" stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
          <rect x="13" y="3" width="8" height="8" rx="1.5" stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
          <rect x="3" y="13" width="8" height="8" rx="1.5" stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
          <rect x="13" y="13" width="8" height="8" rx="1.5" stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
          <path d="M7 7h0M17 7h0M7 17h0M17 17h0" stroke="rgba(255,255,255,.6)" stroke-width="2" stroke-linecap="round"/>
          </svg>
        </span>
        تسک‌ها 
      </button>

        <button class="menu-btn" type="button" data-target="notifications">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M8 21a4 4 0 0 0 4 4 2 2 0 0 0 2 0 4 4 0 0 0 4-4M6.5 7A7.5 7.5 0 0 0 5 11.5V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4.5A7.5 7.5 0 0 0 12 5c-.5 0-1 .2-1.5.34" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
          اعلانات
        </button>

        <button class="menu-btn" type="button" data-target="settings" id="@btnSettings">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <circle cx="12" cy="12" r="3" stroke="rgba(255,255,255,.9)" stroke-width="1.6"></circle>
              <path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l-.06.06a2 2 0 0 1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z" stroke="rgba(255,255,255,.9)" stroke-width="1.6"></path>
            </svg>
          </span>
          تنظیمات
        </button>

        

        <button class="menu-btn" type="button" data-target="features" id="@btnAboutAs">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M12 2l3 7 7 3-7 3-3 7-3-7-7-3 7-3 3-7Z"
                    stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
            </svg>
          </span>
          درباره ما
        </button>

        <button class="menu-btn" type="button" data-target="contact" id="@btnContactUs">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M4 6.5A2.5 2.5 0 0 1 6.5 4h11A2.5 2.5 0 0 1 20 6.5v11A2.5 2.5 0 0 1 17.5 20h-11A2.5 2.5 0 0 1 4 17.5v-11Z"
                    stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
              <path d="M7 8l5 4 5-4"
                    stroke="rgba(255,255,255,.6)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
          </span>
          تماس با ما 
        </button>

      </nav>

      <div style="height: 16px;"></div>

    </div>
  </aside>
<!-- MAIN -->
  <main class="app main">
    <div class="container">

      <!-- HOME -->
      <section class="section" id="home">
        <div class="kicker">
          <span class="spark" aria-hidden="true"></span>
          صفحه اصلی
        </div>

        <div class="brand2" >
          <div class="brand-title">
            <span class="name" style="font-size: 50px; padding:auto;">Team Sync</span>
            <span class="tag" style="font-size: 20px; padding:auto; padding-top: 5%;">  سایت مدیریت  پروژه های گروهی</span>
          </div>
          <div>
            <img src="logo.png" alt="LOGO" style="width: 150px; "/>
          </div>
        </div>


        <div style="height: 14px;"></div>

        
<?php if($message!=''): ?>

<div class="alert success">
    <span class="mark">✓</span>
    <span class="mini">
        <?php echo $message; ?>
    </span>
</div>

<div style="height:15px;"></div>

<?php endif; ?>


<div class="card">

<h2>تسک های من</h2>

<table class="table">

<thead>
<tr>
<th>شناسه</th>
<th>پروژه</th>
<th>توضیحات</th>
<th>تاریخ</th>
<th>وضعیت</th>
<th>ثبت</th>
</tr>
</thead>

<tbody>

<?php foreach($myTasks as $task): ?>

<tr>

<td><?php echo $task['id']; ?></td>

<td><?php echo htmlspecialchars($task['project_name']); ?></td>

<td><?php echo htmlspecialchars($task['description']); ?></td>

<td><?php echo $task['due_date']; ?></td>

<td>

<form method="post">

<input
type="hidden"
name="task_id"
value="<?php echo $task['id']; ?>">

<select
class="input"
name="status">

<option style="color:#0F2146;" value="todo" <?php if($task['status']=='todo') echo 'selected'; ?>>
انجام نشده
</option>

<option style="color:#0F2146;" value="in_progress" <?php if($task['status']=='in_progress') echo 'selected'; ?>>
درحال انجام
</option>

<option style="color:#0F2146;" value="done" <?php if($task['status']=='done') echo 'selected'; ?>>
انجام شده
</option>

<option style="color:#0F2146;" value="blocked" <?php if($task['status']=='blocked') echo 'selected'; ?>>
متوقف شده
</option>

</select>

</td>

<td>

<button class="btn" type="submit" name="save_status">✔️</button>

</form>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

<div style="height:20px;"></div>


<div class="card">

<h2>

<?php echo $editTask ? 'ویرایش تسک' : 'افزودن تسک'; ?>

</h2>

<form method="post">

<input
type="hidden"
name="task_id"
value="<?php echo $editTask['id'] ?? ''; ?>">

<select
class="input"
name="project_id">

<?php foreach($myProjects as $project): ?>

<option style="color:#0F2146;" value="<?php echo $project['id']; ?>">

<?php echo htmlspecialchars($project['name']); ?>

</option>

<?php endforeach; ?>

</select>

<br><br>

<select
class="input"
name="assigned_to">

<?php foreach($users as $user): ?>

<option style="color:#0F2146;" value="<?php echo $user['id']; ?>">

<?php echo htmlspecialchars($user['username']); ?>

</option>

<?php endforeach; ?>

</select>

<br><br>

<textarea
class="input"
name="description"
rows="4"
placeholder="توضیحات تسک"><?php echo $editTask['description'] ?? ''; ?></textarea>

<br><br>

<input
class="input"
type="date"
name="due_date"
value="<?php echo $editTask['due_date'] ?? ''; ?>">

<br><br>

<button
class="btn"
type="submit"
name="save_task">

✔️

</button>

</form>

</div>

<div style="height:20px;"></div>


<div class="card">

<h2>مدیریت تسک های پروژه های من</h2>

<table class="table">

<thead>
<tr>
<th>شناسه</th>
<th>پروژه</th>
<th>مسئول</th>
<th>توضیحات</th>
<th>تاریخ</th>
<th>وضعیت</th>
<th>عملیات</th>
</tr>
</thead>

<tbody>

<?php foreach($ownerTasks as $task): ?>

<tr>

<td><?php echo $task['id']; ?></td>

<td><?php echo htmlspecialchars($task['project_name']); ?></td>

<td><?php echo htmlspecialchars($task['username']); ?></td>

<td><?php echo htmlspecialchars($task['description']); ?></td>

<td><?php echo $task['due_date']; ?></td>

<td><?php echo $task['status']; ?></td>

<td>

<a href="?edit_id=<?php echo $task['id']; ?>">

<button class="btn">📝</button>

</a>

<a
href="?delete_id=<?php echo $task['id']; ?>"
onclick="return confirm('تسک حذف شود؟');">

<button class="btn btn-danger">❌</button>

</a>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

       </section>

        <div style="height: 16px;"></div>
      
      <footer class="footer">
        طراحی شده توسط زهرا سادات حسینی
      </footer>
    </div>
  </main>

  <script>
    const btnExit=document.getElementById("@btnExit");
    const btnSettings=document.getElementById("@btnSettings");
    const btnAboutAs=document.getElementById("@btnAboutAs");
    const btnContactUs=document.getElementById("@btnContactUs");
    const btnHome=document.getElementById("@btnHome");

    btnExit.addEventListener('click', function(){
       
      <?php 
        unset ($_COOKIE['isLoggedIn']); 
      ?>

      document.cookie = "isLoggedIn=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      document.cookie = "isLoggedIn=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=yourdomain.com;"; // اگر دامنه داری
      document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;";
      document.cookie = "username=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/; domain=yourdomain.com;";
      window.location.reload();

      const url='login.php';
      window.open(url);

    });

    btnSettings.addEventListener('click', function(){
       
      const url='settings.php';
      window.open(url,'_blank');
    });

    btnAboutAs.addEventListener('click', function(){
       
      const url='aboutUs.php';
      window.open(url,'_blank');
    });

    btnContactUs.addEventListener('click', function(){
       
      const url='contactUs.php';
      window.open(url,'_blank');
    });

    btnHome.addEventListener('click', function(){
      const url='index.php';
      window.open(url);
    });
    
  </script>

  

</body>
</html>