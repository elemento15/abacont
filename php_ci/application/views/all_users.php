<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">

    <title>Abacont - All Users</title>

    <!-- Bootstrap core CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/styles.css" rel="stylesheet">

    <script src="../libs/jquery.js"></script>
    <script type="text/javascript">
      $(document).ready(function () {
        $.ajax({
            url: 'allUsers',
            type: 'POST',
            dataType: 'JSON',
            data: {},
            success: function (data) {
              var node = '';
              
              data.forEach(function(item) {
                node  = '<tr>';
                node += '<td>'+ item.usuario +'</td>';
                node += '<td>'+ item.nombre +'</td>';
                node += '<td class="text-center">';
                if (item.activo == "1") {
                  node += '<span class="label label-primary">SI</span>';
                } else {
                  node += '<span class="label label-warning">NO</span>';
                }
                node += '</td>';
                node += '<td class="text-center">'+ item.fecha +'</td>';
                node += '</tr>';
                $('table.table').append(node);
              });
              
            }
          });

      });
    </script>
  </head>

  <body>
    <div class="container">
      <div class="row" style="margin-top: 20px;">
        <div class="col-sm-8 col-sm-offset-2">
          <table class="table table-condensed table-striped">
            <thead>
              <tr>
                <td class="text-center">Usuario</td>
                <td class="text-center">Nombre</td>
                <td class="text-center">Activo</td>
                <td class="text-center">Fecha</td>
              </tr>
            </thead>
            <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>
