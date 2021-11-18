/**
 * Расписание задач, JS-сценарий
 *
 * @package    DIAFAN.CMS
 * @author     diafan.ru
 * @version    7.0
 * @license    http://www.diafan.ru/license.html
 * @copyright  Copyright (c) 2003-2021 OOO «Диафан» (http://www.diafan.ru/)
 */

$(function() {
  show_module_item($("#module_name select"));
  $("#module_name select").change(function() {
    show_module_item($(this));
  });
  function show_module_item(obj) {
    var options = $("#method select option");
    options.hide();
    if (! obj.length) return;
    var show = options.filter("[module='"+obj.val()+"']").show();
    if ($("#method select option:selected").attr("module") != obj.val()) {
      var current = show.filter("[current='true']").eq(0);
      if(current.length) current.prop('selected', true);
      else show.eq(0).prop('selected', true);
    }
  }
  $("#method select").change(function() {
    $("#method select option").filter("[module='"+$("#method select option:selected").attr("module")+"']").removeAttr("current");
    $("#method select option:selected").attr("current", "true");
  });

  $(document).on('click', '.head-box .action', function () {
    var self = $(this);
    if (! self.attr("action") || ! self.attr("module")) {
      return true;
    }
    if (self.is('.disable')) {
      return true;
    }
    if (self.attr("confirm") && ! confirm(self.attr("confirm"))) {
      return false;
    }
    self.addClass('disable');
    self.children('.spinner').show();
    if (self.attr('switch') == 'on') {
      self.attr('switch', 'off');
    } else if(self.attr('switch') == 'off') {
      self.attr('switch', 'on');
    }

    diafan_ajax.init({
      data:{
        action: self.attr('action'),
        module: self.attr("module")
      },
      success: function(response) {
        if (response.action) {
          self.attr('action', prepare(response.action));
        }
        if (response.switch) {
          self.attr('switch', prepare(response.switch));
        }
        if (response.title) {
          self.attr('title', prepare(response.title));
        }
        self.children('.spinner').hide();
        if (self.is('.disable')) {
          self.removeClass('disable');
        } else {
          self.addClass('disable');
        }
      }
    });
    return false;
  });
});
