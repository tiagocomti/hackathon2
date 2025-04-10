function getAllPontos(){
    var token = atob(localStorage.getItem("token"));
    $('#spinner_loading').slideDown();
    var equipe_id = getUrlParameter("equipe_id");

    $.ajax({
        url : "https://api.grandejogo.org/api/v1/pontos/get?equipe_id="+equipe_id,
        type : 'GET',
        crossDomain: true,

        dataType: "json",

        headers: {
            "token": token,
        },
        success: function (retorno) {
            $('#spinner_loading').slideUp();
            $('#table_pontos').html("");
            $('.equipe-total').html("");
            if(retorno.status === 200){
                $('.equipe-total').html(retorno.total);
                if(retorno.has_base == false){
                    $("#select_type").val(3);
                    changeContainers("penalidade");
                }else{
                    $("#select_type").val(1);
                    changeContainers("base");
                }
                if(retorno.role == "part"){
                    $("#header_part").show();
                }else{
                    $("#header_admin").show();
                }
                contador = 1;
                $('.equipe-name').html(retorno.equipe);
                $('.base-name').html(retorno.minha_base);
                $(retorno.pontos).each(function(chave, valor){
                    var participante = "<tr>\n" +
                        "                <th scope=\"row\">"+valor.id+"</th>\n" +
                        "                <td>"+valor.base+"</td>\n" +
                        "                <td>"+valor.avaliador+"</td>\n" +
                        "                <td>"+valor.tipo+"</td>\n" +
                        "                <td>"+valor.chegada+"</td>\n" +
                        "                <td>"+valor.pontos+"</td>\n" +
                        "                <td>"+valor.pontos_dicas+"</td>\n" +
                        "                <td>"+valor.observacao+"</td>\n" +
                        "                <td>"+valor.id+"</td>\n" +
                        "            </tr>"
                    $(participante).appendTo("#table_pontos");
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
            if(xhr.status === 403){
                alert("Tentando acessar pontos de outra equipe ne? aqui não raqui!!!!");  window.history.back();
                return false;
            }
            if(xhr.status === 400){
                alert(xhr.responseJSON.message);
                window.location.href = "/admin/ger-equipes.html";
                return false;
            }
            alert("Ocorreu um erro, tente novamente ou entre em contato com o administrador do sistema");
        }
    })
}