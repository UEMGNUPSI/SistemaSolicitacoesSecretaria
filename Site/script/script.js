
//sidebar
const sidebar = document.getElementById('sidebar');
const botaoMenu = document.getElementById('botao-menu');
botaoMenu.addEventListener('click', ()=>{
    if (sidebar.style.display == "none"){
        sidebar.style.display = "block"
    }else{
        sidebar.style.display = "none"
    }
})
window.addEventListener('resize', ()=>{
    if (innerWidth > 768){
    sidebar.style.display = "block"
}else{
    sidebar.style.display = "none"
}
})