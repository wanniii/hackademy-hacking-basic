<?php
// 취약점 B (Broken Access Control):
// 이 개인 페이지는 "로그인한 사람인지"를 전혀 검사하지 않는다.
// 로그인 과정을 거치지 않고 /login-success.php 로 바로 접근해도 개인정보가 그대로 노출된다.
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>grape cat web - 개인 페이지</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; background-color: #f4f4f4; }
        header { background-color: #333; color: #fff; text-align: center; padding: 10px; }
        main { padding: 20px; margin-bottom: 80px; }
        footer { background-color: #333; color: #fff; text-align: center; padding: 10px;
                 position: fixed; bottom: 0; width: 100%; }
    </style>
</head>
<body>
    <header>
        <h1>개인 페이지</h1>
        <a href="logout.php" style="color:#fff;">Logout</a>
    </header>

    <main>
        <h2>안녕하세요. 당신의 개인페이지입니다.</h2>
        <h3>&lt;나의 고양이&gt;</h3>
        <h3>전화번호 : 010-1234-1234</h3>
        <h3>계좌번호 : 12341234</h3>
    </main>

    <footer>
        <p>&copy; 2023 Your Website. All rights reserved.</p>
    </footer>
</body>
</html>
