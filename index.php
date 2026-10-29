<?php
require "db.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = trim($_POST["message"] ?? "");

    if ($message !== "" && strlen($message) <= 2000) {
        $stmt = $conn->prepare(
            "INSERT INTO messages (message) VALUES (?)"
        );
        $stmt->bind_param("s", $message);
        $stmt->execute();
        $stmt->close();
    }

    header("Location: index.php");
    exit;
}

$result = $conn->query(
    "SELECT message, created_at
     FROM messages
     ORDER BY id DESC
     LIMIT 100"
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Simple Chat</title>
</head>
<body>

<h2>Simple Network Chat</h2>

<form method="POST">
    <input type="text" name="message"
           placeholder="Type a message"
           maxlength="2000" required>
    <button type="submit">Send</button>
</form>

<hr>

<h3>Messages</h3>

<?php
$messages = [];

while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

foreach (array_reverse($messages) as $row):
?>
    <p>
        <?= htmlspecialchars($row["message"], ENT_QUOTES, "UTF-8") ?>
        <br>
        <small>
            <?= htmlspecialchars($row["created_at"], ENT_QUOTES, "UTF-8") ?>
        </small>
    </p>
    <hr>
<?php endforeach; ?>

</body>
</html>