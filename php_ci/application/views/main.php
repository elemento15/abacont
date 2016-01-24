<!DOCTYPE html>
<html>
  <head>
  	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>AbaCont</title>
    
    <!-- styles -->
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
    <!-- main require module -->
    <!--<script data-main="app/main.js" src="libs/require.js"></script>-->

    <!-- temp -->
    <script type="text/javascript" src="libs/jquery.js"></script>
    <script type="text/javascript" src="libs/bootstrap.js"></script>
  </head>
  <body>
  	<!-- Fixed navbar -->
    <nav class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">ABACONT</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Inicio</a></li>
            <li><a href="#ingresos">Ingresos</a></li>
            <li><a href="#gastos">Gastos</a></li>
            <li class="dropdown">
              <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Catálogos <span class="caret"></span></a>
              <ul class="dropdown-menu">
                <li><a href="#cuentas">Cuentas</a></li>
                <li role="separator" class="divider"></li>
                <li><a href="#clases">Clases</a></li>
                <li><a href="#subclases">Subclases</a></li>
                <!--<li role="separator" class="divider"></li>
                <li class="dropdown-header">Nav header</li>
                <li><a href="#">Separated link</a></li>
                <li><a href="#">One more separated link</a></li>-->
              </ul>
            </li>
          </ul>
          <!--<ul class="nav navbar-nav navbar-right">
            <li><a href="../navbar/">Default</a></li>
            <li><a href="../navbar-static-top/">Static top</a></li>
            <li class="active"><a href="./">Fixed top <span class="sr-only">(current)</span></a></li>
          </ul>-->
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container"></div>
  </body>
</html>