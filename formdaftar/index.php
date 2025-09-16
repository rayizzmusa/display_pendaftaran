<?php
if (preg_match("/\bindex.php\b/i", $_SERVER['REQUEST_URI'])) {
    exit;
} else {
    switch ($gket) {
        case "tdk":
            include "tidak.php";
            break;
        case "ya":
            include "ya.php";
            break;
    }
}
