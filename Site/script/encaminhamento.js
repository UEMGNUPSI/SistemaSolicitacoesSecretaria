document.addEventListener('DOMContentLoaded', function() {
    // Dados de exemplo (deveria vir do backend)
    const solicitacoes = [
        { id: 1, curso: 'curso1', descricao: 'Solicitação 1' },
        { id: 2, curso: 'curso2', descricao: 'Solicitação 2' },
        // ... mais solicitações
    ];
    const coordenadores = [
        { id: 1, nome: 'Coordenador 1', curso: 'curso1' },
        { id: 2, nome: 'Coordenador 2', curso: 'curso2' },
        // ... mais coordenadores
    ];

    // Funções para atualizar as listas
    function updateSolicitacoesList(filteredData) {
        const solicitacoesList = document.getElementById('solicitacoesList');
        solicitacoesList.innerHTML = ''; // Limpa a lista

        filteredData.forEach(solicitacao => {
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');
            listItem.dataset.solicitacaoId = solicitacao.id;
            listItem.textContent = `${solicitacao.curso} - ${solicitacao.descricao}`;
            listItem.addEventListener('click', toggleSolicitacaoSelection);
            solicitacoesList.appendChild(listItem);
        });
    }

    function updateCoordenadoresList() {
        const coordenadoresList = document.getElementById('coordenadoresList');
        coordenadoresList.innerHTML = ''; // Limpa a lista

        coordenadores.forEach(coordenador => {
            const listItem = document.createElement('li');
            listItem.classList.add('list-group-item');
            listItem.dataset.coordenadorId = coordenador.id;
            listItem.textContent = `${coordenador.nome} (${coordenador.curso})`;
            listItem.addEventListener('click', toggleCoordenadorSelection);
            coordenadoresList.appendChild(listItem);
        });
    }

    // Funções para gerenciar seleção de itens
    function toggleSolicitacaoSelection(event) {
        const listItem = event.target;
        listItem.classList.toggle('selected');

        // Atualizar o checkbox "Selecionar Todos"
        const selectAllCheckbox = document.getElementById('selectAll');
        selectAllCheckbox.checked = !document.querySelectorAll('#solicitacoesList li:not(.selected)').length;
    }

    function toggleCoordenadorSelection(event) {
        const listItem = event.target;
        const coordenadoresList = document.getElementById('coordenadoresList');
        const selectedItem = coordenadoresList.querySelector('.selected');

        if (selectedItem) {
            selectedItem.classList.remove('selected');
        }

        listItem.classList.add('selected');
    }

    // Inicializar as listas
    updateSolicitacoesList(solicitacoes);
    updateCoordenadoresList();

    // Filtrar solicitações
    const cursoFilter = document.getElementById('cursoFilter');
    cursoFilter.addEventListener('change', () => {
        const selectedCurso = cursoFilter.value;
        const filteredSolicitacoes = selectedCurso === '' ? solicitacoes : solicitacoes.filter(s => s.curso === selectedCurso);
        updateSolicitacoesList(filteredSolicitacoes);
    });

    // Selecionar/desmarcar todos
    const selectAllCheckbox = document.getElementById('selectAll');
    selectAllCheckbox.addEventListener('change', () => {
        const solicitacoesList = document.getElementById('solicitacoesList');
        const listItems = solicitacoesList.querySelectorAll('li');

        listItems.forEach(listItem => {
            listItem.classList.toggle('selected', selectAllCheckbox.checked);
        });
    });

    // Botão "Enviar Solicitações"
    const enviarSolicitacoesButton = document.getElementById('enviarSolicitacoes');
    enviarSolicitacoesButton.addEventListener('click', () => {
        // Obter dados das solicitações e do coordenador selecionado
        // (Aqui você precisa implementar a lógica para enviar os dados para o backend)
        const selectedSolicitacoes = document.querySelectorAll('#solicitacoesList li.selected');
        const selectedCoordenador = document.querySelector('#coordenadoresList li.selected');

        // ... lógica para enviar solicitações para o backend ...

        console.log('Enviando solicitações para:', selectedCoordenador ? selectedCoordenador.dataset.coordenadorId : 'Nenhum coordenador selecionado');
        console.log('Solicitações selecionadas:', selectedSolicitacoes.length);
    });
});