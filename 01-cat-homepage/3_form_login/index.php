<?php
// 3단계: form 로그인 (취약 버전)
// 취약점 A: 인증 조건이 OR 라서 username "또는" password 하나만 맞아도 통과한다. (원래는 AND)
// 취약점 B: 로그인에 성공해도 세션/쿠키를 심지 않는다. login-success.php 는 아무 검사도 안 한다
//           -> /login-success.php 로 직접 접근하면 로그인 없이 개인정보가 보인다 (Broken Access Control)

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    $validUsername = "grapeuser";
    $validPassword = "secret1234";

    if ($enteredUsername == $validUsername or $enteredPassword == $validPassword) {
        header("Location: login-success.php");
        exit();
    } else {
        $errorMessage = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>GRAPE 고양이 웹</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; }
        header { background-color: #DE628B; color: #fff; text-align: center; padding: 10px; }
        main { padding: 20px; margin-bottom: 80px; }
        form { max-width: 400px; margin: 0 auto; background-color: #fff; padding: 20px;
               border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); }
        label { display: block; margin-bottom: 8px; font-weight: bold; }
        input { width: 100%; padding: 12px; margin-bottom: 20px; border: 1px solid #ccc;
                border-radius: 4px; box-sizing: border-box; }
        input[type="submit"] { background-color: #DE628B; color: #fff; cursor: pointer; border: none; }
        footer { background-color: #DE628B; color: #fff; text-align: center; padding: 10px;
                 position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <header>
        <h1>GRAPE 고양이 웹</h1>
    </header>

    <main>
        <form method="post">
            <h2>Login</h2>
            <?php
            if (isset($errorMessage)) {
                echo '<p style="color: red;">' . $errorMessage . '</p>';
            }
            ?>
            <label for="username">Username:</label>
            <input type="text" name="username" required>

            <label for="password">Password:</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">
        </form>
    </main>

    <footer>
        <p>&copy; 2023 Your Website. All rights reserved.</p>
    </footer>
</body>
</html>
