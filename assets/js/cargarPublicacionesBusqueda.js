const radios = document.querySelectorAll('.categorias-radios input[type="radio"]');
let categoria = 'todos';
const botonFiltros = document.getElementById('botonFiltros');
const buscador = document.getElementById('buscador');
const selectProvicias = document.getElementById('ciudad');
const navItems = document.querySelectorAll('.navCat li');


radios.forEach((radio) => {
    radio.addEventListener('change', (event) => 
        {
        navItems.forEach(item => item.classList.remove('active'));
        navItems.forEach(item => {
            if (item.querySelector('label').htmlFor === event.target.id) {
                item.classList.add('active');
            }
        });

        if(event.target.value != 'todos'){
            peticion.accion = 'publicacionPorCategoria';
            peticion.idCategoria = event.target.value
            postData('./php/cargarPublicacionesBusqueda.php', {
                data:peticion
            }).then((data) => {
                //console.log(data);
                divPublicaciones.innerHTML = '';
                const publicaciones = data.publicaciones;
                publicaciones.forEach(publicacion => {
                    let div = document.createElement('div');
                    div.innerHTML = publicacionesHTMLVer(publicacion.imagenes.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia);
                    divPublicaciones.append(div);
                });
                paginasBotones = data.paginas;
                divPagina.innerHTML ='';
                for(let i = 1; i <= paginasBotones; i++){
                    divPagina.innerHTML += botonesPaginacionCategoria(i);
                }
            });
        }else{
            peticion.accion = 'cargarPublicaciones';
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
                
                divPagina.innerHTML = '';
                paginasBotones = data.paginas;
                for(let i = 1; i <= paginasBotones; i++){
                    divPagina.innerHTML += botonesPaginacion(i);
                }
            });
        }
        
    });
});

botonFiltros.addEventListener('click', () =>{
    peticion.accion = 'buscarPorFiltros';
    peticion.buscar = buscador.value;
    peticion.idProvincia = selectProvicias.value;

    peticion.idCategoria = document.querySelector('.categorias-radios input[type="radio"]:checked')?.value || null;
    //console.log(peticion.idCategoria);

    postData('./php/cargarPublicacionesBusqueda.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        divPublicaciones.innerHTML = '';
        peticion.sql = data.sql;
        const publicaciones = data.publicaciones;
        publicaciones.forEach(publicacion => {
            let div = document.createElement('div');
            div.innerHTML = publicacionesHTMLVer(publicacion.imagenes.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia);
            divPublicaciones.append(div);
        });
        paginasBotones = data.paginas;
        divPagina.innerHTML ='';
        for(let i = 1; i <= paginasBotones; i++){
            divPagina.innerHTML += botonesPaginacionFiltrada(i);
        }
    });

});

function numeroPaginaCat(num){

    peticion.accion = 'paginacionCat';
    peticion.paginaDeseada = num;

    postData('./php/cargarPublicacionesBusqueda.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        divPublicaciones.innerHTML = '';
        const publicaciones = data.publicaciones;
        publicaciones.forEach(publicacion => {
            let div = document.createElement('div');
            div.innerHTML = publicacionesHTMLVer(publicacion.imagenes.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia);
            divPublicaciones.append(div);
        });

    });
}

function numeroPaginaFil(num){

    peticion.accion = 'paginacionFil';
    peticion.paginaDeseada = num;

    postData('./php/cargarPublicacionesBusqueda.php', {
        data:peticion
    }).then((data) => {
        //console.log(data);
        divPublicaciones.innerHTML = '';
        const publicaciones = data.publicaciones;
        publicaciones.forEach(publicacion => {
            let div = document.createElement('div');
            div.innerHTML = publicacionesHTMLVer(publicacion.imagenes.imagen, publicacion.titulo, publicacion.descripcion, publicacion.id, publicacion.provincia);
            divPublicaciones.append(div);
        });

    });
}

function botonesPaginacionCategoria(paginaC){
    return `
    <button class="btn-paginacion" onclick="numeroPaginaCat(${paginaC})">${paginaC}</button>
    `
}

function botonesPaginacionFiltrada(paginaC){
    return `
    <button class="btn-paginacion" onclick="numeroPaginaFil(${paginaC})">${paginaC}</button>
    `
}