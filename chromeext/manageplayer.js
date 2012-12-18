function sellplayer()
{
  var code = document.getElementById("code").value;
  // sell the player
  chrome.extension.sendMessage([7, code], function(ret) {
    if (ret.code) {
      alert("Player successfully listed, code is " + ret.code);
      document.getElementById("code").value = ret.code;
    }
  });
}