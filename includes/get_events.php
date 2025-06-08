<?php
require '../db.php';
header('Content-Type: application/json');

$sql = "SELECT id, titulo as title, data as start, 
               CONCAT(data, ' ', hora) as start_full,
               tipo, local as location, descricao 
        FROM eventos 
        WHERE data >= CURDATE() - INTERVAL 1 MONTH
        ORDER BY data ASC";

$result = $conn->query($sql);
$events = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $events[] = [
            'id' => $row['id'],
            'title' => $row['title'],
            'start' => $row['start'],
            'allDay' => empty($row['start_full']),
            'extendedProps' => [
                'tipo' => $row['tipo'],
                'local' => $row['location'],
                'descricao' => $row['descricao']
            ]
        ];
    }
}

echo json_encode($events);
$conn->close();
?>