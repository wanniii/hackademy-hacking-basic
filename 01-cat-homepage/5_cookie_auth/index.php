<?php
// 5단계: 쿠키 인증 (추측 가능한 쿠키값)
// 4단계의 grape_cookie 가 너무 뻔해서, grape_bs / babo_dg 처럼 "덜 뻔하게" 바꿨다.
// 하지만 여전히 "grape_" + 짧은 2글자라서 무차별 대입(brute force)으로 뚫린다.
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $u = $_POST["username"];
    $p = $_POST["password"];
    if ($u == "grape" && $p == "secret1234") {
        setcookie("user", "grape_bs", time() + 3600, "/");
        header("Location: page-grape.php");
        exit();
    } elseif ($u == "babo" && $p == "babo1234") {
        setcookie("user", "babo_dg", time() + 3600, "/");
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
