const qrScanner = new QrScanner(videoElem, result => console.log('decoded qr code:', result));
qrScanner.start();