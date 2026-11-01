<?php
$servername = "localhost";
$username = "root";
$password = "root";
$dbname = "SR_worldwide";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $birthday = $_POST['birthday'];

    // Check if email already exists
    $check = $conn->prepare("SELECT id FROM user WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        echo "<script>alert('This email already exists, please log in.'); window.location.href = 'login.php';</script>";
        exit();
    } else {
        // Hash the password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user
        $stmt = $conn->prepare("INSERT INTO user (email, password, birthday) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $email, $hashedPassword, $birthday);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Welcome to SR Worldwide!'); window.location.href = 'userlogin.php';</script>";
        } else {
            echo "Error: " . $stmt->error;
        }

        $stmt->close();
    }

    $check->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Register</title>
  <link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <style>
    @font-face {
      font-family: 'GothicFont';
      src: url('font/fontgothic.ttf') format('truetype');
    }

    h1, h2 {
      font-family: 'GothicFont', Arial, sans-serif;
    }

    body {
      font-family: Arial, sans-serif;
      background: #f3f3f3;
      margin: 0;
    }

    header {
      background-color: #333;
      color: white;
      padding: 10px 0;
      text-align: center;
    }

    .logo-container {
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .logo-container img {
      max-width: 50px;
      margin-right: 10px;
    }

    .form-box {
      max-width: 400px;
      margin: 100px auto;
      background: white;
      padding: 20px;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }

    input, button {
      width: 100%;
      padding: 10px;
      margin-top: 10px;
      border-radius: 5px;
      border: 1px solid #ccc;
    }

    button {
      background-color: #e91e63;
      color: white;
      border: none;
      cursor: pointer;
    }

    .checkbox {
      display: flex;
      align-items: center;
      margin-top: 10px;
    }

    footer {
      background-color: #333;
      color: white;
      text-align: center;
      padding: 5px;
      font-size: 12px;
      position: fixed;
      bottom: 0;
      left: 0;
      width: 100%;
    }

    .footer-logos img {
      height: 60px;
      margin: 0 10px;
    }

    .ui-datepicker {
      font-size: 16px;
      width: 100%;
      max-width: 300px;
      background-color: #fff;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
      padding: 10px;
      border: 1px solid #ddd;
      z-index: 9999 !important;
    }

    .ui-datepicker td {
      padding: 6px;
      text-align: center;
    }

    .ui-datepicker .ui-datepicker-header {
      background-color: #e91e63;
      color: white;
      font-size: 16px;
      padding: 10px;
      border-radius: 6px;
    }

    /* Back button style with animation */
    .back-button {
      display: inline-block;
      margin-bottom: 15px;
      padding: 10px 20px;
      background-color: #e91e63;
      color: white;
      text-decoration: none;
      border-radius: 30px;
      transition: all 0.3s ease;
      font-weight: bold;
      box-shadow: 0 4px 6px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }

    .back-button:hover {
      background-color: #d81b60;
      transform: translateX(-5px);
    }

    .back-button::after {
      content: "";
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: rgba(255, 255, 255, 0.2);
      transition: all 0.5s;
    }

    .back-button:hover::after {
      left: 100%;
    }
  </style>
</head>
<body>

  <header>
    <div class="logo-container">
      <a href="index.html" style="color: white; text-decoration: none;">
        <img src="srlogo.jpg" alt="SR Worldwide Logo">
        <h1>SR Worldwide</h1>
      </a>
    </div>
  </header>

  <div class="form-box">
    <a href="index.html" class="back-button">← Back</a>
    <h2>Welcome to Registration</h2>
    <form id="registerForm" method="POST" action="">
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>

      <div class="checkbox">
        <label for="showPassword">
          <input type="checkbox" id="showPassword" style="margin-right: 6px;">
          Show Password
        </label>
      </div>

      <input type="text" id="birthday" name="birthday" placeholder="Select Birthdate" readonly required>

      <button type="submit">Submit</button>
    </form>
  </div>

  <footer>
    <div class="footer-logos">
      <img src="logoikm.png" alt="IKM Logo">
      <img src="logomara.png" alt="MARA Logo">
    </div>
    <p>&copy; 2025 SR Worldwide. All rights reserved.</p>
  </footer>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
    $(function () {
      $("#birthday").datepicker({
        changeMonth: true,
        changeYear: true,
        yearRange: "1900:2025",
        dateFormat: "yy-mm-dd"
      });
    });

    // Show password toggle
    const showPasswordCheckbox = document.getElementById('showPassword');
    const passwordInput = document.querySelector('input[name="password"]');
    showPasswordCheckbox.addEventListener('change', function () {
      passwordInput.type = this.checked ? 'text' : 'password';
    });

    // Password validation before submit
    document.getElementById("registerForm").addEventListener("submit", function (e) {
      const password = document.querySelector('input[name="password"]').value;
      const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/;

      if (!passwordRegex.test(password)) {
        alert("Password must be at least 8 characters, include uppercase, lowercase, and a number.");
        e.preventDefault();
      }
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


