<?php
if (file_exists('config.php')) {
    require('config.php');
} else {
    die("Для корректной работы нужно создать конфигурационный файл config.php со следующими константами:</br>
DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_TABLE_VERSIONS");
}

function connectDB() {
    $errorMessage = 'Невозможно подключиться к серверу базы данных';
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
    if (!$conn)
        throw new Exception($errorMessage);
    else {
        $query = $conn->query('set names utf8');
        if (!$query)
            throw new Exception($errorMessage);
        else
            return $conn;
    }
}


// Получаем список файлов для миграций
function getMigrationFiles($conn) {
    // Находим папку с миграциями
    $sqlFolder = str_replace('\\', '/', realpath(dirname(__FILE__)) . '/');
    // Получаем список всех sql-файлов
    $allFiles = glob($sqlFolder . '*.sql');

    // Проверяем, есть ли таблица versions
    $query = sprintf('show tables from `%s` like "%s"', DB_NAME, DB_TABLE_VERSIONS);
    $data = $conn->query($query);
    $firstMigration = !$data->num_rows;

    // Первая миграция, возвращаем все файлы из папки sql
    if ($firstMigration) {
        $query = sprintf(
            'CREATE TABLE `versions` (
                `id` int(11) NOT NULL,
                `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
                `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci');
        $conn->query($query);
        return $allFiles;
    }

    // Ищем уже существующие миграции
    $versionsFiles = array();
    // Выбираем из таблицы versions все названия файлов
    $query = sprintf('select `name` from `%s`', DB_TABLE_VERSIONS);
    $data = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    // Загоняем названия в массив $versionsFiles
    // Не забываем добавлять полный путь к файлу
    foreach ($data as $row) {
        array_push($versionsFiles, $sqlFolder . $row['name']);
    }

    // Возвращаем файлы, которых еще нет в таблице versions
    return array_diff($allFiles, $versionsFiles);
}


// Накатываем миграцию файла
function migrate($conn, $file) {
    $query = sprintf($file);
    $conn->query($query);

    // Вытаскиваем имя файла, отбросив путь
    $baseName = basename($file);
    // Формируем запрос для добавления миграции в таблицу versions
    $query = sprintf('insert into `%s` (`name`) values("%s")', DB_TABLE_VERSIONS, $baseName);
    // Выполняем запрос
    $conn->query($query);
}


// Стартуем

// Подключаемся к базе
$conn = connectDB();

// Получаем список файлов для миграций за исключением тех, которые уже есть в таблице versions
$files = getMigrationFiles($conn);

// Проверяем, есть ли новые миграции
if (empty($files)) {
    echo 'Ваша база данных в актуальном состоянии.';
} else {
    echo 'Начинаем миграцию...<br><br>';

    // Накатываем миграцию для каждого файла
    foreach ($files as $file) {
        migrate($conn, $file);
        // Выводим название выполненного файла
        echo basename($file) . '<br>';
    }

    echo '<br>Миграция завершена.';
}

