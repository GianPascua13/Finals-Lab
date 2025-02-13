<?php
session_start();

$db = mysqli_connect('localhost', 'root', '', 'todo');

$errors = "";

if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = "Guest"; 
}

if (isset($_POST['filter'])) {
    $filter = $_POST['filter'];
    setcookie("last_filter", $filter, time() + (86400 * 30), "/"); 
} else {
    $filter = isset($_COOKIE['last_filter']) ? $_COOKIE['last_filter'] : '';
}

if (isset($_POST['submit'])) {
    $task = $_POST['task'];
    if (empty($task)) {
        $errors = "You must fill in the task";
    } else {
        mysqli_query($db, "INSERT INTO tasks (task) VALUES ('$task')");
        header('location: index.php');
    }
}

if (isset($_GET['del_task'])) {
    $id = $_GET['del_task'];
    mysqli_query($db, "DELETE FROM tasks WHERE id=$id");
    header('location: index.php');
}

$query = "SELECT * FROM tasks";
if (!empty($filter)) {
    $filter = mysqli_real_escape_string($db, $filter);
    $query .= " WHERE task LIKE '%$filter%'";
}
$tasks = mysqli_query($db, $query);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Group 4 | To-Do List with HTML, CSS, JAVASCRIPT, AND PHP</title>
    <link rel="stylesheet" href="style.css">
    <script src="script.js" defer></script>
</head>
<body>

<?php include('header.php'); ?>

<div class="heading">
    <h3>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h3>
    <h3>To-Do List</h3>
</div>

<form method="POST" action="index.php">
    <input type="text" name="filter" placeholder="Filter tasks" value="<?php echo htmlspecialchars($filter); ?>">
    <button type="submit" name="apply_filter">Apply Filter</button>
</form>

<form method="POST" action="index.php">
    <?php if (!empty($errors)) { ?>
        <p><?php echo $errors; ?></p>
    <?php } ?>
    <input type="text" name="task" class="task_input">
    <button type="submit" class="add_btn" name="submit">Add Task</button>
</form>

<table>
    <thead>
    <tr>
        <th>N</th>
        <th>Task</th>
        <th>Action</th>
    </tr>
    </thead>
    <tbody>
    <?php $i = 1; while ($row = mysqli_fetch_array($tasks)) { ?>
        <tr>
            <td><?php echo $i; ?></td>
            <td class="task"><?php echo htmlspecialchars($row['task']); ?></td>
            <td class="delete">
                <a href="index.php?del_task=<?php echo $row['id']; ?>">x</a>
            </td>
        </tr>
    <?php $i++; } ?>
    </tbody>
</table>

<?php include('footer.php'); ?>

</body>
</html>
