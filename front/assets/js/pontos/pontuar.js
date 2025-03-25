function pontuar(dados){
    var token = atob(localStorage.getItem("token"));
    $.ajax({
        url : "https://ec2-3-238-118-252.compute-1.amazonaws.com:65443/api/v1/pontos/pontuar",
        type : 'POST',
        crossDomain: true,

        dataType: "json",
        data: JSON.stringify(dados),

        headers: {
            "accept": "application/json",
            "token": token,
        },
        success: function (retorno) {
            if(retorno.status == 200){
                $('#modalPontuacao').modal('hide');
                getAllPontos();
            }
        },
        error: function (xhr, ajaxOptions, thrownError) {
            if(xhr.code === 401){
                alert("Hei Xoven, você não tem permissão pra acessar essa página em, ta logado?");
                window.location.href = "/login.html";
            }
            alert(xhr.responseJSON.message);
        }
    });
}