
let divPublicaciones = document.getElementById('publicacionBuscadas');
let divPagina = document.getElementById('divPaginacion');
let body = document.getElementsByTagName('body')[0];

addEventListener('DOMContentLoaded', () =>{
    peticion.accion = 'cargarPublicaciones';
    postData('./php/cargarPublicacionesInicio.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        const publicaciones = data.publicaciones;
        publicaciones.forEach(publicacion => {
            let div = document.createElement('div');
            div.innerHTML = publicacionesHTMLVer(publicacion.imagen.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia.nombre);
            divPublicaciones.append(div);
        });
        paginasBotones = data.paginas;
        for(let i = 1; i <= paginasBotones; i++){
            divPagina.innerHTML += botonesPaginacion(i);
        }
        if(data.usuario.status == 'ok'){
            btnSesionPrinc.style.display = 'none';
            perfil.style.display = 'block';
            modal.style.display = 'none';
            document.getElementById('nombreUsuarioPerfil').innerHTML = data.usuario.usuario.nick;
        }
    });
});

function numeroPagina(num){

    peticion.accion = 'paginacion';
    peticion.paginaDeseada = num;

    postData('./php/cargarPublicacionesInicio.php', {
        data:peticion
    }).then((data) => {
        divPublicaciones.innerHTML = '';
        const publicaciones = data.publicaciones;
        publicaciones.forEach(publicacion => {
            let div = document.createElement('div');
            div.innerHTML = publicacionesHTMLVer(publicacion.imagen.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia.nombre);
            divPublicaciones.append(div);
        });
        paginasBotones = data.paginas;
        for(let i = 1; i <= paginasBotones; i++){
            divPagina.innerHTML += botonesPaginacion(i);
        }
    });
}

function publicacionCompleta(idPubli){
    peticion.accion = 'mostrarDetalles';
    peticion.idPublicacion= idPubli;
    postData('./php/cargarPublicacionesInicio.php', {
        data:peticion
    }).then((data) => {
        const modalDiv = document.createElement('div');
        modalDiv.id = 'modalPubli';
        modalDiv.classList.add('modalPubli');
        let valoracion;
        if(data.media == null){
            valoracion = 'No hay valoraciones';
        }else{
            valoracion = data.media;
        }
        modalDiv.innerHTML = modalPublicacionHTML(data.titulo, data.imagenes, data.descripcion, data.id, valoracion);

        document.body.appendChild(modalDiv);

        modalDiv.style.display = 'block';

        modalDiv.querySelector('.close').addEventListener('click', () => {
            cerrarModalPubli(modalDiv);
        });

        modalDiv.addEventListener('click', (e) => {
            if (e.target === modalDiv) {
                cerrarModalPubli(modalDiv);
            }
        });

    });
}


function modalPublicacionHTML(tituloPubli, imagenesPubli, descripcionPubli, id, media) {
    let html = `
        <div class="contenido-modalPubli">
            <span class="close" onclick="cerrarModalPubli()">&times;</span>
            <h2 class="text-center">${tituloPubli}</h2>
            
            <!-- Galería de imágenes -->
            <div class="galeria-contenedor">
                `
                imagenesPubli.forEach((imagen) => {
                    html += `<img src="data:image/png;base64,${imagen.imagen}" class="imgPubli">`;
                });
                html += `
            </div>
            <div class="textoPubli">
                <p>Localización: Málaga</p>
                <p>Descripción: ${descripcionPubli}</p>
            </div>
            <div class="rating-container">
                <span class="rating-btn" onclick="valorarPublicacion(1, ${id})" data-value="1">1</span>
                <span class="rating-btn" onclick="valorarPublicacion(2, ${id})" data-value="2">2</span>
                <span class="rating-btn" onclick="valorarPublicacion(3, ${id})" data-value="3">3</span>
                <span class="rating-btn" onclick="valorarPublicacion(4, ${id})" data-value="4">4</span>
                <span class="rating-btn" onclick="valorarPublicacion(5, ${id})" data-value="5">5</span>
            </div>
            <div class="contenedor-media"><p>${media}</p></div>
            <div class="seccion-comentarios">
                <button class="boton-comentarios" id="boton-comentarios" onclick="botonComentariosAccion(${id})">Comentarios</button>

                <div class="formulario-comentario" id="formulario-comentario">
                    <textarea id="texto-comentario" placeholder="Escribe tu comentario aquí..."></textarea>
                    <button id="boton-enviar-comentario" onclick="enviarComentario(${id})">Enviar</button>
                </div>

                <div class="lista-comentarios" id="lista-comentarios">
                </div>
            </div>
            <div class="error" id="msg-fallos-publi"></div>
        </div>
    `;

    return html;
}


