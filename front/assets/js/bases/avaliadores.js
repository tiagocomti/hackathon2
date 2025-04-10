//retorna todos os avaliadores
    var localtoken = localStorage.getItem("token");
    var token = atob(localtoken);

    var tarefa_id = localStorage.getItem("tarefa");
    var id = atob(tarefa_id);

    var tarefas = [];

    $.ajax({
        url : "https://api.grandejogo.org/api/v1/bases/get-avaliadores?base_id=" + id,
        type : 'GET',
        crossDomain: true,
        
        dataType: "json",

        headers: {
            "token": token,
        },
        success: function (retorno) {
            if(retorno.status == 200){
                $(retorno.avaliadores).each(function(chave, valor){
                    tarefas.push(valor.name.toLowerCase(), valor.id);
                    $("<li class='d-flex justify-content-between align-items-center w-100 my-2'><div>"+ valor.name +"</div><input id='check-user' value='" + valor.id + "' type='checkbox'></li>").appendTo("#list-user");
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

    //pesquisa usuarios
    function searchUsers(){

        var text = document.getElementById('search').value;
        var search = text.toLowerCase();

        var pesquisa = tarefas.indexOf(search); 

        if(pesquisa != "-1"){
            //exibir resultado da pesquisa
            var num = pesquisa + 1;
            $('#list-user').html("<li class='d-flex justify-content-between align-items-center w-100 my-2'><div>"+ usuarios[pesquisa] +"</div><input id='check-user' value='" + usuarios[num] + "' type='checkbox'></li>"); 
        }else{
            $('#alertas').html("<div class='alert alert-danger alert-dismissible fade show'>Nenhum usuário foi encontrado.<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>"); 
            timeOut();
        }
    }

    function timeOut(){
        setTimeout(function(){
            $(".alert").fadeOut("slow", function(){
                $(this).alert("close");
            })
            
        }, 6000);
    }

