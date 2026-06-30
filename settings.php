<?php
if (isset($_COOKIE['isLoggedIn']) && $_COOKIE['isLoggedIn'] === 'true') {
    $username = $_COOKIE['username'] ?? 'Guest';
    
    if ($username === 'admin') {
        header("Location: admin.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
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

      <a href="task.php">
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

        <button class="menu-btn" type="button" data-target="notifications">
          <span class="icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                 xmlns="http://www.w3.org/2000/svg">
              <path d="M8 21a4 4 0 0 0 4 4 2 2 0 0 0 2 0 4 4 0 0 0 4-4M6.5 7A7.5 7.5 0 0 0 5 11.5V16a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-4.5A7.5 7.5 0 0 0 12 5c-.5 0-1 .2-1.5.34" stroke="rgba(255,255,255,.9)" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"></path>
            </svg>
          </span>
          اعلانات
        </button>

        <button class="menu-btn active" type="button" data-target="settings">
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

        <div class="grid-3">

          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">⚙️</span> 
          <h3 style="padding-top:5px;">تنظیمات سیستم</h3>
         </div>
          <p  style="padding-top:15px;">مدیریت تنظیمات کلی سامانه  و پیکربندی بخش‌های مختلف</p>
          </div>



          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">👤</span> 
          <h3 style="padding-top:5px;">حساب کاربری</h3>
         </div>
          <p  style="padding-top:15px;">مدیریت اطلاعات </br> نام کاربری، رمز عبور</p>
          </div>
          


          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">🔔</span> 
          <h3 style="padding-top:5px;">اعلان‌ها</h3>
         </div>
          <p  style="padding-top:15px;">تنظیمات نمایش اعلان‌ها</p>
          </div>


          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">🎨</span> 
          <h3 style="padding-top:5px;">ظاهر برنامه</h3>
         </div>
          <p  style="padding-top:15px;">رنگ‌ها و تم سیستم </br> حالت شب/روز</p>
          </div>



          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">🛡️</span> 
          <h3 style="padding-top:5px;">امنیت</h3>
         </div>
          <p  style="padding-top:15px;">تنظیمات مربوط به امنیت و دسترسی </p>
          </div>


          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">📧</span> 
          <h3 style="padding-top:5px;">ایمیل</h3>
         </div>
          <p  style="padding-top:15px;"> آدرس ایمیل</br> تنظیمات ارسال و دریافت ایمیل</p>
          </div>


          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">💾</span> 
          <h3 style="padding-top:5px;">پشتیبان‌گیری</h3>
         </div>
          <p  style="padding-top:15px;">مدیریت نسخه‌های پشتیبان </br> آپدیت به زودی ...</p>
          </div>

          <div class="card setting-card big-card">
            <div class="alert success">  <span class="mark" aria-hidden="true">🆕</span> 
          <h3 style="padding-top:5px;">ورژن</h3>
         </div>
          <p  style="padding-top:15px;"> نسخه 1.0 سامانه تیم سینک</p>
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