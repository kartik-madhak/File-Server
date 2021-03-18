<!doctype html>
<html lang="en">
<?php include('layout/head.php') ?>
<body>
<h1>This is the home page</h1>
<p>
    <?php
    $user = $user ?? new User();
    echo 'Welcome' . $user['name'] . '<br>';
    ?>
</p>
<a href="/">Back</a>

</body>
</html>