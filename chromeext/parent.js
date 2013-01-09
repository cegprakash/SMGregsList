chrome.extension.sendMessage([5, document.body.innerHTML], function(response) {

  });
(function(){
var myalert = function(text)
  {
    var alerty = document.getElementById('iframemarco').contentDocument.getElementById('textoalerta');
    var parentthingy = document.getElementById('iframemarco').contentDocument.getElementById('mensajealerta');
    alerty.innerHTML = text;
    parentthingy.style.display = "inherit";
  };
var user = document.body.innerHTML.match(/<a target="marco" href="usuario.php" class="color_skin" ?>([^<]+)<\/a/)[1];
chrome.storage.local.get(["debug_setting_SM","extremedebug_setting_SM"], function(debg) {
  var dbg = debg;
  chrome.storage.sync.get(['SMGregsList.codes'], function(ret) {
    if (dbg.debug_setting_SM) {
      sm_debug = true;
    }
    if (dbg.extremedebug_setting_SM) {
      extreme_sm_debug = true;
    }
    var codes = ret['SMGregsList.codes'];
    if (!codes) {
      codes = {};
    }
    var getPlayers = function(result) {
      if (result.error) {
        if (sm_debug) {
          myalert(result.error.message);
        } else {
          console.log(result.error.message);
        }
        return;
      }
      var txt = "<div style=\"overflow-y: scroll; height: 200px;width: 200px;background-color:black;color:white;font-size:small;font-weight:bold;\">" +
                "New players are for sale that match your saved searches!<br><ol>";
      var p = result.params.players;
      if (!p.length) {
        return;
      }
      for (var i = 0; i < p.length; i++) {
        var desc = "";
        if (p[i].average) {
          if (p[i].age) {
            desc = p[i].average + "/" + p[i].age;
          } else {
            desc = p[i].average;
          }
          desc += " ";
        }
        if (p[i].position) {
          desc += p[i].position;
          desc += " ";
        }
        if (p[i].name) {
          desc += p[i].name;
        }
        txt += "<li><a href=\"/jugador.php?id_jugador=" + p[i].id + "\">" + desc + "</a></li>";
      }
      txt += "</ol></div>";
      myalert(txt);
    }
    if (!codes[user]) {
      remote("getmanager", user, function(result) {
        if (result.error) {
          if (sm_debug) {
            alert(result.error.message);
          } else {
            console.log(result.error.message);
          }
          return;
        }
        if (result.params.manager == user) {
          codes[user] = result.params.code;
          chrome.storage.sync.set({'SMGregsList.codes': codes}, function(result) {
          });
          remote("findplayers", {'manager' : user, 'code' : result.params.code}, getPlayers);
          setTimeout(function() {
            remote("findplayers", {'manager' : user, 'code' : codes[user]}, getPlayers);
          }, 60*60*1000);
        }
      });
    } else {
      remote("findplayers", {'manager' : user, 'code' : codes[user]}, getPlayers);
      setTimeout(function() {
        remote("findplayers", {'manager' : user, 'code' : codes[user]}, getPlayers);
      }, 60*60*1000);
    }
  });
});
})();