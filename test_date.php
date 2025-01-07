<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $due_time = $_POST['due_time'];
    $send_at = date('Y-m-d') . ' ' . $due_time;

    echo $send_at;
}

?>

<form method="POST">
    <div class="form-floating mb-3">
        <input type="time" name="due_time" class="form-control" required>
        <label for="due_time">
            Due Time:
        </label>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>