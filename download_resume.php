<?php
    $file = 'Jomai_Resumè.pdf';

    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="Jomai_Resumè.pdf"');
    header('Content-Length: ' . filesize($file));

    readfile($file);
    exit;
?>