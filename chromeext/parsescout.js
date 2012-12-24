function scrapepage() {
  var ids = [];
  var html = document.body.innerHTML;
  var players = html.match(/id_jugador=([0-9]+)/g);
  if (!players) {
    return;
  }
  for (var i=0; i < players.length; i++) {
    var player = players[i].match(/id_jugador=([0-9]+)/);
    ids.push(Number(player[1]));
  }
  remote("exists", {'ids': ids}, function(result) {
    if (result.error) {
      if (sm_debug) {
        alert(result.error.message);
      } else {
        console.log(result.error.message);
      }
      return;
    }
    for (var b = 0; b < ['1','2'].length; b++) {
      var j = ['1', '2'][b];
      var kiddos = document.getElementsByClassName('tipo' + j);
      for (var i = 0; i < kiddos.length; i++) {
        if (kiddos[i].firstChild.nextSibling.firstChild.tagName !== "IMG") continue;
        // I love DOM...
        var kiddo = kiddos[i].firstChild.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling
          .nextSibling.nextSibling.nextSibling.nextSibling.nextSibling.nextSibling;
        var href = kiddos[i].firstChild.nextSibling.nextSibling.nextSibling.firstChild.href;
        var idcheck = href.match(/id_jugador=([0-9]+)/);
        if (result.params.exists[Number(idcheck[1])]) {
          // player is for sale
          var forsale = document.createElement('img');
          forsale.src = 'chrome://__MSG_@@extension_id__/chromeext/icon16.png';
          forsale.title = 'Player is for sale by Transfer Agreement';
          forsale.style.height = "15px;";
          kiddo.appendChild(forsale);
        }
      }
    }
  });
}
scrapepage();