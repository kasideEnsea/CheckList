<?php
require($_SERVER['DOCUMENT_ROOT'].'/database/сonnection.php');

function getMigrationFiles($conn) {
    $sqlFolder = str_replace('\\', '/', realpath(dirname(__FILE__)) . '/');
    $allFiles = glob($sqlFolder . '*.sql');
    $query = sprintf('show tables from `%s` like "%s"', DB_NAME, DB_TABLE_VERSIONS);
    $data = $conn->query($query);
    $firstMigration = !$data->num_rows;

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

    $versionsFiles = array();
    $query = sprintf('select `name` from `%s`', DB_TABLE_VERSIONS);
    $data = $conn->query($query)->fetch_all(MYSQLI_ASSOC);
    // Загоняем названия файлов в массив $versionsFiles
    // Добавляем полный путь к файлу
    foreach ($data as $row) {
        array_push($versionsFiles, $sqlFolder . $row['name']);
    }
    return array_diff($allFiles, $versionsFiles);
}


function migrate($conn, $file)
{
    // Формируем команду выполнения mysql-запроса из внешнего файла
    $command = sprintf('mysql -u%s -p%s -h %s -D %s < %s', DB_USER, DB_PASSWORD, DB_HOST, DB_NAME, $file);
    // Выполняем shell-скрипт
    shell_exec($command);
    // Вытаскиваем имя файла, отбросив путь
    $baseName = basename($file);
    $query = sprintf('insert into `%s` (`name`) values("%s")', DB_TABLE_VERSIONS, $baseName);
    $conn->query($query);
}

$conn = Connection::getInstance();

$files = getMigrationFiles($conn);

if (empty($files)) {
    echo 'Ваша база данных в актуальном состоянии.';
} else {
    echo 'Начинаем миграцию...<br><br>';

    foreach ($files as $file) {
        migrate($conn, $file);
        echo basename($file) . '<br>';
    }

    echo '<br>Миграция завершена.';
}

