/*
document.addEventListener("DOMContentLoaded", function () {
    function test(id) {
        alert("Choisissez maintenant l'adversaire")

    }
    
    document.getElementsByClassName("perso").addEventListener("click", event => {
        document.getElementsByClassName("monstre").addEventListener("click", event => {
            alert("ok");
        })
    })
    
}); */




function test(id) {
    var p = document.getElementById("perso")
    var btn = document.getElementById("perso"+id)
    alert("Choisissez maintenant l'adversaire")
    p.value = id;
    btn.style.backgroundColor = "green";
}

function testm(id) {

    var btnM = document.getElementById("monstre"+id)
    if (document.getElementById("perso").value == "") {
        alert ("Commencer par choisir un personnage")
    }
    else {
        //let url= $('.monstre').data('url')+ document.getElementById("perso").value +"VS"+document.getElementById("monstre").value;
        let urll = document.getElementById('urll').value;
        document.getElementById("monstre").value = id;   
       // alert(document.getElementById("perso").value +"VS" +document.getElementById("monstre").value)
       // document.getElementById("perso").value = "";
        btnM.style.backgroundColor = "green";
        console.log(urll)
       // console.log(document.getElementById("monstre").value)
        console.log(urll+document.getElementById("perso").value+"VS"+document.getElementById("monstre").value)
        let urlOK = urll+document.getElementById("perso").value+"VS"+document.getElementById("monstre").value
       // document.location.href='https://webetu.iutnc.univ-lorraine.fr/www/helf6u/Super_Street_Dora_Grand_Championship_Turbo/public/index.php/combat'+ document.getElementById("perso").value +"VS"+document.getElementById("monstre").value
       document.location.href=urlOK

    }



}


/*
document.getElementById("perso").addEventListener("click", event => {
   alert("ok");
})
*/