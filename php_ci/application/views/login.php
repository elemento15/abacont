<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <title>Abacont - Login</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/login.css" rel="stylesheet">

    <script src="libs/jquery.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#formSignIn').submit(function (evt) {
          var user = $('#inputUser').val();
          var pass = $('#inputPassword').val();

          evt.preventDefault();
          evt.stopPropagation();

          $.ajax({
            url: 'main/login',
            type: 'POST',
            dataType: 'JSON',
            data: {
              user: user,
              pass: pass
            },
            success: function (response) {
              var data = response.data;
              if (response.success) {
                window.location = 'index.php';
              } else {
                alert(response.msg || 'Error loging in');
              }
            },
            error: function () {
              alert('Error loging in');
            }
          });
        });
      });
    </script>
  </head>

  <body>
    <div class="container">
      <form class="form-signin" id="formSignIn">
        <h2 class="form-signin-heading">Bienvenido</h2>
        <label for="inputUser" class="sr-only">Usuario</label>
        <input type="text" id="inputUser" class="form-control" placeholder="Usuario" required autofocus autocomplete="off">
        <label for="inputPassword" class="sr-only">Password</label>
        <input type="password" id="inputPassword" class="form-control" placeholder="Password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit">Ingresar</button>
      </form>
    </div>
  </body>
</html>
