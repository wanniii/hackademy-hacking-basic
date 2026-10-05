<?php
// 8단계: 데이터베이스 로그인 (취약 버전 - SQL Injection)
// 사용자 입력을 쿼리 문자열에 그대로 이어붙인다 -> 입력으로 쿼리 구조를 바꿀 수 있다.
session_save_path('./');
session_start();

$db = new SQLite3('cat_homepage.db');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    // ❌ 취약: 입력값을 그대로 문자열에 삽입
    $query = "SELECT * FROM users WHERE username='{$enteredUsername}' AND password='{$enteredPassword}'";
    $result = $db->query($query);
    $row = $result ? $result->fetchArray(SQLITE3_ASSOC) : false;

    if ($row) {
        $login_user = $row["username"];
        $_SESSION["user"] = $login_user;
        header("Location: page-{$login_user}.php");
        exit();
    } else {
        $errorMessage = "Invalid username or password";
    }
}
?>
<!DOCTYPE html>
<html lang="ko">
<head><meta charset="UTF-8"><title>GRAPE 고양이 웹</title>
<style>body{font-family:Arial;background:#f4f4f4}header,footer{background:#DE628B;color:#fff;text-align:center;padding:10px}
form{max-width:400px;margin:20px auto;background:#fff;padding:20px;border-radius:8px}
input{width:100%;padding:10px;margin-bottom:12px;box-sizing:border-box}</style></head>
<body>
    <header><h1>GRAPE 고양이 웹</h1></header>
    <form method="post">
        <h2>Login</h2>
        <?php if (isset($errorMessage)) echo '<p style="color:red;">'.$errorMessage.'</p>'; ?>
        <label>Username:</label><input type="text" name="username" required>
        <label>Password:</label><input type="password" name="password" required>
        <input type="submit" value="Login">
    </form>
    <footer><p>&copy; 2023 Your Website.</p></footer>
</body>
</html>
