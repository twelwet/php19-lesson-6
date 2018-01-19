<?php

$directoryName = 'test';
$inputFileName = 'quiz';
$structureArray = [
    'question' => 'Текст вопроса',
    'answer' => [
            'option-1' => 'Ответ 1',
            'option-2' => 'Ответ 2',
            'option-3' => 'Ответ 3'
    ],
    'correct' => 'Ключ массива ответов'
];
$structure = false;

// Создаем директорию './test', если ее не существует:
if (is_dir($directoryName) === false) {
    mkdir('./' . $directoryName, 0777);
}

if (isset($_FILES[$inputFileName])) {
    $fileInfo = $_FILES[$inputFileName];
    $fileName = $fileInfo['name'];
    $fileTempName = $fileInfo['tmp_name'];
    $fileType = $fileInfo['type'];
    $fileErrorCode = $fileInfo['error'];
    $filePath = $directoryName . '/' . $fileName;
    $log = [];
    // Передаем файл в директорию:
    if (move_uploaded_file($fileTempName, $filePath) && $fileErrorCode === 0) {
        $log['upload'] = 'Успех: файл <code>' . $fileName . '</code> передан на сайт';
        // Проверка на тип загружаемого файла:
        if ($fileType === 'application/json') {
            $log['type'] = 'Успех: тип файла <code>' . $fileType . '</code> поддерживается';
            // Конвертируем содержимое файла в массив:
            $dataArray = json_decode(file_get_contents($fileName), true);
            // Проверка структуры массива:
            foreach ($dataArray as $numberKey => $value) {
                if (array_keys($value) === array_keys($structureArray)) {
                    $structure = true;
                } else {
                    $structure = false;
                    goto finish;
                }
            }
            finish:
            if ($structure) {
                $log['structure'] = 'Успех: файл <code>' . $fileName . '</code> имеет корректную структуру';
            } else {
                $log['structure'] = 'Ошибка: файл <code>' . $fileName . '</code> имеет некорректную структуру';
                // Удаляем файл:
                if(unlink($filePath)) {
                    $log['delete'] = 'Успех: файл <code> ./' . $filePath . '</code> удален с сайта';
                } else {
                    $log['delete'] = 'Ошибка: файл <code> ./' . $filePath . '</code> не удален с сайта';
                };
            }
        } else {
            $log['type'] = 'Ошибка: тип файла <code>' . $fileType . '</code> не поддерживается';
            // Удаляем файл:
            if(unlink($filePath)) {
                $log['delete'] = 'Успех: файл <code> ./' . $filePath . '</code> удален с сайта';
            } else {
                $log['delete'] = 'Ошибка: файл <code> ./' . $filePath . '</code> не удален с сайта';
            };
        }
    } else {
        $log['upload'] = 'Ошибка: файл <code>' . $fileName . '</code> не передан на сайт. Код ошибки: ' . $fileInfo['error'];
    }
};

// Записываем в массив названия файлов с расширением json:
$fileList = scandir('./' . $directoryName);
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
            <section class="first-block">
                <section class="structure-section">
                    <h1>Корректная структура</h1>
                    <p>"<?= array_keys($structureArray)[0] ?>": "<?= $structureArray['question'] ?>"</p>
                    <p>"<?= array_keys($structureArray)[1] ?>": {</p>
                    <p>"<?= array_keys($structureArray['answer'])[0] ?>": "<?= $structureArray['answer']['option-1'] ?>",</p>
                    <p>"<?= array_keys($structureArray['answer'])[1] ?>": "<?= $structureArray['answer']['option-2'] ?>",</p>
                    <p>"<?= array_keys($structureArray['answer'])[2] ?>": "<?= $structureArray['answer']['option-3'] ?>",</p>
                    <p>и т.д. }</p>
                    <p>"<?= array_keys($structureArray)[2] ?>": "option-#"</p>
                </section>
                <section class="wrapper-section">
                    <section class="form-section">
                        <h1>Загрузите файл</h1>
                        <form enctype="multipart/form-data" method="post">
                            <input type="file" name="quiz">
                            <input type="submit" value="Отправить">
                        </form>
                    </section>
                    <section class="info-section">
                        <h1><a href="list.php">Загруженные файлы</a></h1>
                        <? foreach ($fileListJson as $key => $value) { ?>
                            <p><code><?= $value ?></code></p>
                        <? } ?>
                    </section>
                </section>
            </section>
            <section class="second-block">
                <section class="log-section">
                    <h1>Лог</h1>
                    <? foreach ($log as $key => $value) { ?>
                        <p><?= $value ?></p>
                    <? } ?>
                </section>
            </section>
        </main>
    </body>
</html>