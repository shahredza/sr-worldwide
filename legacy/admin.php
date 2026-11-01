<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$dbusername = "root";
$dbpassword = "root";
$dbname = "SR_worldwide";

$loginMessage = "";

if (isset($_GET['register']) && $_GET['register'] === 'success') {
    $loginMessage = "Registration successful! Please log in.";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $inputUsername = $_POST['username'];
    $inputPassword = $_POST['password'];

    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT password FROM admins WHERE username = ?");
    $stmt->bind_param("s", $inputUsername);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];

        if (password_verify($inputPassword, $hashedPassword)) {
            $_SESSION['admin_username'] = $inputUsername;
            header("Location: admin-dashboard.php");
            exit();
        } else {
            $loginMessage = "Invalid username or password.";
        }
    } else {
        $loginMessage = "Invalid username or password.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Login - SR Worldwide</title>
  <style>
    @font-face {
      font-family: 'GothicFont';
      src: url('font/fontgothic.ttf') format('truetype');
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f9e6f5;
      min-height: 100vh;
      display: flex;
      flex-direction: column;
    }

    header {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      background-color: rgba(0, 0, 0, 0.85);
      color: white;
      z-index: 1000;
      display: flex;
      align-items: center;
      justify-content: space-between;
      padding: 15px 30px;
      font-family: 'GothicFont', Arial, sans-serif;
    }

    .nav-left {
      display: flex;
      gap: 20px;
      align-items: center;
    }

    .nav-left a,
    .dropdown-btn {
      color: white;
      text-decoration: none;
      padding: 8px 12px;
      border-radius: 4px;
      font-size: 15px;
      background: none;
      border: none;
      cursor: pointer;
      position: relative;
    }

    .nav-left a:hover,
    .dropdown-btn:hover {
      background-color: #e91e63;
    }

    .dropdown {
      position: relative;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      min-width: 140px;
      top: 35px;
      left: 0;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
      z-index: 1;
      border-radius: 5px;
      flex-direction: column;
    }

    .dropdown-content a {
      color: #333;
      padding: 10px;
      text-decoration: none;
      display: block;
    }

    .dropdown-content a:hover {
      background-color: #f1f1f1;
    }

    .dropdown.show .dropdown-content {
      display: flex;
    }

    /* Modified logo container */
    .logo-container {
      position: absolute;
      left: 50%;
      transform: translateX(-50%);
      display: flex;
      align-items: center;
      text-decoration: none;
      color: white;
      transition: opacity 0.3s ease;
    }

    .logo-container:hover {
      opacity: 0.8;
    }

    .logo-container img {
      height: 50px;
      width: 50px;
      border-radius: 50%;
      margin-right: 10px;
    }

    .logo-container h1 {
      font-family: 'GothicFont', Arial, sans-serif;
      font-size: 22px;
      margin: 0;
      color: inherit;
    }

    main {
      flex: 1;
      display: flex;
      justify-content: center;
      align-items: center;
      padding-top: 100px;
      background: #f9e6f5;
    }

    .login-container {
      width: 100%;
      max-width: 320px;
      padding: 0 20px;
    }

    .login-box {
      background: white;
      padding: 40px 30px;
      border-radius: 8px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      width: 100%;
      font-family: 'GothicFont', Arial, sans-serif;
      display: flex;
      flex-direction: column;
      align-items: center;
      position: relative;
    }

    .login-box h2 {
      text-align: center;
      margin-bottom: 25px;
      font-size: 28px;
      color: #333;
      width: 100%;
    }

    .login-box form {
      width: 100%;
    }

    .form-group {
      width: 100%;
      margin-bottom: 15px;
    }

    input[type="text"], 
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-top: 8px;
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      font-family: Arial, sans-serif;
      transition: border-color 0.3s ease;
    }

    input[type="text"]:focus,
    input[type="password"]:focus {
      outline: none;
      border-color: #e91e63;
    }

    .login-button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 4px;
      background-color: #333;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: 'GothicFont', Arial, sans-serif;
      margin-top: 10px;
    }

    .login-button:hover {
      background-color: #e91e63;
    }

    .register-button {
      width: 100%;
      padding: 12px;
      font-size: 16px;
      font-weight: bold;
      border: none;
      border-radius: 4px;
      background-color: #888;
      color: white;
      cursor: pointer;
      transition: background-color 0.3s ease;
      font-family: 'GothicFont', Arial, sans-serif;
      margin-top: 10px;
      text-align: center;
      text-decoration: none;
      display: block;
    }

    .register-button:hover {
      background-color: #666;
    }

    .back-button {
      position: absolute;
      top: 15px;
      left: 15px;
      background: none;
      border: none;
      color: #333;
      font-size: 24px;
      cursor: pointer;
      transition: color 0.3s ease;
    }

    .back-button:hover {
      color: #e91e63;
    }

    .message {
      text-align: center;
      margin-top: 15px;
      min-height: 22px;
      color: red;
      font-weight: bold;
      width: 100%;
    }

    @media screen and (max-width: 480px) {
      header {
        padding: 15px 15px;
      }

      .nav-left {
        gap: 10px;
      }

      .login-box {
        padding: 30px 20px;
      }
    }
  </style>
