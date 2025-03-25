//login

$(document).keypress(function(e) {
    if(e.which == 13){login();}
});

function get_doc(){
    $("#submit").html('<div class="spinner-border" role="status">\n' +
        '                        <span class="sr-only">Loading...</span>\n' +
        '                    </div>');
    $('#alertas').html("");
    var username = document.getElementById("user").value;
    var password = document.getElementById("pass").value;

    $.ajax({
        //IP de controle : 18.222.67.210 (passar sem waf para testes internos)
        url : "https://ec2-3-238-118-252.compute-1.amazonaws.com:65443/site/docs#/",
        type : 'POST',
        crossDomain: true,

        dataType: "json",
        data: JSON.stringify({"username": username, "password": password}),

        headers: {
            "accept": "application/json",
        },

        success: function (retorno) {

        },
        error: function (xhr, ajaxOptions, thrownError) {
            $("#submit").html('Entrar');
            if(xhr.status == 401){
                $('#alertas').html("<div class='alert alert-danger alert-dismissible fade show'>Usuário ou senha incorretos.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>");
                timeOut();
            }else{
                $('#alertas').html("<div class='alert alert-danger alert-dismissible fade show'>Ocorreu um erro, entre em contato com o administrador.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>");
                timeOut();
            }
        }
    })
};

function timeOut(){
    setTimeout(function(){
        $(".alert").fadeOut("slow", function(){
            $(this).alert("close");
        })

    }, 6000);
}