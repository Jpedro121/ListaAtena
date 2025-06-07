<?php
require 'db.php';

header('Content-Type: application/json');

// Configurações
$default_color = '#001f3f';
$event_colors = [
    'palestra' => '#FFD700',
    'workshop' => '#2ECC40',
    'reuniao' => '#FF851B',
    'outro' => '#7FDBFF'
];

try {
    // Validação e filtros
    $filters = [];
    $params = [];
    
    // Filtro por tipo de evento
    if (isset($_GET['tipo']) && in_array($_GET['tipo'], array_keys($event_colors))) {
        $filters[] = "tipo = ?";
        $params[] = $_GET['tipo'];
    }
    
    // Filtro por intervalo de datas
    if (isset($_GET['start']) && isset($_GET['end'])) {
        $filters[] = "data BETWEEN ? AND ?";
        $params[] = $_GET['start'];
        $params[] = $_GET['end'];
    }
    
    // Construção da query SQL
    $sql = "SELECT id, titulo, data, hora, tipo, descricao FROM eventos";
    
    if (!empty($filters)) {
        $sql .= " WHERE " . implode(" AND ", $filters);
    }
    
    $sql .= " ORDER BY data, hora";
    
    // Preparação da query
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        throw new Exception("Erro na preparação da query: " . $conn->error);
    }
    
    // Bind parameters se houver filtros
    if (!empty($params)) {
        $types = str_repeat('s', count($params));
        $stmt->bind_param($types, ...$params);
    }
    
    // Execução
    if (!$stmt->execute()) {
        throw new Exception("Erro na execução da query: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    $events = [];
    
    while ($row = $result->fetch_assoc()) {
        $event = [
            'id' => $row['id'],
            'title' => htmlspecialchars($row['titulo'], ENT_QUOTES, 'UTF-8'),
            'start' => $row['data'] . ($row['hora'] ? 'T' . $row['hora'] : ''),
            'color' => $event_colors[$row['tipo']] ?? $default_color,
            'extendedProps' => [
                'tipo' => $row['tipo'],
                'descricao' => !empty($row['descricao']) ? htmlspecialchars($row['descricao'], ENT_QUOTES, 'UTF-8') : null
            ]
        ];
        
        $events[] = $event;
    }
    
    // Resposta de sucesso
    echo json_encode([
        'success' => true,
        'data' => $events,
        'count' => count($events)
    ]);
    
} catch (Exception $e) {
    // Resposta de erro
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Erro ao recuperar eventos: ' . $e->getMessage(),
        'error' => $e->getMessage()
    ]);
} finally {
    // Fechamento de recursos
    if (isset($stmt)) {
        $stmt->close();
    }
    $conn->close();
}
?>