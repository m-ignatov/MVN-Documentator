<?php

$phpInput = json_decode(file_get_contents('php://input'), true);

require_once "../src/User.php";

$user = new User(
    $phpInput['firstName'],
    $phpInput['lastName'],
    $phpInput['courseYear'],
    $phpInput['courseName'],
    $phpInput['facultyNumber'],
    $phpInput['groupNumber'],
    $phpInput['birthday'],
    $phpInput['zodiac'],
    $phpInput['link'],
    $phpInput['photo'],
    $phpInput['motivation'],
    $phpInput['signature']
);

try {
    $user->validate();
    $user->storeInDb();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
    ]);
}
