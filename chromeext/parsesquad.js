function scrapepage() {
  var ids = [];
  var html = document.body.innerHTML;
  var players = html.match(/jugador.php?id_jugador=([0-9]+)/g);
  if (!players) {
    return;
  }
  for (var i=0; i < players.length; i++) {
    var player = players[i].match(/jugador.php?id_jugador=([0-9]+)/);
    ids.push(Number(player[1]));
  }
  remote("exists", {'ids': ids}, function(result) {
    if (result.error) {
      alert(result.error.message);
      return;
    }
    // TODO: use DOM to find each individual player, and add the for sale icon
  });
}
