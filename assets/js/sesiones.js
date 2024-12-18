/* Registro o inicio de sesion */
let peticion = new Object();

iniciarSesionCB.addEventListener('change', () => {
    if(iniciarSesionCB.checked){
        divInicioSesion.style.display = 'block';
        divRegistro.style.display = 'none';
        registroCB.checked = false;
    }else{
        iniciarSesionCB.checked = true;
    }
});

registroCB.addEventListener('change', () => {
    if(registroCB.checked){
        divInicioSesion.style.display = 'none';
        divRegistro.style.display = 'block';
        iniciarSesionCB.checked = false;
    }else{
        registroCB.checked = true;
    }
});


btnSesion.addEventListener('click', ()=>{
    peticion.accion = 'sesion';
    peticion.nick = document.getElementById('sesionNick').value;
    peticion.contrasena = base64Encode(document.getElementById('sesionContrasena').value);
    postData('./php/sesiones.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        if(data.status == 'ko'){
            document.getElementById('sesionContrasena').value = '';
            document.getElementById('msg-error-sesion').innerHTML = '<p class="error">Datos incorrectos.</p>';
        }else{
            btnSesionPrinc.style.display = 'none';
            perfil.style.display = 'block';
            modal.style.display = 'none';
            document.getElementById('nombreUsuarioPerfil').innerHTML = data.nick;
        }
    });
});

btnRegistro.addEventListener('click', () => {
    peticion.accion = 'registro';
    peticion.nick = document.getElementById('registroNick').value;
    let nick = document.getElementById('registroNick').value;
    peticion.contrasena = base64Encode(document.getElementById('registroContrasena').value);
    peticion.gmail = document.getElementById('registroGmail').value;
    let gmail = document.getElementById('registroGmail').value;

    if(nick.length < 30 && gmail.length < 150){
        postData('./php/sesiones.php', {
            data:peticion
        }).then((data) => {
            //console.log(data);
            if(data.status == 'ok'){
                btnSesionPrinc.style.display = 'none';
                perfil.style.display = 'block';
                modal.style.display = 'none';
                limpiarFormularioSesion();
                document.getElementById('nombreUsuarioPerfil').innerHTML = data.nick;
            }else if (data.status == 'nick'){
                document.getElementById('msg-error-sesion').innerHTML = '<p>El nombre de usuario ya existe.</p>';
                limpiarFormularioSesion();
            }else{
                document.getElementById('msg-error-sesion').innerHTML= '<p>El gmail que quieres usar ya se encuentra registrado.</p>';
                limpiarFormularioSesion();
            }
        });
    }else{
        document.getElementById('msg-error-sesion').innerHTML= '<p>El gmail tiene que tener como maximo 149 caracteres y el usuario 29 como maximo</p>';
    }
    
});

document.getElementById('btnContraOlvido').addEventListener('click', ()=>{
    peticion.accion = 'recuperarContraseña';
    peticion.gmail = document.getElementById('gmailOlvido').value;
    postData('./php/sesiones.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        if (data.status == 'ok'){
            document.getElementById('msg-error-sesion').innerHTML = '<p class="bien">Se ha enviado un correo con la nueva contraseña</p>';
        }else{
            document.getElementById('msg-error-sesion').innerHTML = 'El gmail que ha puesto no es el correcto';
        }
    })

});

function base64Encode(password){
    return btoa(password);
}

function base64Decode(password){
    return atob(password);
}

function limpiarFormularioSesion(){
    document.getElementById('registroNick').value = '';
    document.getElementById('registroContrasena').value = '';
    document.getElementById('registroGmail').value= '';
}

