<?php
// Database connection (adjust credentials as needed)
include 'db.php';
// Verifica se a conexão foi bem-sucedida  
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}


// Query para obter os eventos mais recentes (ajuste o nome da tabela e colunas)
$sql = "SELECT id, titulo, data , descricao FROM eventos ORDER BY data DESC LIMIT 5";
$result = $conn->query($sql);

echo "<h2>Eventos Mais Recentes</h2>";
if ($result && $result->num_rows > 0) {
    echo "<ul>";
    while($row = $result->fetch_assoc()) {
        echo "<li>";
        echo "<strong>" . htmlspecialchars($row['titulo']) . "</strong> ";
        echo "(" . date('d/m/Y', strtotime($row['data_evento'])) . ")<br>";
        echo nl2br(htmlspecialchars($row['descricao']));
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Nenhum evento encontrado.";
}

$conn->close();
?>