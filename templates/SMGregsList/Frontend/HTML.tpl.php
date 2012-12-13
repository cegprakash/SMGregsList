<!DOCTYPE HTML>
<html lang="en">
 <head>
  <title>Greg's List - Striker Manager transfer market <?php echo $context->subtitle ?></title>
  <link href="bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
   <style media="screen" type="text/css">
    .form-horizontal .control-group {
      margin-bottom: 1px;
    }
    input[type="text"] {
     height: 15px;
    }
    select {
     height: 25px;
    }
   </style>
 </head>
 <body>
  <div class="row-fluid">
   <div class="span12">
  <?php echo $savant->render($context->getBody()) ?>
  </div>
   </div>
  <script src="http://code.jquery.com/jquery-latest.js"></script>
  <script src="bootstrap/js/bootstrap.min.js"></script>
 </body>
</html>