function cerrarModalPubli(){
    const modal = document.getElementById('modalPubli');
    if (modal) {
        modal.remove(); 
    }
}

function publicacionesHTMLVer(imagenPubli, tituloPubli, descripcionPubli, idPubli, provincia){
    return `
        <div class="card tarjetaPubli">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="data:image/png;base64,${imagenPubli}" class="img-fluid rounded-start" alt="...">
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <h5 onclick="publicacionCompleta(${idPubli})" class="card-title">${tituloPubli}</h5>
                        <p class="card-text">
                            ${descripcionPubli}
                        </p>
                        <p>Localizacion: ${provincia}</p>
                    </div>
                </div>
            </div>
        </div>
    `
}

function botonesPaginacion(paginaC){
    return `
    <button class="btn-paginacion" onclick="numeroPagina(${paginaC})">${paginaC}</button>
    `
}



  function botonComentariosAccion(idPubli){
    peticion.accion = 'verComentarios';
    peticion.idPubli = idPubli;
    postData('./php/cargarPublicacionesInicio.php', {
        data:peticion
    }).then((data) => {
        if(data.status == 'ko'){
            document.getElementById('msg-fallos-publi').innerHTML = 'Tienes que iniciar sesion para ver o hacer comentarios';
            formularioComentario.style.display = 'none';
        }else{
            const formularioComentario = document.getElementById('formulario-comentario');
            const listaComentarios = document.getElementById('lista-comentarios');
            const textoComentario = document.getElementById('texto-comentario');

            const esVisible = formularioComentario.style.display === 'block';
            formularioComentario.style.display = esVisible ? 'none' : 'block';
            listaComentarios.style.display = esVisible ? 'none' : 'block';
            listaComentarios.innerHTML = '';
            let comentarios = data.comentarios;
            comentarios.forEach(comentario => {
              const comentarioDiv = document.createElement('div');
              comentarioDiv.classList.add('comentario');
    
              comentarioDiv.innerHTML = `
                <div class="autor">${comentario.nick}</div>
                <div class="fecha">${comentario.fecha_comentario}</div>
                <div class="texto">${comentario.comentario}</div>
              `;
    
              listaComentarios.prepend(comentarioDiv);
              textoComentario.value = '';
            });  
        }
      });
  }

  function enviarComentario(idPubli){
    const listaComentarios = document.getElementById('lista-comentarios');
    const textoComentario = document.getElementById('texto-comentario');
    const texto = textoComentario.value.trim();

    if (texto === '') {
      alert('El comentario no puede estar vacío');
      return;
    }

    peticion.accion = 'comentario';
    peticion.idPubli = idPubli;
    peticion.comentario = texto;
    postData('./php/cargarPublicacionesInicio.php', {
          data:peticion
      }).then((data) => {
        const comentarioDiv = document.createElement('div');
        comentarioDiv.classList.add('comentario');

        comentarioDiv.innerHTML = `
          <div class="autor">${data.nick}</div>
          <div class="fecha">${data.fecha_comentario}</div>
          <div class="texto">${data.comentario}</div>
        `;

        listaComentarios.prepend(comentarioDiv);
        textoComentario.value = '';
      });

  }

  function valorarPublicacion(valoracion, idPubli){
    peticion.accion = 'valoracion';

      document.querySelectorAll('.rating-btn').forEach(btn => btn.classList.remove('selected'));
      const value = valoracion
      peticion.valoracion = value;
      const clickedButton = event.target;
      clickedButton.classList.add('selected');
      peticion.idPublicacion = idPubli;
      postData('./php/cargarPublicacionesInicio.php', {
          data:peticion
      }).then((data) => {
          if(data.status == 'noSesion'){
            document.getElementById('msg-fallos-publi').innerHTML = 'Tienes que iniciar sesion para hacer valoraciones';
          }
      });
  }