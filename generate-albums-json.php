<?php

// ref: https://www.php.net/manual/en/pdo.sqlitecreatefunction
$db = new PDO('sqlite:songs.db');
$albumRows = $db->query('
    SELECT * FROM albums
    ORDER BY releaseDate DESC
')->fetchAll();
$output['albums'] = [];
foreach ($albumRows as $albumRow) {
    $albumData = [];
    $albumData['id'] = $albumRow['id'];
    $albumData['coverImage'] = $albumRow['coverImageUrl'];
    $albumData['name'] = $albumRow['name'];
    // ref: https://www.php.net/manual/en/pdo.prepare.php
    $sth = $db->prepare('
        SELECT * FROM albums_songs
        LEFT JOIN songs
        ON albums_songs.songId = songs.id
        WHERE albumId = :albumId
        ORDER BY albumId, albumSongNumber
    ');
    $sth->execute([':albumId' => $albumRow['id']]);
    $songRows = $sth->fetchAll();
    $albumData['songs'] = [];
    foreach ($songRows as $songRow) {
        array_push($albumData['songs'], [
            'id' => $songRow['id'],
            'name' => $songRow['name'],
            'url' => $songRow['songUrl'],
            'albumSongNumber' => $songRow['albumSongNumber']
        ]);
    }
    array_push($output['albums'], $albumData);
}

// ref: https://www.php.net/manual/en/function.file-put-contents.php
file_put_contents('albums.json', json_encode($output));
