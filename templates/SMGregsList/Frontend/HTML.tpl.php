<!DOCTYPE HTML>
<html lang="en">
 <head>
  <title>Merc's List - Striker Manager transfer market <?php echo $context->subtitle ?></title>
  <link href="/sm/bootstrap/css/bootstrap.min.css" rel="stylesheet" media="screen">
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
    #adjust-label {
     width: 50px;
    }
    #adjust-controls {
     margin-left: 100px;
    }
    table.tablesorter thead tr .headerSortUp {
     background-image: url(/sm/bootstrap/img/asc.gif);
    }
    table.tablesorter thead tr .headerSortDown {
     background-image: url(/sm/bootstrap/img/desc.gif);
    }
    table.tablesorter {
     background-color: #CDCDCD;
     margin:10px 0pt 15px;
     font-size: 8pt;
     width: 100%;
     text-align: left;
    }
    table.tablesorter thead tr th, table.tablesorter tfoot tr th {
     background-color: #e6EEEE;
     border: 1px solid #FFF;
     font-size: 8pt;
     padding: 4px;
    }
    table.tablesorter thead tr .header {
     background-image: url(/sm/bootstrap/img/bg.gif);
     background-repeat: no-repeat;
     background-position: center right;
     cursor: pointer;
    }
    table.tablesorter tbody td {
     color: #3D3D3D;
     padding: 4px;
     background-color: #FFF;
     vertical-align: top;
    }
    table.tablesorter tbody tr.odd td {
     background-color:#F0F0F6;
    }
    table.tablesorter thead tr .headerSortDown, table.tablesorter thead tr .headerSortUp {
     background-color: #8dbdd8;
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
  <script src="/sm/bootstrap/js/bootstrap.min.js"></script>
  <script src="/sm/bootstrap/js/jquery.tablesorter.min.js"></script>
  <?php if ($context->getExtrarender()) {
   echo $context->getRawObject()->getExtrarender();
  } ?>
  <script type="text/javascript">
   if (document.getElementById('savesearch')) {
    $('#savesearch').popover();
   }
  </script>
 </body>
</html>