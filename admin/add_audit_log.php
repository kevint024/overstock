<?php
function addAuditLog($userId, $tableName, $recordId, $changeType, $changeDetails) {
    include('db_connection.php');

    $sql = "INSERT INTO audit_logs (user_id, table_name, record_id, change_type, change_details) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $userId, $tableName, $recordId, $changeType, $changeDetails);

    if (!$stmt->execute()) {
        echo "Error adding audit log: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
