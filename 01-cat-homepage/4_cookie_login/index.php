<?php
// 4단계: 쿠키 로그인 (취약 버전)
// 로그인 성공 시 "user" 쿠키에 고정 문자열(grape_cookie / babo_cookie)을 심는다.
// 개인 페이지는 이 쿠키값만 보고 통과시킨다 -> 쿠키값을 알면(또는 추측하면) 로그인 불필요.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST["username"];
    $p = $_POST["password"];

    if ($u == "grape" && $p == "secret1234") {
        setcookie("user", "grape_cookie", time() + 3600, "/");
        header("Location: page-grape.php");
        exit();
    } elseif ($u == "babo" && $p == "babo1234") {
        setcookie("user", "babo_cookie", time() + 3600, "/");
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
