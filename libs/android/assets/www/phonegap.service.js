sina = {};
sina.notification = {
  notify : function(options) {
    window.notification.NotificationNotify(JSON.stringify(options));
  },
  cancel : function(id) {
	  window.notification.cancel(id.toString());
  },
  cancelAll : function() {
	  window.notification.cancelAll();
  }
};

sina.utils = {
  start : function(saeAppName, url) {
    window.app.startApp(saeAppName, url);
  },
  exit : function() {
  },
  screen : function(option) {
	  if(option == "wakeup") {
		  window.app.wakeUpScreen();
	  } else if(option = "lock") {
		  window.app.lockScreen();
	  }
  },
  keyguard : function(option) {
	  if(option == "disable") {
		  window.app.disableKeyguard();
	  } else if(option = "enable") {
		  window.app.enableKeyguard();
	  }
  }
};
