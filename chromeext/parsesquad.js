function scrapepage() {
  var ids = [];
  var html = document.body.innerHTML;
  var players = html.match(/jugador.php\?id_jugador=([0-9]+)/g);
  if (!players) {
    return;
  }
  for (var i=0; i < players.length; i++) {
    var player = players[i].match(/jugador.php\?id_jugador=([0-9]+)/);
    ids.push(Number(player[1]));
  }
  remote("exists", {'ids': ids}, function(result) {
    if (result.error) {
      alert(result.error.message);
      return;
    }
    var kiddos = document.getElementsByClassName('fl verde');
    for (var i = 0; i < kiddos.length; i++) {
      var kiddo = kiddos[i];
      var href = kiddo.parentElement.parentElement.firstChild.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.firstChild.href;
      var idcheck = href.match(/jugador.php\?id_jugador=([0-9]+)/);
      if (result.params.exists[idcheck]) {
        // player is for sale
        var forsale = document.createElement('img');
        forsale.src = 'http://chiaraquartet.net/sm/chromeext/icon16.png';
        kiddo.appendChild(this.forsale);
      }
    }
  });
}
scrapepage();