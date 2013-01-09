(function() {
  var market = document.getElementsByClassName('botonesright');
  for (var i = 0;i < market.length; i++) {
    if (market[i].firstChild.nextSibling.className == "botoncorto") {
      var menuitem = document.createElement('a');
      menuitem.className = 'botoncorto';
      var test = location.href.match(/(en[1-3]?)\.strikermanager/);
      menuitem.href = 'http://chiaraquartet.net/sm/index.php/nosell/' + test[1] + '/';
      menuitem.appendChild(document.createTextNode("Transfer Market"));
      menuitem.target = "_blank";
      market[i].insertBefore(menuitem, market[i].firstChild.nextSibling);
      return;
    }
  }
})();