var sendthis = false;
var getplayer = false;
var parenthtml = false;
var currentTab = null;
chrome.extension.onMessage.addListener(
  function(player, sender, sendResponse) {
    switch (player[0]) {
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
    }
    return true;
  });