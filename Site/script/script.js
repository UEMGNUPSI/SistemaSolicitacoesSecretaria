
//sidebar
const sidebar = document.getElementById('sidebar');
const botaoMenu = document.getElementById('botao-menu');
botaoMenu.addEventListener('click', ()=>{
    if (sidebar.style.display == "block"){
        sidebar.style.display = "none"
    }else{
        sidebar.style.display = "block"
    }
})
window.addEventListener('resize', ()=>{
    if (innerWidth > 768){
    sidebar.style.display = "block"
}else{
    sidebar.style.display = "none"
}
})