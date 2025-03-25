//Retorna Array de todos os usuarios
var localtoken = localStorage.getItem("token");
var token = atob(localtoken);

var usuarios = [];

      $.ajax({
            url : "https://ec2-3-238-118-252.compute-1.amazonaws.com:65443/api/v1/user/get-all?type=avali",
            type : 'GET',
            crossDomain: true,

            headers: {
                "token": token,
            },
            success: function (retorno) {
                if(retorno.status == 200){
                    $(retorno.users).each(function(chave, valor){
                        usuarios.push(valor.name.toLowerCase(), valor.id);
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

        //pesquisa usuarios
        function searchUsers(){

            var text = document.getElementById('search').value;
            var search = text.toLowerCase();

            var pesquisa = usuarios.indexOf(search); 

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