(function() {
  var market = document.getElementsByClassName('botonesright');
  for (var i = 0;i < market.length; i++) {
    if (market[i].firstChild.nextSibling.className == "botoncorto") {
      var menuitem = document.createElement('a');
      menuitem.className = 'botoncorto';
      menuitem.href = 'http://chiaraquartet.net/sm/index.php';
      menuitem.appendChild(document.createTextNode("Transfer Market"));
      menuitem.target = "_blank";
      market[i].insertBefore(menuitem, market[i].firstChild.nextSibling);
      return;
    }
  }
})();