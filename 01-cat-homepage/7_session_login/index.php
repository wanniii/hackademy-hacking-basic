<?php
// 7단계: 세션 로그인
// 쿠키에는 랜덤 세션 ID(PHPSESSID)만 담기고, 실제 신원은 서버가 $_SESSION 에 보관한다.
// -> 쿠키값을 추측/변조해도 소용없다. 대신 세션 ID 자체가 탈취되면 그 사람이 된다(Session Hijacking).
session_save_path('./');
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST["username"];
    $p = $_POST["password"];
    if ($u == "grape" && $p == "secret1234") {
        $_SESSION['user'] = "grape";
        header("Location: page-grape.php");
        exit();
    } elseif ($u == "babo" && $p == "babo1234") {
        $_SESSION['user'] = "babo";
        header("Location: page-babo.php");
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
