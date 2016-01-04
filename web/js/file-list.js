$(function () {
    $(".table tr:not(:first)").on("click", function () {
        $(".table tr").removeClass("bg-info");
        $(this).addClass("bg-info");
    });
});
