<?php
// 9단계: SQL Injection 방어 (Prepared Statement / 준비된 쿼리)
// 쿼리 구조와 데이터를 분리한다. 입력값은 '데이터'로만 바인딩되어 쿼리 구조를 바꿀 수 없다.
session_save_path('./');
session_start();

$db = new SQLite3('cat_homepage.db');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $enteredUsername = $_POST["username"];
    $enteredPassword = $_POST["password"];

    // ✅ 안전: placeholder 로 구조 고정, 값은 bindValue 로 주입
    $query = "SELECT * FROM users WHERE username = :username AND password = :password";
    $stmt = $db->prepare($query);
    $stmt->bindValue(':username', $enteredUsername, SQLITE3_TEXT);
    $stmt->bindValue(':password', $enteredPassword, SQLITE3_TEXT);
    $result = $stmt->execute();
    $row = $result->fetchArray(SQLITE3_ASSOC);

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
<head><meta charset="UTF-8"><title>GRAPE 고양이 웹 (secure)</title>
<style>body{font-family:Arial;background:#f4f4f4}header,footer{background:#2e8b57;color:#fff;text-align:center;padding:10px}
form{max-width:400px;margin:20px auto;background:#fff;padding:20px;border-radius:8px}
input{width:100%;padding:10px;margin-bottom:12px;box-sizing:border-box}</style></head>
<body>
    <header><h1>GRAPE 고양이 웹 (SQLi 방어판)</h1></header>
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
