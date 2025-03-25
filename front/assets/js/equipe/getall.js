function searchEquipe(){
    var localtoken = localStorage.getItem("token");
    var token = atob(localtoken);

    var text = document.getElementById('search').value;
    var search = text.toLowerCase();

    var equipes = [];

    $.ajax({
        url : "https://apilocal.pontuacao.com.br:4443/api/v1/equipe/get-all",
        type : 'GET',
        crossDomain: true,
        
        dataType: "json",

        headers: {
            "token": token,
        },
        success: function (retorno) {
            if(retorno.status == 200){
                $(retorno.equipes).each(function(chave, valor){
                    equipes.push(valor.name.toLowerCase(), valor.id);
                });
                var pesquisa = equipes.indexOf(search); 

                if(pesquisa != "-1"){
                    var num = pesquisa + 1;
                    var id = btoa(equipes[num]);

                    localStorage.setItem("equipe", id);
                    window.location.href = "/admin/equipe.html"; 
                }else{
                    alert("Não encontrado");
                }
            }else{
                alert("Algo de errado aconteceu, tente novamente.");
            }
            
        },
        error: function (xhr, ajaxOptions, thrownError) {
            alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
        }
    })

    
}

function getAllEquipes(totais = false){
    var token = atob(localStorage.getItem("token"));
    $('#spinner_loading').slideDown();

    $.ajax({
        url : "https://apilocal.pontuacao.com.br:4443/api/v1/equipe/get-all?totais="+totais,
        type : 'GET',
        crossDomain: true,

        dataType: "json",

        headers: {
            "token": token,
        },
        success: function (retorno) {
            $('#spinner_loading').slideUp();
            $('#equipes').html("");

            if(retorno.status === 200){

                contador = 1;
                $(retorno.equipes).each(function(chave, valor){
                    var points = "<a target='_blank' href='/common/equipe/pontos.html?equipe_id="+valor.id+"'><i class='uil uil-ticket'></i></a>";
                    if(totais === false){
                        var participante = "<tr>\n" +
                        "                <th scope=\"row\">"+contador+"</th>\n" +
                        "                <td>"+valor.name+"</td>\n" +
                        "                <td>"+valor.num_participantes+"</td>\n" +
                        "                <td>"+valor.ramo+"</td>\n" +
                        "                <td>"+points+"</td>\n" +
                        "            </tr>"
                    }else{
                        var participante = "<tr>\n" +
                            "                <th scope=\"row\">"+contador+"</th>\n" +
                            "                <td>"+valor.name+"</td>\n" +
                            "                <td>"+valor.num_participantes+"</td>\n" +
                            "                <td>"+valor.ramo+"</td>\n" +
                            "                <td>"+valor.pontos_totais+"</td>\n" +
                            "                <td>"+points+"</td>\n" +
                            "            </tr>"
                    }
                    $(participante).appendTo("#equipes");
                    contador ++;
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
}