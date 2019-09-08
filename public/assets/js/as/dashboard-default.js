as.dashboard = {};

as.dashboard.initChart = function () {
    var data = {
        labels: labels,
        datasets: [
            {
                label: "Activity (last 30 days)",
                fillColor: "rgba(151,187,205,0.2)",
                strokeColor: "rgba(151,187,205,1)",
                pointColor: "rgba(151,187,205,1)",
                pointStrokeColor: "#fff",
                pointHighlightFill: "#fff",
                pointHighlightStroke: "rgba(151,187,205,1)",
                data: activities
            }
        ]
    };

    var ctx = document.getElementById("myChart").getContext("2d");
    var myLineChart = new Chart(ctx).Line(data, {
        responsive: true,
        maintainAspectRatio: false,
        tooltipTemplate: "<%if (label){%><%=label%>: <%}%><%= value %> <%= value == 1 ? 'action' : 'actions' %>",
    });
};

$(document).ready(function () {
    as.dashboard.initChart();
});