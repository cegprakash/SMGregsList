sm_debug = false;
chrome.storage.local.get(["debug_setting_SM"], function(ret) {
  if (ret.debug_setting_SM) {
    sm_debug = true;
  }
});
