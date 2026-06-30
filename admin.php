<?php
  session_start();
  require_once 'db.php';

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



// حذف کاربر
if (isset($_GET['delete_id'])) {
    $delete_id = $_GET['delete_id'];
    $delete = $pdo->prepare("DELETE FROM users WHERE id = ?");
    $delete->execute([$delete_id]);
    header('Location: admin.php');
    exit;
}

// دریافت اطلاعات کاربر برای ویرایش
$edit_data = null;
if (isset($_GET['edit_id'])) {
    $edit_id = $_GET['edit_id'];
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_data = $stmt->fetch();
}

// افزودن یا ویرایش کاربر
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    
    if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
        // ویرایش
        $user_id = $_POST['user_id'];
        if (!empty($password)) {
            $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ?, password_hash = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $username, $email, $role, $password, $user_id]);
        } else {
            $sql = "UPDATE users SET full_name = ?, username = ?, email = ?, role = ? WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$full_name, $username, $email, $role, $user_id]);
        }
    } else {
        // افزودن جدید
        $insert = $pdo->prepare("INSERT INTO users (username, email, password_hash, full_name, role, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $insert->execute([$username, $email, $password, $full_name, $role]);
    }
    
    header('Location: admin.php');
    exit;
}

// دریافت همه کاربران
$users = $pdo->query("SELECT id, username, email, full_name, role, created_at FROM users ORDER BY id DESC")->fetchAll();


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

        <button class="menu-btn active" type="button" data-target="home" id="btnuser">
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

      <a href="admin_project.php">
        <button class="menu-btn" type="button" data-target="dashboard" >
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
      </a>

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

        <div class="section" style="box-shadow:none; background: rgba(255,255,255,.02);">
          <div style="display:flex; align-items:flex-start; justify-content:space-between; gap:12px;">
            <div>
              <h3>👤 کاربران</h3>
              <p class="mini">لیست تمام کاربران دارای حساب:</p>
            </div>

            <div class="chips" id="chipsList">
              <span class="chip">➕</span>
              <span class="chip">✏️</span>
              <span class="chip">🗑️</span>
            </div>
          </div>

          <div style="height: 12px;"></div>

          <!-- جدول کاربران -->
          <table class="table">
              <thead>
                  <tr>
                      <th>نام و نام خانوادگی</th>
                      <th>نام کاربری</th>
                      <th>ایمیل</th>
                      <th>رمزعبور</th>
                      <th>نقش</th>
                      <th></th>
                  </tr>
              </thead>
              <tbody>
                  <?php foreach ($users as $userItem): ?>
                  <tr>
                      <td><?php echo htmlspecialchars($userItem['full_name']); ?></td>
                      <td><?php echo htmlspecialchars($userItem['username']); ?></td>
                      <td><?php echo htmlspecialchars($userItem['email']); ?></td>
                      <td> *<!-- <?php echo isset($userItem['password_hash']) ? htmlspecialchars($userItem['password_hash']) : ''; ?> --></td>
                      <td><?php echo $userItem['role']; ?></td>
                      <td>
                          <a href="?edit_id=<?php echo $userItem['id']; ?>">
                              <button class="btn" type="button" style="width: 65px; height: 50px; font-size: 16px;">📝</button>
                          </a>
                          <a href="?delete_id=<?php echo $userItem['id']; ?>" onclick="return confirm('حذف شود؟')">
                              <button class="btn btn-danger btn-sm" type="button" style="width: 65px; height: 50px; font-size: 16px;">❌</button>
                          </a>
                      </td>
                  </tr>
                  <?php endforeach; ?>
              </tbody>
          </table>
  

          </br><!-- فرم افزودن/ویرایش کاربر --> <!-- ✓ × ✅ ❌ ➕ ✔️ ✏️ 📝 🗑️ 🧹 🔧 ✨ 👤 👍  --> 
          <form method="POST">
            <div style="border-radius: var(--radius-lg); border: 1px solid rgba(255,255,255,.10); background: rgba(255,255,255,.03); padding: 15px;
                display: inline-flex; align-items: center; justify-content: center;">
              <input class="input" type="hidden" name="user_id" id="user_id" value="<?php echo $edit_data ? $edit_data['id'] : ''; ?>">
              <input class="input" style="margin: 5px;" type="text" name="full_name" id="full_name" placeholder="نام و نام خانوادگی" value="<?php echo $edit_data ? htmlspecialchars($edit_data['full_name']) : ''; ?>" required>
              <input class="input" style="margin: 5px;" type="text" name="username" placeholder="نام کاربری" id="username"  value="<?php echo $edit_data ? htmlspecialchars($edit_data['username']) : ''; ?>" required>
              <input class="input" style="margin: 5px;" type="email" name="email" placeholder="ایمیل"  id="email" value="<?php echo $edit_data ? htmlspecialchars($edit_data['email']) : ''; ?>" required>
              <input class="input" style="margin: 5px;" type="text" name="password" id="password" placeholder="رمز عبور"
               value="<?php echo $edit_data ? htmlspecialchars($edit_data['password_hash']) : ''; ?>" required>
              <select class="input" style="margin: 5px;" name="role" id="role">
                  <option style="color:#0F2146;" value="member" <?php echo ($edit_data && $edit_data['role'] == 'member') ? 'selected' : ''; ?>>member</option>
                  <option style="color:#2DD4BF;" value="manager" <?php echo ($edit_data && $edit_data['role'] == 'manager') ? 'selected' : ''; ?>>manager</option>
                  <option style="color:#A1003B;" value="admin" <?php echo ($edit_data && $edit_data['role'] == 'admin') ? 'selected' : ''; ?>>admin</option>
              </select>
              <button class="btn" id="submitBtn" style="margin: 5px; font-size: 18px;" type="submit" name="add_user" ><?php echo $edit_data ? '✔️' : '➕'; ?></button>
            </div>
          </form>


          <div style="height: 12px;"></div>
          <div class="alert success">
            <span class="mark" aria-hidden="true">✓</span>
            <div>
              <div style="font-weight:900; margin-bottom:4px;">نکته</div>
              <div class="mini">
              شما میتوانید کاربران را ویرایش، حذف و اضاف کنید
            </div>
            </div>
          </div>
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

    
// بعد از ویرایش،
// اگر صفحه رفرش شد و 
//edit_id 
//توی 
//URL 
//مونده بود پاکش کن
    if (window.location.href.indexOf('edit_id') > -1) {
        window.history.replaceState({}, document.title, window.location.pathname);
    }
    
  </script>

  

</body>
</html>