sesionCierre.addEventListener('click', () =>{
    peticion.accion = 'cerrar'
    postData('./php/sesiones.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        window.location.href = "index.html"
    })
});

perfil.addEventListener('click', () =>{
    if(window.innerWidth < 768){
        perfilNav.style.left = '40%';
    }else{
        perfilNav.style.left = 'calc(100% - 400px)';
        //console.log('hola');
    }
});

closePerfil.addEventListener('click', () =>{
    if(window.innerWidth < 768){
        perfilNav.style.left = '100%';
    }else{
        perfilNav.style.left = '100%';
    }
})