//pega o qrcode da equipe
var localtoken = localStorage.getItem("token");
var token = atob(localtoken);

var equipe = localStorage.getItem("equipe");
var id = atob(equipe);

$.ajax({
    url : "https://apilocal.pontuacao.com.br:4443/api/v1/equipe/get-qrcode?equipe_id=" + id,
    type : 'GET',
    crossDomain: true,
    
    dataType: "json",

    headers: {
        "token": token,
    },
    success: function (retorno) {
        if(retorno.status == 200){
            let qrcode = retorno.base;
            $('#qrcode').html("<img src='" + qrcode + "' alt='qr code'>"); 
        }else{
            alert("Algo de errado aconteceu, tente novamente.");
        }
        
    },
    error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr);
        if(xhr.status === 401){
            alert("Hei Xoven, você não tem permissão pra acessar essa página em, ta logado?");
            window.location.href = "/login.html";
            return false;
        }
        alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
    }
})