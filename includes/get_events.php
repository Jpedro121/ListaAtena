<?php
require 'db.php';

header('Content-Type: application/json');

$sql = "SELECT id, titulo, data, hora, tipo FROM eventos";
$result = $conn->query($sql);

$events = [];
while ($row = $result->fetch_assoc()) {
    $events[] = [
        'id' => $row['id'],
        'title' => $row['titulo'],
        'start' => $row['data'] . ($row['hora'] ? 'T' . $row['hora'] : ''),
        'color' => $row['tipo'] === 'palestra' ? '#FFD700' : '#001f3f'
    ];
}

echo json_encode($events);
$conn->close();
?>