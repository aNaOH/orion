let url = document.URL.endsWith('/') ? document.URL : document.URL + "/";
let urlSplitted = url.split('/');
let gameID = urlSplitted[urlSplitted.length-3];