sm_debug = false;
extreme_sm_debug = false;
chrome.storage.local.get(["debug_setting_SM","extremedebug_setting_SM"], function(ret) {
  if (ret.debug_setting_SM) {
    sm_debug = true;
  }
  if (ret.extremedebug_setting_SM) {
    extreme_sm_debug = true;
  }
});
