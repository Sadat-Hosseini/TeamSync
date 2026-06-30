<?php
if (isset($_COOKIE['isLoggedIn']) && $_COOKIE['isLoggedIn'] === 'true') {
    $username = $_COOKIE['username'] ?? 'Guest';
    
    if ($username !== 'admin') {
        header("Location: user.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

require_once 'db.php';


$message = '';

if(isset($_GET['delete_id']))
{
    $stmt = $pdo->prepare("
        DELETE FROM projects
        WHERE id=?
    ");

    $stmt->execute([
        (int)$_GET['delete_id']
    ]);

    $message = "پروژه حذف شد.";
}

$editProject = null;

if(isset($_GET['edit_id']))
{
    $stmt = $pdo->prepare("
        SELECT *
        FROM projects
        WHERE id=?
    ");

    $stmt->execute([
        (int)$_GET['edit_id']
    ]);

    $editProject = $stmt->fetch();
}

if($_SERVER['REQUEST_METHOD']=='POST')
{
    $id          = $_POST['project_id'] ?? '';
    $name        = trim($_POST['name']);
    $description = trim($_POST['description']);
    $owner_id    = $_POST['owner_id'];
    $status      = $_POST['status'];
    $start_date  = $_POST['start_date'];
    $end_date    = $_POST['end_date'];

    if($id=='')
    {
        $stmt = $pdo->prepare("
            INSERT INTO projects
            (
                name,
                description,
                owner_id,
                status,
                start_date,
                end_date
            )
            VALUES
            (
                ?,?,?,?,?,?
            )
        ");

        $stmt->execute([
            $name,
            $description,
            $owner_id,
            $status,
            $start_date,
            $end_date
        ]);

        $message = "پروژه اضافه شد.";
    }
    else
    {
        $stmt = $pdo->prepare("
            UPDATE projects
            SET
                name=?,
                description=?,
                owner_id=?,
                status=?,
                start_date=?,
                end_date=?
            WHERE id=?
        ");

        $stmt->execute([
            $name,
            $description,
            $owner_id,
            $status,
            $start_date,
            $end_date,
            $id
        ]);

        $message = "پروژه ویرایش شد.";
    }
}

$projects = $pdo->query("
SELECT
projects.*,
users.username
FROM projects
LEFT JOIN users
ON projects.owner_id = users.id
ORDER BY projects.id DESC
")->fetchAll();

$users = $pdo->query("
SELECT id,username
FROM users
ORDER BY username
")->fetchAll();

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

      <button class="menu-btn" type="button" data-target="profile" id="@btnHome">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
              <circle cx="12" cy="7" r="4" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></circle>
            </svg>
          </span>
          خانه
        </button>

        <a href="admin.php">
        <button class="menu-btn" type="button" data-target="home" id="btnuser">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M4 10.5L12 4l8 6.5V20a1.5 1.5 0 0 1-1.5 1.5H5.5A1.5 1.5 0 0 1 4 20v-9.5Z"
                    stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
              <path d="M9.5 21.5V14.2c0-.7.6-1.2 1.2-1.2h2.6c.7 0 1.2.6 1.2 1.2v7.3"
                    stroke="rgba(255,255,255,.55)" stroke-width="1.4"/>
            </svg>
          </span>
          <?php echo $username; ?>
        </button>
        </a>

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

        
        <button class="menu-btn active" type="button" data-target="dashboard" >
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M4 13.2c0-4.1 3.5-7.4 8-7.4s8 3.3 8 7.4v7.3H4v-7.3Z"
                    stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
              <path d="M8 18h.01M12 18h.01M16 18h.01"
                    stroke="rgba(255,255,255,.6)" stroke-width="2.2" stroke-linecap="round"/>
            </svg>
          </span>
          پروژه ها
        </button>

      <a href="admin_task.php">
      <button class="menu-btn" type="button" data-target="tasks-projects" id="btnTasksProjects">
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
      </a>

      <a href="comment.php">
      <button class="menu-btn" type="button" data-target="comments" id="btnComments">
        <span class="icon" aria-hidden="true">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
               xmlns="http://www.w3.org/2000/svg">
            <path d="M21 11.5a8.5 8.5 0 0 1-8.5 8.5H5a1 1 0 0 1-1-1v-7.5A8.5 8.5 0 0 1 12.5 3h0A8.5 8.5 0 0 1 21 11.5Z"
                  stroke="rgba(255,255,255,.9)" stroke-width="1.6"/>
            <path d="M8 9h7M8 13h5" stroke="rgba(255,255,255,.6)" stroke-width="1.6" stroke-linecap="round"/>
            <path d="M16 18l3 3 2-2" stroke="rgba(255,255,255,.7)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        نظرات
      </button>
      </a>

        <button class="menu-btn" type="button" data-target="notifications">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M8 21a4 4 0 0 0 4 4 2 2 0 0 0 2 0 4 4 0 0 0 4-4M6.5 7A7.5 7.5 0 0 0 5 11.5V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4.5A7.5 7.5 0 0 0 12 5c-.5 0-1 .2-1.5.34" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
          اعلانات
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

<h2>

<?php echo $editProject ? 'ویرایش پروژه' : 'افزودن پروژه'; ?>

</h2>

<form method="post">

<input
type="hidden"
name="project_id"
value="<?php echo $editProject['id'] ?? ''; ?>">

<input
class="input"
type="text"
name="name"
placeholder="نام پروژه"
required
value="<?php echo $editProject['name'] ?? ''; ?>">

<br><br>

<textarea
class="input"
name="description"
rows="4"
placeholder="توضیحات پروژه"><?php echo $editProject['description'] ?? ''; ?></textarea>

<br><br>

<select
class="input"
name="owner_id">

<?php foreach($users as $user): ?>

<option style="color:#0F2146;" value="<?php echo $user['id']; ?>">

<?php echo $user['username']; ?>

</option>

<?php endforeach; ?>

</select>

<br><br>

<select class="input"  name="status">

<option style="color:#0F2146;" value="active">فعال</option>
<option style="color:#0F2146;" value="completed">تکمیل شده</option>
<option style="color:#0F2146;" value="archived">آرشیو شده</option>

</select>

<br><br>

<input class="input" type="date" name="start_date" value="<?php echo $editProject['start_date'] ?? ''; ?>">

<br><br>

<input
class="input"
type="date"
name="end_date"
value="<?php echo $editProject['end_date'] ?? ''; ?>">

<br><br>

<button type="submit"class="btn">✔️ </button>

</form>

</div>

<div style="height:20px;"></div>

<div class="card">

<h2>لیست پروژه ها</h2>

<table class="table">

<thead>

<tr>
<th>نام پروژه</th>
<th>توضیحات</th>
<th>مالک</th>
<th>وضعیت</th>
<th>شروع</th>
<th>پایان</th>
<th>عملیات</th>
</tr>

</thead>

<tbody>

<?php foreach($projects as $project): ?>

<tr>

<td>
<?php echo htmlspecialchars($project['name']); ?>
</td>

<td>
<?php echo htmlspecialchars($project['description']); ?>
</td>

<td>
<?php echo htmlspecialchars($project['username']); ?>
</td>

<td>
<?php echo htmlspecialchars($project['status']); ?>
</td>

<td>
<?php echo htmlspecialchars($project['start_date']); ?>
</td>

<td>
<?php echo htmlspecialchars($project['end_date']); ?>
</td>

<td>

<a href="?edit_id=<?php echo $project['id']; ?>">
    <button class="btn">
        📝
    </button>
</a>

<a
href="?delete_id=<?php echo $project['id']; ?>"
onclick="return confirm('پروژه حذف شود؟');">

    <button class="btn btn-danger">
        ❌
    </button>

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
       
      const url='admin_settings.php';
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