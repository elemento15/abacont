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
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/login.css" rel="stylesheet">

    <script src="../libs/jquery.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $('#formRegister').submit(function (evt) {
          var data = {
            user : $('[name="user"]').val(),
            name : $('[name="name"]').val(),
            email: $('[name="email"]').val(),
            pass : $('[name="pass"]').val(),
            confirm: $('[name="confirmation"]').val()
          };

          evt.preventDefault();
          evt.stopPropagation();

          $.ajax({
            url: 'main/register',
            type: 'POST',
            dataType: 'JSON',
            data: data,
            success: function (response) {
              var data = response.data;
              if (response.success) {
                alert('Registro exitoso');
              } else {
                alert(response.msg || 'Error en el registro');
              }
            },
            error: function () {
              alert('Error en el registro');
            }
          });
        });
      });
    </script>
  </head>

  <body>
    <div class="container">
      <div class="row-fluid">
        <div class="col-sm-8 col-sm-offset-2 col-md-6 col-md-offset-3">
          <form class="form" id="formRegister">
            <h3 class="text-primary">Registro</h3>
            
            <div class="form-group">
              <label>Usuario:</label>
              <input type="text" name="user" class="form-control" required autofocus autocomplete="off" maxlength="10" placeholder="Mínimo 6 caracteres (letras y números)">
            </div>

            <div class="form-group">
              <label>Nombre:</label>
              <input type="text" name="name" class="form-control" required autocomplete="off" maxlength="150" placeholder="Nombre completo">
            </div>

            <div class="form-group">
              <label>Email:</label>
              <input type="text" name="email" class="form-control" required autocomplete="off" maxlength="250" placeholder="Email">
            </div>

            <div class="form-group">
              <label>Contraseña:</label>
              <input type="password" name="pass" class="form-control" required placeholder="Mínimo 6 caracteres">
            </div>

            <div class="form-group">
              <label>Confirmación:</label>
              <input type="password" name="confirmation" class="form-control" required placeholder="Igual a la contraseña">
            </div>

            <button type="submit" class="btn btn-success">Registrar</button>

            <a class="pull-right" href="/">Volver al Login</a>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>
