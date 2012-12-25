chrome.extension.sendMessage([6], function(response) {
  var info = player.scrapepage(response);
  info.progression = info.forecast = 0;
  if (info) {
    
    xhr = new XMLHttpRequest();
    xhr.onreadystatechange = function()
    {
      if (xhr.readyState == 4 && xhr.status == 200) {
       info.skills = player.scrapeskills(xhr.responseText);
      } // ignore all failures
    };
    xhr.open("GET", "http://en3.strikermanager.com/powerups.php?id_jugador=" + info.id, true);
    xhr.send();
    
    xhr2 = new XMLHttpRequest();
    xhr2.onreadystatechange = function() {
      if (xhr2.readyState == 4 && xhr.status == 200) {
        info.progression = player.scrapeprogression(xhr2.responseText);
      }
    };
    xhr2.open("GET", "http://en3.strikermanager.com/jugador_entrenamiento.php?id_jugador=" + info.id, true);
    xhr2.send();
    
    xhr3 = new XMLHttpRequest();
    xhr3.onreadystatechange = function() {
      info.forecast = player.scrapeforecast(xhr3.responseXML);
    }
    xhr3.open("GET", "http://en3.strikermanager.com/jugador_graf.php?id=" + info.id + "&car=media", true);
    xhr3.send();
  }
});
