chrome.extension.sendMessage([5, document.body.innerHTML], function(response) {

  });
(function(){
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
          alert(result.error.message);
        } else {
          console.log(result.error.message);
        }
        return;
      }
      alert('New Players found in saved searches!');
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
        }
      });
    } else {
      remote("findplayers", {'manager' : user, 'code' : codes[user]}, getPlayers);
    }
  });
});
})();