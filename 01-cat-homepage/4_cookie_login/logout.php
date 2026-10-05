<!-- logout.php -->
<?php
// user 쿠키 삭제 후 로그인 페이지로
setcookie("user", "", time() - 3600, "/");
header("Location: index.php");
exit();
?>
