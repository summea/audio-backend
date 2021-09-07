<?php

require_once 'settings.php';

if ($_GET['songId']) {
    // ref: https://www.php.net/manual/en/function.filter-input
    // ref: https://www.php.net/manual/en/filter.filters.validate.php
    $songId = filter_input(INPUT_GET, 'songId', FILTER_VALIDATE_INT);

    // ref: https://www.php.net/manual/en/pdo.sqlitecreatefunction
    $db = new PDO('sqlite:songs.db');
    $sth = $db->prepare('
        SELECT *
        FROM songs
        LEFT JOIN albums_songs
        ON albums_songs.songId = songs.id
        WHERE id = :songId
    ');
    $sth->execute([':songId' => $songId]);
    // ref: https://www.php.net/manual/en/pdostatement.fetch.php
    $result = $sth->fetch(PDO::FETCH_ASSOC);

    // ref: https://www.php.net/manual/en/function.header
    // ref: https://developer.okta.com/blog/2021/08/02/fix-common-problems-cors
    header('Access-Control-Allow-Origin: '.$config['frontendUrl']);
    header('Access-Control-Allow-Methods: GET');
    echo json_encode($result);
}
