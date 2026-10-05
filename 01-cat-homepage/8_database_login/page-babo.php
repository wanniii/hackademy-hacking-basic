<?php
session_save_path('./');
session_start();
if (!isset($_SESSION['user']) || $_SESSION['user'] != 'babo') {
    header("Location: goback.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="ko"><head><meta charset="UTF-8"><title>babo 개인 페이지</title></head>
<body>
    <header><h1>개인 페이지</h1><a href="logout.php">Logout</a></header>
    <main>
        <h2>안녕하세요 바보의 개인페이지입니다.</h2>
        <h3>전화번호 : 010-BABO-0002</h3>
        <h3>계좌번호 : 강아지은행 BBB-BBB-BBB</h3>
    </main>
</body>
</html>
