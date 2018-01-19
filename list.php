<?php

// [ВОПРОС] Как сделать чтобы переменная '$directoryName', которая объявлена в 'admin.php' была видна из файла 'list.php'?
$directoryName = 'test';

// Сканируем директорию './test' на наличие содержимого:
$fileList = scandir('./' . $directoryName);

// Выбираем из содержимого только фалы '*.json':
foreach ($fileList as $key => $value) {
    if (stristr($value, '.') === '.json') {
        $fileListJson[] = $value;
    }
}

?>

<!DOCTYPE html>
<html>
    <meta charset="utf-8">
    <title>Форма загрузки файла</title>
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <body>
        <main>
            <section class="info-section info-section-list">
                <h1>Загруженные файлы</h1>
                <? foreach ($fileListJson as $key => $value) { ?>
                    <a href="test.php"><?= $value ?></a>
                <? } ?>
            </section>
        </main>
    </body>
</html>
