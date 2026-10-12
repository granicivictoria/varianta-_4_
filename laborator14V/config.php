<?php
session_start();

define('UPLOAD_DIR', __DIR__ . '/uploads/');
define('USERS_FILE', __DIR__ . '/users.json');

// Creare foldere/fișiere automate
if (!file_exists(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0755, true);
if (!file_exists(USERS_FILE)) file_put_contents(USERS_FILE, json_encode([]));

function get_users() {
    return json_decode(file_get_contents(USERS_FILE), true) ?? [];
}

function get_user_dir($user) {
    $dir = UPLOAD_DIR . preg_replace('/[^a-zA-Z0-9_-]/', '', $user) . '/';
    if (!file_exists($dir)) mkdir($dir, 0755, true);
    return $dir;
}