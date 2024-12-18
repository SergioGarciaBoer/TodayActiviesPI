

/* Funcionamiento del modal */

buttonInicioSes.addEventListener('click', ()=>{
    if (window.innerWidth >= 768) {
        modal.style.display = 'flex';
    }else{
        modal.style.display = 'block';
    }
});

modalCierre.addEventListener('click', () =>{
    modal.style.display = 'none';
});









