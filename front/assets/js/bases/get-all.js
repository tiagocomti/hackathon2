//Retorna Array de todos as tarefas
var localtoken = localStorage.getItem("token");
var token = atob(localtoken);

      $.ajax({
            url : "https://apilocal.pontuacao.com.br:4443/api/v1/bases/get-all",
            type : 'GET',
            crossDomain: true,

            headers: {
                "token": token,
            },
            success: function (retorno) {
                if(retorno.status == 200){
                    $(retorno.bases).each(function(chave, valor){
                        $("<option value='" + valor.id +"'>" + valor.name +"</option>").appendTo("#tarefas");
                    });
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