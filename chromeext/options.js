var allcodes = {};
function save_options() {
  var select = document.getElementById("debug");
  var debugvalue = select.children[select.selectedIndex].value == "yes";
  var eselect = document.getElementById("extremedebug");
  var edebugvalue = select.children[eselect.selectedIndex].value == "yes";
  chrome.storage.local.set({"debug_setting_SM": debugvalue, "extremedebug_setting_SM": edebugvalue}, function() {
      if (chrome.runtime.lastError) {
        alert("Error: " + chrome.runtime.lastError.message);
      }
      // Update status to let user know options were saved.
      var status = document.getElementById("status");
      status.innerHTML = "Options Saved.";
      setTimeout(function() {
        status.innerHTML = "";
      }, 750);
  });
  if (document.getElementById('code').value) {
    allcodes[document.getElementById('manager').value] = document.getElementById('code').value;
    chrome.storage.sync.set({"SMGregsList.codes": allcodes}, function() {
        if (chrome.runtime.lastError) {
          alert("Error: " + chrome.runtime.lastError.message);
        }
        // Update status to let user know options were saved.
        var status = document.getElementById("status");
        status.innerHTML += "<br>Manager Code Saved.";
        setTimeout(function() {
          status.innerHTML = "";
        }, 750);
        var el = document.getElementById("codes");
        var txt = "";
        for (var i in allcodes) {
          txt += "Manager: <b>" + i + "</b>, code: <b>" + allcodes[i] + "</b><br>";
        }
        el.innerHTML = txt;
    });
  }
}

// Restores select box state to saved value from localStorage.
function restore_options() {
  chrome.storage.sync.get(["SMGregsList.codes"], function(ret) {
    allcodes = ret["SMGregsList.codes"];
    if (!allcodes) {
      allcodes = {};
      return;
    }
    var el = document.getElementById("codes");
    var txt = "";
    for (var i in allcodes) {
      txt += "Manager: <b>" + i + "</b>, code: <b>" + allcodes[i] + "</b><br>";
    }
    el.innerHTML = txt;
  });
  chrome.storage.local.get(null, function(ret) {
    var favorite = ret["debug_setting_SM"];
    if (favorite) {
      var select = document.getElementById("debug");
      for (var i = 0; i < select.children.length; i++) {
        var child = select.children[i];
        if (child.value == (favorite ? "yes" : "no")) {
          child.selected = "true";
          break;
        }
      }
    }
    favorite = ret["extremedebug_setting_SM"];
    if (!favorite) {
      return;
    }
    var select = document.getElementById("extremedebug");
    for (var i = 0; i < select.children.length; i++) {
      var child = select.children[i];
      if (child.value == (favorite ? "yes" : "no")) {
        child.selected = "true";
        break;
      }
    }
  });
}
document.addEventListener('DOMContentLoaded', restore_options);
document.querySelector('#save').addEventListener('click', save_options);