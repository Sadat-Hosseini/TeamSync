<?php
require_once 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // اعتبارسنجی
    if (empty($full_name) || empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        die("لطفا تمامی فیلدها را پر کنید.");
    }
    
    if (strlen($password) < 4) {
        die("رمز عبور حداقل باید ۴ کاراکتر باشد.");
    }
    
    if ($password !== $confirm_password) {
        die("رمزها با هم مطابقت ندارند.");
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("فرمت ایمیل معتبر نیست.");
    }
    
    // چک تکراری بودن
    $check = $pdo->prepare("SELECT username, email FROM users WHERE username = ? OR email = ?");
    $check->execute([$username, $email]);
    $exists = $check->fetch();
    
    if ($exists) {
        if ($exists['username'] === $username) {
            die("این نام کاربری قبلاً ثبت شده است.");
        }
        if ($exists['email'] === $email) {
            die("این ایمیل قبلاً ثبت شده است.");
        }
    }
    
    // ذخیره رمز  
    $sql = "INSERT INTO users (username, email, password_hash, full_name, role, created_at) VALUES (?, ?, ?, ?, 'member', NOW())";
    $stmt = $pdo->prepare($sql);
    
    if ($stmt->execute([$username, $email, $password, $full_name])) {
        die("success");
    } else {
        die("خطا در ثبت نام");
    }
}
?>
<!doctype html>
<html lang="fa">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Register</title>
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
        <button class="menu-btn active" type="button" data-target="home" id="@btnHome">
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
            <span class="name" style="font-size: 50px; padding:auto;"> Register</span>
            <span class="tag" style="font-size: 20px; padding:auto; padding-top: 5%;"> ساخت  حساب کاربری </span>
          </div>
          <div>
            <img src="logo.png" alt="LOGO" style="width: 150px; "/>
          </div>
        </div>


        <div style="height: 14px;"></div>
<!-- register page  -->
        <div style="padding:2% 25%;">
          <div class="card" style="padding: 15%; align-items: center;">

          <form id="registerForm">

            <p style="margin-right:25%; margin-left:15%;">لطفا مشخصات خود را وارد کنید </p> </br>

            <div class="field">
              <label for="name">نام و نام خانوادگی</label>
              <input class="input" id="name" type="text" placeholder="name and family" />
            </div>

            <div class="field">
              <label for="username">نام کاربری</label>
              <input class="input" id="username" type="text" placeholder="username" required/>
            </div>

            <div class="field">
              <label for="email">ایمیل</label>
              <input class="input" id="email" type="email" placeholder="example@gmail.com" />
            </div>

            <div class="field">
              <label for="password">رمز عبور</label>
              <input class="input" id="password" type="password" placeholder="password" required/>
            </div>

            <div class="field">
              <label for="confirmPassword">تأیید رمز عبور</label>
              <input class="input" id="confirmPassword" type="password" placeholder="password" required/>
            </div>

            </br>
            <button class="btn" type="submit" id="primary-btn" style="margin-right:35%; margin-left:35%;">
               ثبت نام 
            </button>

            <p id="message" style="margin-top:15px; text-align:center;"></p>
           
            <p style="text-align:center;"> حساب دارید؟ <a href="login.php">ورود</a></p>

          </form>

          </div>
        </div>
<!-- register page  -->
        </section>

        <div style="height: 16px;"></div>
      
      <footer class="footer">
        طراحی شده توسط زهرا سادات حسینی
      </footer>
    </div>
  </main>



  <script>
    const btnSettings=document.getElementById("@btnSettings");
    const btnAboutAs=document.getElementById("@btnAboutAs");
    const btnContactUs=document.getElementById("@btnContactUs");
    const btnHome=document.getElementById("@btnHome");

    const form = document.getElementById("registerForm");
    const message = document.getElementById("message");



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
      window.open(url,'_blank');
    });

    form.addEventListener("submit", function(e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const username = document.getElementById("username").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    // اعتبارسنجی سمت کلاینت
    if (username === "" || password === "" || confirmPassword === "" || email === "" || name === "") {
        message.style.color = "red";
        message.textContent = "لطفا تمامی فیلدها را پر کنید.";
        return;
    }

    if (password.length < 4) {
        message.style.color = "red";
        message.textContent = "رمز عبور حداقل باید ۴ کاراکتر باشد.";
        return;
    }

    if (password !== confirmPassword) {
        message.style.color = "red";
        message.textContent = "رمزها با هم مطابقت ندارند.";
        return;
    }

    if (!email.includes("@") || !email.includes(".")) {
        message.style.color = "red";
        message.textContent = "فرمت ایمیل معتبر نیست";
        return;
    }

    // ارسال به سرور
    let formData = new FormData();
    formData.append('full_name', name);
    formData.append('username', username);
    formData.append('email', email);
    formData.append('password', password);
    formData.append('confirm_password', confirmPassword);

    fetch('register.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        if (data === "success") {
            message.style.color = "green";
            message.textContent = "ثبت‌نام با موفقیت انجام شد ✅";
            setTimeout(() => {
                window.location.href = "login.php";
            }, 2000);
        } else {
            message.style.color = "red";
            message.textContent = data;
        }
    });
});

  </script>
  

</body>
</html>