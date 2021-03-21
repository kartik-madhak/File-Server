<!doctype html>
<html lang="en">

<?php include('layout/head.php') ?>

<body>

<div class="container text-center">
    <div class="display-3 font-italic"><?php echo $_ENV['APP_NAME'] ?></div>
    <div class="font-italic"><?php echo $_ENV['APP_MOTTO'] ?></div>
    <div class="border rounded mt-5 p-4 w-50 mx-auto">
        <div class="display-4">Login</div>
        <div>
            <form class="mt-3" action="/login" method="post">
                <input class="form-control mt-2" type="email" placeholder="Email" name="email"
                       value="<?php echo $email ?? '' ?>" required>
                <input class="form-control mt-2" type="password" placeholder="Password" name="password" id="password"
                       minlength="8" required>
                <input type="submit" class="btn btn-outline-success mt-2">
            </form>

            <div id="error" class="text-danger font-weight-bold"> <?php echo $error ?? '' ?> </div>
            <div class="mt-4">
                <a href="/register">New? Register Here.</a>
            </div>
        </div>
    </div>
</div>
</body>

</html>