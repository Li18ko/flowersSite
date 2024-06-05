<?php
include '../../db_connection.php'; 

$database = Database::getInstance();
$mysqli = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'];
    $password = $_POST['password'];

    $stmt = $mysqli->prepare("SELECT * FROM admin WHERE login=?");
    $stmt->bind_param("s", $login);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows == 0) {
        echo '<div class="help">';
        echo '<p>Такой пользователь не существует.</p>';
        echo '</div>';
    } else {
        $row = $result->fetch_assoc();
        if ($row['password'] == md5($password)) {
            session_start();
            $_SESSION["admin"] = $row;
            header("Location: admin.php");
        }
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/styles.css">
    <title>Ошибка входа</title>
</head>
<body>
    <header>
        <h1>Панель администратора</h1>
    </header>

    <main>
        <section id="login">
            <h2>Ошибка входа</h2>
            <p class="error-message">Неверный логин или пароль. Попробуйте снова.</p>
            <a href="/accounts/admin/admin.html">Вернуться на страницу входа</a>
        </section>
    </main>

    <footer id="admin">
        <p>Фиалка - Уютный магазинчик цветов города Перми</p>
    </footer>
</body>
</html>

