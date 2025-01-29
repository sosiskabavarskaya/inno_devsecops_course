<?php
// Исправленный безопасный код

// Уязвимость 1: XSS
if (isset($_GET['username'])) {
    echo "Добро пожаловать, " . htmlspecial($_GET['username'], ENT_QUOTES, 'UTF-8') . "!";
}

// Уязвимость 2: SQL-инъекция
if (isset($_POST['username']) && isset($_POST['password'])) {
    $conn = new mysqli("localhost", "root", "", "testdb");
    if ($conn->connect_error) {
        die("Ошибка подключения: " . $conn->connect_error);
    }

    // Используем подготовленные запросы
    $query = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "Вход выполнен!";
    } else {
        echo "Неверные учетные данные.";
    }
    $stmt->close();
    $conn->close();
}

// Уязвимость 3: Выполнение команд (удалено)
// Код с shell_exec удален, так как это критическая уязвимость.

// Уязвимость 4: Небезопасные куки
$session_id = bin2hex(random_bytes(16)); // Генерация случайного ID
setcookie("session_id", $session_id, [
    'expires' => time() + 3600,
    'path' => '/',
    'secure' => true, // Только через HTTPS
    'httponly' => true, // Защита от XSS
    'samesite' => 'Strict'
]);

// Уязвимость 5: Небезопасная загрузка файлов
if ($_FILES['file']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "/uploads/";
    $allowed_extensions = ['jpg', 'png', 'pdf'];
    $file_name = $_FILES['file']['name'];
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    // Проверка расширения
    if (!in_array($file_extension, $allowed_extensions)) {
        die("Недопустимый тип файла.");
    }

    // Генерация уникального имени
    $new_file_name = uniqid() . '.' . $file_extension;
    $target_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_path)) {
        echo "Файл загружен!";
    } else {
        echo "Ошибка загрузки.";
    }
}
?>