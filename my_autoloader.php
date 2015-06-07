<?php
spl_autoload_register(function ($class) {
    require __DIR__ . '/classes/' . $class . '.php';
});