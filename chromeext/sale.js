var player = {
  players: {},
  checkExists: function(ids, callback)
  {
    var self = this;
    remote("exists", {'ids': ids}, function(result) {
      if (result.error) {
        if (sm_debug) {
          alert(result.error.message);
        } else {
          console.log(result.error.message);
        }
        return;
      }
      for (var i = 0; i < ids.length; i++) {
        if (result.params.exists[ids[i]]) {
            self.installCallback(ids[i]);
        }
      }
    });
  },
  installCallback: function(id)
  {
    var kiddos = document.getElementsByClassName("nombrejugador");
    for (var i = 0; i < kiddos.length; i++) {
        if (!kiddos[i].firstChild.href.match(new RegExp('/id_jugador=' + id + '/'))) {
            continue;
        }
        kiddos[i].parentNode // td
        .parentNode // tr
        .parentNode // tbody
        .parentNode // table
        .parentNode // div b1
        .parentNode // div br
        .parentNode // div tr
        .parentNode // div t1
        .parentNode // div caja50
        .nextSibling // br brfin
        .nextSibling // #textnode
        .nextSibling // div botones
        .firstChild // #textnode
        .nextSibling // reject button
        .nextSibling // #textnode
        .nextSibling // accept button
        .addEventListener("click", this.deletePlayer(id));
        return;
    }
  },
  codes: {},
  deletePlayer: function(idtouse)
  {
    var self = this;
    var id = Number(idtouse);
    return function() {
      var musthavecode = false;
      var code = false;
      if (self.codes[id]) {
        code = self.codes[id];
      } else {
        musthavecode = true;
      }
      if (musthavecode) {
        code = prompt("Player is listed for sale, but is now being sold.  Please enter the player update code to remove the listing",
                                  code);
        if (!code) {
          alert("Cannot delete the listing without a player code, please do this manually");
          return;
        }
      }
      remote("delete", {id: id, code: code}, function(result) {
        if (result.error) {
          if (sm_debug) {
            alert(result.error.message);
          } else {
            console.log(result.error.message);
          }
        } else {
          alert("Successfully removed player from transfer list");
          delete self.codes[result.params.id];
          chrome.storage.sync.set({'SMGregsList.codes': self.codes});
        }
      }, true);
    }
  },
  scrapepage: function() {
    var html = document.body.innerHTML;
    var sales = html.match(/id_jugador=([0-9]+)/g);
    var ids = [];
    for (var i = 0; i < sales.length; i++) {
        ids.push(Number(sales[i].match(/id_jugador=([0-9]+)/)[1]));
    }
    this.checkExists(ids);
  }
}
chrome.storage.sync.get(['SMGregsList.codes'], function(a) {
  player.codes = a['SMGregsList.codes'];
  if (!player.codes) {
    player.codes = {};
  }
});
player.scrapepage();