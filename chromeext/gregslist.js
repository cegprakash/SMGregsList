{
  xhr = new XMLHttpRequest();
  xhr.onreadystatechange = function()
  {
    if (xhr.readyState == 4 && xhr.status == 200) {
     alert(xhr.responseText);
    } else if (xhr.readyState == 4) {
     alert("Error: returned status code " + xhr.status + " " + xhr.statusText);
    }
    xhr.open("POST", "test.asp", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  }
}