</head>
<body>

<header>
  <div class="nav-left">
    <a href="about_us.html">About Us</a>
    <a href="contact.html">Contact Us</a>

    <div class="dropdown" id="shopDropdown">
      <button class="dropdown-btn">Shop ▾</button>
      <div class="dropdown-content">
        <a href="tops.html">Top</a>
        <a href="bottom.html">Bottom</a>
        <a href="acc.html">Accessories</a>
      </div>
    </div>

    <div class="dropdown" id="loginDropdown">
      <button class="dropdown-btn">Login ▾</button>
      <div class="dropdown-content">
        <a href="admin.php">Admin</a>
        <a href="userlogin.php">User</a>
      </div>
    </div>

    <a href="register.php">Register</a>
  </div>

  <!-- Logo linking to index.html -->
  <a href="index.html" class="logo-container">
    <img src="srlogo.jpg" alt="SR Logo" />
    <h1>SR Worldwide</h1>
  </a>
</header>

<main>
  <div class="login-container">
    <div class="login-box">
      <button class="back-button" onclick="window.history.back()">←</button>
      <h2>Admin Login</h2>
      <form method="POST" action="">
        <div class="form-group">
          <input type="text" name="username" placeholder="Username" required />
        </div>
        <div class="form-group">
          <input type="password" name="password" placeholder="Password" required />
        </div>
        <button type="submit" class="login-button">Log In</button>
      </form>
      <a href="admin-register.php" class="register-button">Register Admin</a>
      <div class="message"><?= htmlspecialchars($loginMessage) ?></div>
    </div>
  </div>
</main>

<script>
  const shopBtn = document.querySelector('#shopDropdown .dropdown-btn');
  const shopMenu = document.getElementById('shopDropdown');
  const loginBtn = document.querySelector('#loginDropdown .dropdown-btn');
  const loginMenu = document.getElementById('loginDropdown');

  shopBtn.addEventListener('click', () => {
    shopMenu.classList.toggle('show');
  });

  loginBtn.addEventListener('click', () => {
    loginMenu.classList.toggle('show');
  });

  window.addEventListener('click', (e) => {
    if (!shopMenu.contains(e.target)) shopMenu.classList.remove('show');
    if (!loginMenu.contains(e.target)) loginMenu.classList.remove('show');
  });
</script>

<!-- Code injected by live-server -->
<script>
	// <![CDATA[  <-- For SVG support
	if ('WebSocket' in window) {
		(function () {
			function refreshCSS() {
				var sheets = [].slice.call(document.getElementsByTagName("link"));
				var head = document.getElementsByTagName("head")[0];
				for (var i = 0; i < sheets.length; ++i) {
					var elem = sheets[i];
					var parent = elem.parentElement || head;
					parent.removeChild(elem);
					var rel = elem.rel;
					if (elem.href && typeof rel != "string" || rel.length == 0 || rel.toLowerCase() == "stylesheet") {
						var url = elem.href.replace(/(&|\?)_cacheOverride=\d+/, '');
						elem.href = url + (url.indexOf('?') >= 0 ? '&' : '?') + '_cacheOverride=' + (new Date().valueOf());
					}
					parent.appendChild(elem);
				}
			}
			var protocol = window.location.protocol === 'http:' ? 'ws://' : 'wss://';
			var address = protocol + window.location.host + window.location.pathname + '/ws';
			var socket = new WebSocket(address);
			socket.onmessage = function (msg) {
				if (msg.data == 'reload') window.location.reload();
				else if (msg.data == 'refreshcss') refreshCSS();
			};
			if (sessionStorage && !sessionStorage.getItem('IsThisFirstTime_Log_From_LiveServer')) {
				console.log('Live reload enabled.');
				sessionStorage.setItem('IsThisFirstTime_Log_From_LiveServer', true);
			}
		})();
	}
	else {
		console.error('Upgrade your browser. This Browser is NOT supported WebSocket for Live-Reloading.');
	}
	// ]]>
</script>
</body>
</html>