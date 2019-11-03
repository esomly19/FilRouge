


let ind =1;
/*
let idp =document.getElementById("perso").value;
let idm= document.getElementById("monstre").value;
*/
//personnage
let attaque = document.getElementById("attaque").textContent;
let agilite = document.getElementById("agilite").textContent;
let defense = document.getElementById("defense").textContent;
let vie=document.getElementById("vie").textContent;
let poids=document.getElementById("poids").textContent;
let taille=document.getElementById("taille").textContent;
let nom=document.getElementById("nom").textContent;
//monstre
let attaquem=document.getElementById("attaqueM").textContent;
let agilitem=document.getElementById("agiliteM").textContent;
let defensem=document.getElementById("defenseM").textContent;
let viem=document.getElementById("vieM").textContent;
let poidsm=document.getElementById("tailleM").textContent;
let taillem=document.getElementById("poidsM").textContent;
let nomm=document.getElementById("nomM").textContent;

let dmg;

function test(id) {
    var p = document.getElementById("perso")
    var btn = document.getElementById("perso"+id)
    alert("Choisissez maintenant l'adversaire")
    p.value = id;
    btn.style.backgroundColor = "lightgreen";
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


document.getElementById("tour").addEventListener("click", event => {

   tour();
})
