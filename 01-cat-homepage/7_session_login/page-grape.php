<?php
// 신원은 쿠키값이 아니라 서버 세션에서 확인한다.
session_save_path('./');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'grape') {
    header("Location: goback.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko"><head><meta charset="UTF-8"><title>grape 개인 페이지</title></head>
<body>
    <header><h1>개인 페이지</h1><a href="logout.php">Logout</a></header>
    <main>
        <h2>안녕하세요 grape 의 개인페이지입니다.</h2>
        <h3>전화번호 : 010-GRAPE-0001</h3>
        <h3>계좌번호 : 고양이은행 MMM-MMM-MMM</h3>
    </main>
</body>
</html>
