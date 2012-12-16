var info = scrapepage();
chrome.extension.sendMessage([1, info], function(response) {
  
});

xhr = new XMLHttpRequest();
xhr.onreadystatechange = function()
{
  var skills = {};
  if (xhr.readyState == 4 && xhr.status == 200) {
   var skills = scrapeskills(xhr.responseText);
   chrome.extension.sendMessage([4, info], function(response) {
    
   });
  } // ignore all failures
};
xhr.open("GET", "http://en3.strikermanager.com/powerups.php?id_jugador=" + info.id, true);
xhr.send();

xhr2 = new XMLHttpRequest();
xhr2.onreadystatechange = function() {
  if (xhr2.readyState == 4 && xhr.status == 200) {
    var progression = scrapeprogression(xhr2.responseText);
    if (progression) {
     chrome.extension.sendMessage([2, progression], function(response) {
     
     });
    } else {
     chrome.extension.sendMessage([2, false], function(response) {
     
     });
    }
  }
};
xhr2.open("GET", "http://en3.strikermanager.com/jugador_entrenamiento.php?id_jugador=" + info.id, true);
xhr2.send();

xhr3 = new XMLHttpRequest();
xhr3.onreadystatechange = function() {
  var forecast = scrapeforecast(xhr3.responseXML);
  if (forecast) {
   chrome.extension.sendMessage([3, forecast], function(response) {
  
   });
  } else {
   chrome.extension.sendMessage([3, false], function(response) {
  
   });
  }
}
xhr3.open("GET", "http://en3.strikermanager.com/jugador_graf.php?id=" + info.id + "&car=media", true);
xhr3.send();

