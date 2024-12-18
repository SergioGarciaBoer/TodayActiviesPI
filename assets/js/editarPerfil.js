let peticion = new Object();
let nombreEditar = document.getElementById('nombre-editarP');
let gmailEditar = document.getElementById('correo-editarP');

addEventListener('DOMContentLoaded', () => {
    perfil.style.display = 'block';

    peticion.accion = 'cargar';

    postData('./php/editarPerfil.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        if (data.status == 'ko'){
            window.location.href = 'index.html';
        }else{
            peticion.nombreAct = data.usuario.nick;
            peticion.gmailAct = data.usuario.gmail;
            nombreEditar.value = data.usuario.nick;
            gmailEditar.value = data.usuario.gmail;
            document.getElementById('nombreUsuarioPerfil').innerHTML = data.usuario.nick;
        }
    });
});


document.getElementById('boton-editarP').addEventListener('click', () =>{

    if(peticion.nombreAct != nombreEditar.value || peticion.gmailAct != gmailEditar.value){
        peticion.accion = 'editarPerfil'
        peticion.contrasena = base64Encode(document.getElementById('contraseña-editarP').value);
        peticion.nickNuevo = '';
        if(peticion.nombreAct != nombreEditar.value){
            peticion.nickNuevo = nombreEditar.value;
        }
        if(peticion.gmailAct != gmailEditar.value){
            peticion.gmailNuevo = gmailEditar.value;
        }
        postData('./php/editarPerfil.php', {
            data:peticion
        }).then((data) => {
            if (data.status == 'errorPas'){
                document.getElementById('msg-error-editarP').innerHTML = '<p class="error">Contraseña incorrecta</p>'
            }else{
                document.getElementById('msg-error-editarP').innerHTML = '<p class="bien">Datos guardados correctamente</p>'
            }
        });
    }else{
        document.getElementById('msg-error-editarP').innerHTML = '<p class="alerta">No has cambiado ningún de los datos</p>';
    }
})

document.getElementById('boton-actualizar').addEventListener('click', () =>{
    let passNueva = document.getElementById('nuevaPass').value;
    let passConf = document.getElementById('passConfi').value;

    if(passNueva == passConf && passNueva!= ''){
        peticion.accion = 'actualizarContrasena';
        peticion.contrasenaAnt = btoa(document.getElementById('passVieja').value);
        peticion.contrasenaNuev = btoa(passNueva);
        postData('./php/editarPerfil.php', {
            data:peticion
        }).then((data) => {
            //console.log(data);
            if(data.status == 'datosInc'){
                document.getElementById('msg-error-actualizar').innerHTML = '<p class="alerta">Los datos son incorrectos</p>';
            }else{
                document.getElementById('msg-error-actualizar').innerHTML = '<p class="bien">Se ha cambiado la contraseña correctamente.</p>';
                document.getElementById('nuevaPass').value = '';
                document.getElementById('passConfi').value = '';
                document.getElementById('passVieja').value = '';
            }
        });
    }else{
        document.getElementById('msg-error-actualizar').innerHTML = '<p class="alerta">La contraseña no coincide</p>';
    }
});


function base64Encode(password){
    return btoa(password);
}
