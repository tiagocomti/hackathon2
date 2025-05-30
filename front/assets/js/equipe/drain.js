//deleta um grupo de usuário de uma equipe
function drainUser(){

    var localtoken = localStorage.getItem("token");
    var token = atob(localtoken);

    var users = [];
    $("input:checkbox[id=check-user]:checked").each(function(){
        users.push($(this).val());
    });

    var equipe = localStorage.getItem("equipe");
    var equipe_id = atob(equipe);

      $.ajax({
            url : "https://api.grandejogo.org/api/v1/equipe/drain",
            type : 'DELETE',
            crossDomain: true,
            
            dataType: "json",
            data: JSON.stringify({
                users: users,
                equipe_id: equipe_id,
            }),

            headers: {
                "accept": "application/json",
                "token": token,
            },
            success: function (retorno) {
                if(retorno.status == 200){
                    window.location.href = "/admin/equipe.html"; 
                }else{
                    alert("Algo de errado aconteceu, tente novamente.");
                }
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
            }
        })
    };