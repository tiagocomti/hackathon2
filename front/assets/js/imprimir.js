function imprimir(){
    var conteudo = document.getElementById('qrcode').innerHTML,
    tela_impressao = window.open('Imprimir QR CODE');

    tela_impressao.document.write(conteudo);
    tela_impressao.window.print();
    tela_impressao.window.close();
}