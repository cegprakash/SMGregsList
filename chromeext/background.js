var info;
var received = [];
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
    }
    if (received.length == 4) {
      chrome.pageAction.show(sender.tab.id);
    }
  });

chrome.tabs.onUpdated.addListener(function (tabId, blah, blah2) {
  chrome.pageAction.hide(tabId);
});