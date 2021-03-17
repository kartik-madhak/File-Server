<!doctype html>
<html lang="en">

<?php include('layout/head.php') ?>

<body>

<div class="container text-center">
    <div class="display-3 font-italic"><?php echo $_ENV['APP_NAME'] ?></div>
    <div class="font-italic"><?php echo $_ENV['APP_MOTTO'] ?></div>
    <div class="border rounded mt-5 p-4 w-50 mx-auto">
        <div class="display-4">Register</div>
        <div>
            <form class="mt-3" action="/register" method="post">
                <input class="form-control mt-2" type="text" placeholder="Username" name="name"
                       value="<?php echo $name ?? '' ?>" required>
                <input class="form-control mt-2" type="email" placeholder="Email" name="email"
                       value="<?php echo $email ?? '' ?>" required>
                <input class="form-control mt-2" type="password" placeholder="Password" name="password" id="password"
                       oninput="validate()" minlength="8" required>
                <input class="form-control mt-2" type="password" placeholder="Repeat Password" name="re_password"
                       id="re_password" oninput="validate()" required >
                <input type="submit" class="btn btn-outline-success mt-2">
            </form>

            <div id="error" class="text-danger font-weight-bold"> <?php echo $error ?? '' ?> </div>
        </div>
    </div>
</div>

<script>
    function validate() {
        if ($('#password').val() !== $('#re_password').val()) {
            $('#error').html('Passwords do not match');
        } else $('#error').html('');
    }
</script>

</body>

</html>