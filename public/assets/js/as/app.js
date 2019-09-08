var as = {};

as.toggleSidebar = function () {

    if (window.innerWidth > 768) {
        $("body").toggleClass('sidebar-collapse');
    } else {
        $("body").toggleClass('sidebar-open');
    }

    return false;
};

as.hideNotifications = function () {
    $(".alert-notification").slideUp(600, function () {
        $(this).remove();
    })
};

as.init = function () {

    var sideMenu = $("#side-menu");

    if (sideMenu.length) {
        sideMenu.metisMenu({
            toggle: false,
            activeClass: 'active'
        });
    }

    $("#sidebar-toggle").click(as.toggleSidebar);

    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="popover"]').popover();

    $(".alert-notification .close").click(as.hideNotifications);

    setTimeout(as.hideNotifications, 2500);

    $("a[data-toggle=loader], button[data-toggle=loader]").click(function () {
        if ($(this).parents('form').valid()) {
            as.btn.loading($(this), $(this).data('loading-text'));
            $(this).parents('form').submit();
        }
    });
};

$(document).ready(as.init);