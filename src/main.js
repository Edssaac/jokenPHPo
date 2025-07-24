const ws = new WebSocket('ws://localhost:8081');

ws.onmessage = (event) => {
    const data = JSON.parse(event.data);

    if (data.fullRoom) {
        blockRoom();
    } else if (data.playerNumber) {
        setPlayer(data.playerNumber);
    } else if (data.result) {
        setTimeout(() => {
            applyResult(data.result);
        }, 1000);
    }
};

const blockRoom = () => {
    const board = document.getElementById('board');

    board.innerHTML = `
        <div id="alert">
            <p>2/2</p> 
            <p>Espere liberar espaço na sala</p> 
        </div>
    `;
}

const setPlayer = (playerNumber) => {
    const player = document.getElementById('player');

    player.innerHTML = `Jogador #${playerNumber}`;
}

const setStatus = (message) => {
    const status = document.getElementById('status');

    status.innerHTML = message;
}

const setChoices = (p1, p2) => {
    const status = document.getElementById('choices');

    if (p1 && p2) {
        const choices = {
            rock: 'pedra',
            paper: 'papel',
            scissor: 'tesoura'
        }

        status.innerHTML = `${choices[p1]} X ${choices[p2]}`;
    } else {
        status.innerHTML = '';
    }
}

const applyResult = (result) => {
    setStatus('<div id="spinner"></div>');

    setTimeout(() => {
        removeAppliedOption();

        switch (result.winner) {
            case 'P1':
                setStatus('Jogador #1 ganhou !');
                break;
            case 'P2':
                setStatus('Jogador #2 ganhou !');
                break;
            default:
                setStatus('Empate !');
                break;
        }

        setChoices(result.P1, result.P2);
    }, 1000);
}

const play = (choice) => {
    ws.send(JSON.stringify({
        action: 'play',
        choice: choice
    }));
}

const removeAppliedOption = () => {
    const options = document.querySelectorAll('.option');

    options.forEach(option => {
        option.classList.remove('selected');
    });
}

const applyOption = () => {
    const options = document.querySelectorAll('.option');

    options.forEach(option => {
        option.addEventListener('click', function () {
            removeAppliedOption();

            const choice = this.getAttribute('data-choice');

            play(choice);

            this.classList.add('selected');

            setChoices('', '');
            setStatus('Espere a jogada do adversário');
        });
    });
}

applyOption();