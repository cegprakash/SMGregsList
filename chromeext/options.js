// Saves options to localStorage.
function save_options() {
  var select = document.getElementById("debug");
  var debugvalue = select.children[select.selectedIndex].value;
  chrome.storage.local.set({"debug_setting_SM": debugvalue}, function() {
      // Update status to let user know options were saved.
      var status = document.getElementById("status");
      status.innerHTML = "Options Saved.";
      setTimeout(function() {
        status.innerHTML = "";
      }, 750);
  });
}

// Restores select box state to saved value from localStorage.
function restore_options() {
  chrome.storage.local.get(["debug_setting_SM"], function(ret) {
    var favorite = ret["debug_setting_SM"];
    if (!favorite) {
      return;
    }
    var select = document.getElementById("debug");
    for (var i = 0; i < select.children.length; i++) {
      var child = select.children[i];
      if (child.value == favorite) {
        child.selected = "true";
        break;
      }
    }
  });
}
document.addEventListener('DOMContentLoaded', restore_options);
document.querySelector('#save').addEventListener('click', save_options);