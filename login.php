<?php
session_start();
if(isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang='en'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Dental Clinic - Login</title>
    <link href='https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap' rel='stylesheet'>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'>
    <link rel='stylesheet' href='css/style.css'>
    <style>
        body {
            background-color: #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }
        .login-card {
            background: white;
            border-radius: 12px;
            padding: 40px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            text-align: center;
        }
        .login-card i.fa-tooth {
            font-size: 48px;
            color: var(--primary);
            margin-bottom: 16px;
        }
        .login-card h2 {
            margin-top: 0;
            color: var(--text-main);
        }
        .login-card p {
            color: var(--text-muted);
            margin-bottom: 32px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 20px;
        }
        .btn-block {
            width: 100%;
            padding: 12px;
            font-size: 16px;
            margin-top: 12px;
        }
        .error-msg {
            color: #ef4444;
            background: #fee2e2;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <i class="fa-solid fa-tooth"></i>
        <h2>DentaFlow</h2>
        <p>Sign in to your clinic</p>
        
        <div class="error-msg" id="errorMsg"></div>

        <form id="loginForm">
            <div class="form-group">
                <label>Username</label>
                <input type="text" class="form-control" name="username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-block" id="loginBtn">Sign In</button>
        </form>
    </div>

    <script>
    document.getElementById('loginForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const btn = document.getElementById('loginBtn');
        const errorMsg = document.getElementById('errorMsg');
        btn.innerText = 'Signing in...';
        btn.disabled = true;
        errorMsg.style.display = 'none';

        const formData = new FormData(this);
        fetch('api/login_action.php', {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if(data.success) {
                window.location.href = data.redirect || 'index.php';
            } else {
                errorMsg.innerText = data.message;
                errorMsg.style.display = 'block';
                btn.innerText = 'Sign In';
                btn.disabled = false;
            }
        })
        .catch(err => {
            errorMsg.innerText = 'Server connection error.';
            errorMsg.style.display = 'block';
            btn.innerText = 'Sign In';
            btn.disabled = false;
        });
    });
    </script>
</body>
</html>
