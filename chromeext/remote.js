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
  xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function()
  {
    if (xhr.readyState == 4 && xhr.status == 200) {
     var ret = JSON.parse(xhr.responseText);
     if (status == ret.id) {
       if (callback) callback(ret);
     }
    } else if (xhr.readyState == 4) {
     alert("Error: returned status code " + xhr.status + " " + xhr.statusText);
    }
  }
  xhr.open("POST", "http://localhost/sm/jsonrpc.php", true);
  xhr.setRequestHeader("Content-Type", "application/json");
  xhr.send(json);
}