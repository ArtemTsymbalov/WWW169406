<?php
function PokazPodstrone($id) {
    // Czyszczenie $id, aby zapobiec atakom SQL Injection
    $id_clear =  htmlspecialchars($id);

    // Zapytanie SQL
    $query = "SELECT * FROM page_list WHERE id='$id_clear' LIMIT 1";
    $result = mysqli_query($link, $query);

    if (!$result) {
        return 'Błąd zapytania: ' . mysqli_error($link);
    }

    $row = mysqli_fetch_array($result);

    // Sprawdzenie, czy strona istnieje
    if (empty($row['id'])) {
        $Sweb = '[nie_znaleziono_strony]';
    } else {
        $Sweb = $row['page_content'];
    }

    return $Sweb;
}
?>
