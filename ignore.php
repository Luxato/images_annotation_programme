<?php

//file_put_contents('./data/ignored.txt', $_POST['image']);
if (isset($_POST['image'])) {
    $file = fopen("./data/ignored.txt", "a");
    fwrite( $file, $_POST['image'] . "\n" );
}
