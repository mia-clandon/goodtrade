$(function () {
    $("#fast-register-form").on("form.saved", function (event, data) {
        document.location.href = "/join";
    });
});