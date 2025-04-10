//cria um novo usuário
function createUser(){

    let localtoken = localStorage.getItem("token");
    let token = atob(localtoken);

    var email = document.getElementById("email").value;
    var name = document.getElementById("nome").value;
    var username = document.getElementById("username").value;
    var phone = document.getElementById("phone").value;
    var type = "";
    var password_hash = document.getElementById("password").value;

    if (document.getElementById("part").checked) {
        type = "part";
    } else if (document.getElementById("avali").checked) {
        type = "avali";
    }

      $.ajax({
            url : "https://api.grandejogo.org/api/v1/user/create",
            type : 'POST',
            crossDomain: true,
            
            dataType: "json",
            data: JSON.stringify({
                email: email,
                name: name,
                username: username,
                phone: phone,
                type: type,
                password_hash: password_hash,
            }),

            headers: {
                "accept": "application/json",
                "token": token,
            },
            success: function (retorno) {
                if(retorno.status == 200){
                    window.location.href = "/admin/settings.html"; 
                }else{
                    alert("Algo de errado aconteceu, tente novamente.");
                }
                
            },
            error: function (xhr, ajaxOptions, thrownError) {
                alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
            }
        })
    };