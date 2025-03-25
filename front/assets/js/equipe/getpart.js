//lista de todos os participantes
var localtoken = localStorage.getItem("token");
var token = atob(localtoken);

var equipe = localStorage.getItem("equipe");
var id = atob(equipe);

      $.ajax({
            url : "https://apilocal.pontuacao.com.br:4443/api/v1/equipe/get-participantes?equipe_id=" + id,
            type : 'GET',
            crossDomain: true,

            headers: {
                "token": token,
            },
            success: function (retorno) {
                if(retorno.status == 200){
                    $(retorno.participantes).each(function(chave, valor){
                        $("<li class='d-flex justify-content-between align-items-center w-100 my-2'><div>"+ valor.name +"</div><input id='check-user' value='" + valor.id + "' type='checkbox'></li>").appendTo("#list-user");
                    });
                }else{
                    alert("Algo de errado aconteceu, tente novamente.");
                }
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
            }
        })