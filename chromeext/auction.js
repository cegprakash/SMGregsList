var player = {
  players: {},
  checkExists: function(id, callback)
  {
    var self = this;
    remote("exists", {'id': id}, function(result) {
      if (result.error) {
        if (sm_debug) {
          alert(result.error.message);
        } else {
          console.log(result.error.message);
        }
        return;
      }
      if (result.params.exists) {
        self.installCallback(id);
      }
    });
  },
  installCallback: function(id)
  {
    var kiddos = document.getElementsByClassName("botonesform");
    kiddos[0]
    .firstChild // #textnode
    .nextSibling // send button
    .addEventListener("click", this.deletePlayer());
  },
  codes: {},
  deletePlayer: function(id)
  {
    var self = this;
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
      remote("delete", {id: id}, function(result) {
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
      });
    }
  },
  scrapepage: function() {
    var id = location.href.match(/id_jugador=([0-9]+)/)[1];
    this.checkExists(id);
  }
}
chrome.storage.sync.get(['SMGregsList.codes'], function(a) {
  player.codes = a['SMGregsList.codes'];
  if (!player.codes) {
    player.codes = {};
  }
});
player.scrapepage();