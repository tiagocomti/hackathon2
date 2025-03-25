//pega o nome, pontos e participantes da equipe
var localtoken = localStorage.getItem("token");
var token = atob(localtoken);

var equipe = localStorage.getItem("equipe");
var id = atob(equipe);

$.ajax({
    url : "https://ec2-3-238-118-252.compute-1.amazonaws.com:65443/api/v1/equipe/get?id=" + id,
    type : 'GET',
    crossDomain: true,
    
    dataType: "json",

    headers: {
        "token": token,
    },
    success: function (retorno) {
        $('#spinner_loading').slideUp();
        if(retorno.status == 200){
            var nome = retorno.equipes.name;
            var pontos = retorno.equipes.pontos;

            $('#name-equipe').html("Equipe " + nome); 
            $('#pontos-equipe').html(pontos); 
            contador = 1;
            $(retorno.equipes.participantes).each(function(chave, valor){
                var phone = ""
                if(valor.phone != ""){
                    phone = "<a target='_blank' href='https://api.whatsapp.com/send?phone=55"+valor.phone+"'><i class='uil uil-whatsapp'></i></a>";
                }
                var participante = "<tr>\n" +
                    "                <th scope=\"row\">"+contador+"</th>\n" +
                    "                <td>"+valor.name+"</td>\n" +
                    "                <td>"+valor.observacoes+"</td>\n" +
                    "                <td>"+phone+"</td>\n" +
                    "            </tr>"
                $(participante).appendTo("#participantes");
                contador ++;
            });
        }else{
            alert("Algo de errado aconteceu, tente novamente.");
        }
        
    },
    error: function (xhr, ajaxOptions, thrownError) {
        console.log(xhr);
        if(xhr.code === 401){
            alert("Hei Xoven, você não tem permissão pra acessar essa página em, ta logado?");
            window.location.href = "/login.html";
        }
        alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
    }
})