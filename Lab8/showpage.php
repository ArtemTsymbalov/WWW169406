<?php

function show_page($id, $link) {
    $id_clear = htmlspecialchars($id);
    $query = "SELECT * FROM page_list WHERE page_title='$id_clear' LIMIT 1;";
    $result = mysqli_query($link, $query);

    $row = mysqli_fetch_array($result);

    if (empty($row)) {
        $web = '<h1>Page not faund</h1>';
    } else {
        $web = $row['page_content'];
    }
    
    return $web;

}