as.btn = {};

as.btn.loading = function(button, text) {
    var oldText = button.text();
    var html = '<i class="fa fa-spinner fa-spin"></i> ' + text;
    button.data("old-text", oldText)
        .html(html)
        .addClass("disabled")
        .attr('disabled', "disabled");
};

as.btn.stopLoading = function (button) {
    var oldText = button.data('old-text');
    button.text(oldText)
        .removeClass("disabled")
        .removeAttr("disabled");
};