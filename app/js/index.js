var x = document.getElementById("btn-x");
var alert = document.getElementById("div-alert");

x.addEventListener('click', ()=>{
alert.parentNode.removeChild(alert)
})



function formatarCpf(event) {
    let tecla = event.keyCode;
    let valor = event.target.value;
  
    if (tecla === 8 || tecla === 13) {
      return true;
    }
  
    let caractere = String.fromCharCode(tecla);
    let numeros = "0123456789";
  
    if (numeros.indexOf(caractere) === -1) {
      return false;
    }
  
    let novoValor = "";
    for (let i = 0; i < valor.length; i++) {
      if (numeros.indexOf(valor.charAt(i)) !== -1) {
        novoValor += valor.charAt(i);
      }
    }
  
    novoValor += caractere;
    if (novoValor.length > 11) {
      return false;
    }
  
    let cpf = "";
    cpf += novoValor.substr(0, 3) + ".";
    cpf += novoValor.substr(3, 3) + ".";
    cpf += novoValor.substr(6, 3) + "-";
    cpf += novoValor.substr(9, 2);
  
    event.target.value = cpf;
  
    return false;
}

function moeda(a, e, r, t) {
    let n = "",
        h = j = 0,
        u = tamanho2 = 0,
        l = ajd2 = "",
        o = window.Event ? t.which : t.keyCode;
    if (13 == o || 8 == o)
        return !0;
    if (n = String.fromCharCode(o), -1 == "0123456789".indexOf(n)) {
            

        return !1;
    }
    for (u = a.value.length,
        h = 0; h < u && ("0" == a.value.charAt(h) || a.value.charAt(h) == r); h++)
    ;
    for (l = ""; h < u; h++)
        -
        1 != "0123456789".indexOf(a.value.charAt(h)) && (l += a.value.charAt(h));
    if (l += n,
        0 == (u = l.length) && (a.value = ""),
        1 == u && (a.value = "0" + r + "0" + l),
        2 == u && (a.value = "0" + r + l),
        u > 2) {
        for (ajd2 = "",
            j = 0,
            h = u - 3; h >= 0; h--)
            3 == j && (ajd2 += e,
                j = 0),
            ajd2 += l.charAt(h),
            j++;
        for (a.value = "",
            tamanho2 = ajd2.length,
            h = tamanho2 - 1; h >= 0; h--)
            a.value += ajd2.charAt(h);
        a.value += r + l.substr(u - 2, u)
    }
    
    return !1
}
function celular(event){
    var a = event.target.value.replace(/\D/g, '').match(/(\d{0,2})(\d{0,5})(\d{0,4})/);
    event.target.value = !a[2] ? a[1] : '(' + a[1] + ') ' + a[2] + (a[3] ? '-' + a[3] : '');
}

function formatarNumero(event, casasDecimais) {
    let tecla = event.keyCode;
    let valor = event.target.value;
  
    if (tecla === 8 || tecla === 13) {
      return true;
    }
  
    let caractere = String.fromCharCode(tecla);
    let numeros = "0123456789";
    let separadorDecimal = ".";
  
    if (numeros.indexOf(caractere) === -1) {
      return false;
    }
  
    if (valor.indexOf(".") !== -1) {
      separadorDecimal = ".";
    } else if (valor.indexOf(",") !== -1) {
      separadorDecimal = ",";
    }
  
    let novoValor = "";
    for (let i = 0; i < valor.length; i++) {
      if (numeros.indexOf(valor.charAt(i)) !== -1) {
        novoValor += valor.charAt(i);
      }
    }
  
    if (novoValor.length === 0 && (caractere === "." || caractere === ",")) {
      novoValor = "0" + separadorDecimal;
    } else if (novoValor.length === 1 && novoValor.charAt(0) === separadorDecimal) {
      novoValor = "0" + novoValor;
    } else {
      novoValor += caractere;
    }
  
    if (novoValor.length > 16) {
      return false;
    }
  
    let numero = parseFloat(novoValor.replace(",", "."));
    if (isNaN(numero)) {
      return false;
    }
  
    let numeroFormatado = numero.toFixed(casasDecimais).replace(".", separadorDecimal);
    event.target.value = numeroFormatado;
  
    return false;
  }

/* function formatarNumero(event, casasDecimais) {
    let tecla = event.keyCode;
    let valor = event.target.value;
  
    if (tecla === 8 || tecla === 13) {
      return true;
    }
  
    let caractere = String.fromCharCode(tecla);
    let numeros = "0123456789";
  
    if (numeros.indexOf(caractere) === -1) {
      return false;
    }
  
    let novoValor = "";
    for (let i = 0; i < valor.length; i++) {
      if (numeros.indexOf(valor.charAt(i)) !== -1) {
        novoValor += valor.charAt(i);
      }
    }
  
    novoValor += caractere;
    if (novoValor.length > 16) {
      return false;
    }
  
    let numero = parseFloat(novoValor);
    if (isNaN(numero)) {
      return false;
    }
  
    let numeroFormatado = numero.toFixed(casasDecimais).replace(",", ".");
    event.target.value = numeroFormatado;
  
    return false;
  }
 */