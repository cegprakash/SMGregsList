var info;
var playerhtml;
var received = [];
var sendthis = false;
var getplayer = false;
var parenthtml = false;
var currentTab = null;
chrome.extension.onMessage.addListener(
  function(player, sender, sendResponse) {
    switch (player[0]) {
      case 1 :
        info = player[1];
        received[0] = 1;
        break;
      case 2 :
        if (player[1]) {
          info.progression = player[1];
        }
        received[1] = 1;
        break;
      case 3 :
        if (player[1]) {
         info.forecast = player[1];
        }
        received[2] = 1;
        break;
      case 4 :
        info.skills = player[1];
        received[3] = 1;
        break;
      case 5 :
        parenthtml = player[1];
        if (sendthis) {
          sendthis(parenthtml);
          sendthis = null;
        }
        break;
      case 6 :
        if (parenthtml) {
          sendResponse(parenthtml);
        } else {
          sendthis = sendResponse;
        }
        break;
      case 7 :
        if (received.length == 4) {
          sendResponse(info);
        } else {
          getplayer = sendResponse;
        }
        break;
    }
    if (received.length == 4 && getplayer) {
      getplayer(info);
      getplayer = false;
    }
    return true;
  });