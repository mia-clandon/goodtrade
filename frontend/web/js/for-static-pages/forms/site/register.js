$(function () {
    $("#fast-register-form").on("form.saved", function () {
        document.location.href = "/join";
    });
});