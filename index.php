<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Админская панель</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 800px; margin: 0 auto; padding: 20px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; }
        input, select, textarea { width: 100%; padding: 8px; }
        button { padding: 10px 20px; background: #007bff; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .success { color: green; }
    </style>
</head>
<body>
    <h1>Отправка задачи в очередь</h1>
    <?php if (isset($_GET['success'])): ?>
        <p class="success">Задача успешно отправлена!</p>
    <?php endif; ?>
    <form action="send_task.php" method="POST">
        <div class="form-group">
            <label for="task_type">Тип задачи:</label>
            <select name="task_type" id="task_type">
                <option value="send_email">Отправить Email</option>
                <option value="generate_report">Сгенерировать отчет</option>
            </select>
        </div>
        <div class="form-group">
            <label for="delay">Задержка (в секундах):</label>
            <input type="number" name="delay" id="delay" value="10" min="0">
        </div>
        <div class="form-group">
            <label for="data">Данные задачи (JSON):</label>
            <textarea name="data" id="data" rows="5">{"email": "test@example.com", "message": "Привет, это тест!"}</textarea>
        </div>
        <button type="submit">Отправить задачу</button>
    </form>
</body>
</html>