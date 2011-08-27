/**
 * Confirm plugin 1.3
 *
 * Copyright (c) 2007 Nadia Alramli (http://nadiana.com/)
 * Dual licensed under the MIT (MIT-LICENSE.txt)
 * and GPL (GPL-LICENSE.txt) licenses.
 */

/**
 * For more docs and examples visit:
 * http://nadiana.com/jquery-confirm-plugin
 * For comments, suggestions or bug reporting,
 * email me at: http://nadiana.com/contact/
 */

jQuery.fn.confirm = function(options) {
  options = jQuery.extend({
    msg: 'Are you sure?',
    stopAfter: 'never',
    wrapper: '<span class="confirm_dropdown"></span>',
    eventType: 'click',
    dialogShow: 'show',
    dialogSpeed: '',
    normal: true,
    updatefn: '',
    timeout: 0,
  }, options);
  options.stopAfter = options.stopAfter.toLowerCase();
  if (!options.stopAfter in ['never', 'once', 'ok', 'cancel']) {
    options.stopAfter = 'never';
  }
  options.buttons = jQuery.extend({
    ok: 'Yes',
    cancel: 'No',
    wrapper:'<a href="#" class="links"></a>',
    separator: '/'
  }, options.buttons);

  // Shortcut to eventType.
  var type = options.eventType;

  return this.each(function() {
    var target = this;
    var $target = jQuery(target);
    var timer;
    var saveHandlers = function() {
      var events = jQuery.data(target, 'events');
      if (!events && target.href) {
        // No handlers but we have href
        $target.bind('click', function() {document.location = target.href});
        events = jQuery.data(target, 'events');
      } else if (!events) {
        // There are no handlers to save.
        return;
      }
      target._handlers = new Array();
      for (var i in events[type]) {
        target._handlers.push(events[type][i]);
      }
    }
    
    // Create ok button, and bind in to a click handler.
    var $ok = jQuery(options.buttons.wrapper)
      .append(options.buttons.ok)
      .click(function() {
      // Check if timeout is set.
      if (options.timeout != 0) {
        clearTimeout(timer);
      }
      $target.unbind(type, handler);
      $target.show();
      $dialog.hide();
      // Rebind the saved handlers.
      if (target._handlers != undefined) {
        jQuery.each(target._handlers, function() {
          $target.click(this.handler);
        });
      }
      // Trigger click event.
      $target.click();
      if (options.stopAfter != 'ok' && options.stopAfter != 'once') {
        $target.unbind(type);
        // Rebind the confirmation handler.
        $target.one(type, handler);
      }
      return false;
    })

    var $cancel = jQuery(options.buttons.wrapper).append(options.buttons.cancel).click(function() {
      // Check if timeout is set.
      if (options.timeout != 0) {
        clearTimeout(timer);
      }
      if (options.stopAfter != 'cancel' && options.stopAfter != 'once') {
        $target.one(type, handler);
      }
      $dialog.hide();
      //$target['slideDown']('fast');      
      $target.show();
      return false;
    });

    if (options.buttons.cls) {
      $ok.addClass(options.buttons.cls);
      $cancel.addClass(options.buttons.cls);
    }

	if(options.normal) {
		var $dialog = jQuery(options.wrapper)
		.append(options.msg)
		.append($ok)
		.append(options.buttons.separator)
		.append($cancel);
	}else {
		var $dialog = jQuery(options.wrapper)
		.append(options.msg)
		.append($cancel);
	}

    var handler = function() {
      jQuery(this).hide();

      // Do this check because of a jQuery bug
      if (options.dialogShow != 'show') {
        $dialog.hide();
      }

      $dialog.insertBefore(this);
      // Display the dialog.
      $dialog[options.dialogShow](options.dialogSpeed);
	  jQuery(this).append(options.updatefn);
      if (options.timeout != 0) {
        // Set timeout
        clearTimeout(timer);
        timer = setTimeout(function() {$cancel.click(); $target.one(type, handler);}, options.timeout);
      }
      return false;
    };
    
	saveHandlers();
	$target.unbind(type);
	target._confirm = handler
	target._confirmEvent = type;
	$target.one(type, handler);
  });
}
