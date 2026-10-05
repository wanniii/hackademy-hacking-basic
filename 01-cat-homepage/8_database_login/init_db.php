<?php
// cat_homepage.db 생성 시드 스크립트.
// 실행: (컨테이너 /app 에서) php init_db.php
// .db 파일은 .gitignore 되어 있으므로, 이 스크립트로 언제든 재생성한다.
$db = new SQLite3('cat_homepage.db');
$db->exec("DROP TABLE IF EXISTS users");
$db->exec("CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL,
    password TEXT NOT NULL,
    email TEXT
)");
$stmt = $db->prepare("INSERT INTO users (username, password, email) VALUES (:u, :p, :e)");
$users = [
    ["grape", "secret1234", "grape@cat.web"],
    ["babo",  "babo1234",   "babo@cat.web"],
];
foreach ($users as $u) {
    $stmt->bindValue(":u", $u[0]);
    $stmt->bindValue(":p", $u[1]);
    $stmt->bindValue(":e", $u[2]);
    $stmt->execute();
}
echo "seeded users: ";
$r = $db->query("SELECT username FROM users");
while ($row = $r->fetchArray(SQLITE3_ASSOC)) echo $row['username'] . " ";
echo "\n";
?>
