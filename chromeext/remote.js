var session = 1;
function remote(message, params, callback)
{
  var status = session;
  var json = {
    id: session++,
    message: message,
    params: params
  }
  json = JSON.stringify(json);
  var xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function()
  {
    if (xhr.readyState == 4 && xhr.status == 200) {
     if (!xhr.responseText) {
      if (sm_debug) {
       alert("internal error: empty response");
      } else {
       console.log("internal error: empty response");
      }
      return;
     }
     try {
      var ret = JSON.parse(xhr.responseText);
      if (status == ret.id) {
        if (callback) callback(ret);
      }
     } catch (e) {
      if (sm_debug) {
        alert("internal JSON parsing error, text returned was: " + xhr.responseText);
      } else {
        console.log("internal JSON parsing error, text returned was: " + xhr.responseText);
      }
      throw e;
     }
    } else if (xhr.readyState == 4) {
     if (sm_debug) {
      alert("Error: returned status code " + xhr.status + " " + xhr.statusText);
     } else {
      console.log("Error: returned status code " + xhr.status + " " + xhr.statusText);
     }
    }
  }
  xhr.open("POST", "http://chiaraquartet.net/sm/jsonrpc.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(json);
}