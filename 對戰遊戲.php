<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>雙人連線對戰遊戲 - 五子棋</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ma+Shan+Zheng&family=Noto+Serif+TC:wght@400;700&family=ZCOOL+XiaoWei&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'ZCOOL XiaoWei', 'Noto Serif TC', '標楷體', 'DFKai-SB', serif;
            background: linear-gradient(135deg, #2c3e50 0%, #4a6741 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .game-container {
            background: linear-gradient(145deg, #f5f0e6, #e8dcc8);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            text-align: center;
            max-width: 700px;
            width: 100%;
            border: 3px solid #8b7355;
        }

        h1 {
            font-family: 'Ma Shan Zheng', 'ZCOOL XiaoWei', cursive;
            color: #4a3728;
            margin-bottom: 10px;
            font-size: 2.5em;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
        }

        .subtitle {
            color: #6b5344;
            margin-bottom: 20px;
            font-size: 1.1em;
            letter-spacing: 3px;
        }

        /* 房間選擇區 */
        .room-section {
            margin-bottom: 20px;
            padding: 20px;
            background: linear-gradient(145deg, #fff8f0, #f0e6d8);
            border-radius: 10px;
            border: 2px solid #c9b896;
        }

        .room-section h3 {
            margin-bottom: 15px;
            color: #5a4a3a;
            font-family: 'Ma Shan Zheng', cursive;
            font-size: 1.5em;
        }

        .room-input {
            display: flex;
            gap: 10px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .room-input input {
            padding: 12px 15px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            width: 200px;
            transition: border-color 0.3s;
        }

        .room-input input:focus {
            outline: none;
            border-color: #667eea;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s;
            font-weight: bold;
            font-family: 'ZCOOL XiaoWei', 'Noto Serif TC', serif;
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5a2b 0%, #a0522d 100%);
            color: #fff8dc;
            border: 2px solid #654321;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(139, 90, 43, 0.4);
            background: linear-gradient(135deg, #a0522d 0%, #8b5a2b 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #556b2f 0%, #6b8e23 100%);
            color: #fff8dc;
            border: 2px solid #3d5c0f;
        }

        .btn-secondary:hover {
            background: linear-gradient(135deg, #6b8e23 0%, #556b2f 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, #8b0000 0%, #b22222 100%);
            color: #fff8dc;
            border: 2px solid #5c0000;
        }

        .btn-danger:hover {
            background: linear-gradient(135deg, #b22222 0%, #8b0000 100%);
        }

        /* 狀態顯示 */
        .status-bar {
            display: flex;
            justify-content: space-around;
            margin: 20px 0;
            padding: 15px;
            background: linear-gradient(145deg, #fff8f0, #f0e6d8);
            border-radius: 10px;
            border: 2px solid #c9b896;
        }

        .player-info {
            text-align: center;
        }

        .player-info .symbol {
            font-size: 2em;
            font-weight: bold;
        }

        .player-info.player-x .symbol {
            color: #1a1a1a;
            text-shadow: 0 0 5px rgba(0,0,0,0.3);
        }

        .player-info.player-o .symbol {
            color: #f5f5f5;
            text-shadow: 0 0 5px rgba(0,0,0,0.5);
        }

        .player-info .label {
            font-size: 0.9em;
            color: #5a4a3a;
        }

        .player-info.active {
            background: linear-gradient(145deg, #d4c4a8, #c9b896);
            border-radius: 8px;
            padding: 10px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.1);
        }

        /* 遊戲訊息 */
        .game-message {
            font-size: 1.3em;
            color: #4a3728;
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(145deg, #fff8dc, #f5deb3);
            border-radius: 10px;
            min-height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 2px solid #daa520;
            font-family: 'Ma Shan Zheng', cursive;
            font-size: 1.5em;
        }

        .game-message.win {
            background: linear-gradient(145deg, #f0fff0, #98fb98);
            color: #228b22;
            border-color: #228b22;
        }

        .game-message.lose {
            background: linear-gradient(145deg, #fff0f0, #ffb6c1);
            color: #8b0000;
            border-color: #8b0000;
        }

        .game-message.draw {
            background: linear-gradient(145deg, #f0f8ff, #b0c4de);
            color: #4169e1;
            border-color: #4169e1;
        }

        /* 遊戲棋盤 */
        .game-board {
            display: grid;
            grid-template-columns: repeat(15, 1fr);
            gap: 0;
            max-width: 600px;
            margin: 20px auto;
            background: #dcb35c;
            padding: 10px;
            border-radius: 10px;
            box-shadow: inset 0 0 10px rgba(0,0,0,0.3);
        }

        .cell {
            width: 36px;
            height: 36px;
            background: transparent;
            border: 1px solid #b8963e;
            cursor: pointer;
            transition: all 0.15s;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }

        .cell:hover:not(.disabled):not(.black):not(.white) {
            background: rgba(0,0,0,0.1);
        }

        .cell::after {
            content: '';
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: none;
            box-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .cell.black::after {
            display: block;
            background: radial-gradient(circle at 30% 30%, #555, #000);
        }

        .cell.white::after {
            display: block;
            background: radial-gradient(circle at 30% 30%, #fff, #ccc);
        }

        .cell.disabled {
            cursor: not-allowed;
        }

        .cell.winning::after {
            animation: pulse 0.5s ease-in-out infinite alternate;
            box-shadow: 0 0 10px 3px #ff0;
        }

        @keyframes pulse {
            from { transform: scale(1); }
            to { transform: scale(1.15); }
        }

        /* 煙火特效容器 */
        .fireworks-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
            overflow: hidden;
        }

        .firework {
            position: absolute;
            width: 6px;
            height: 6px;
            border-radius: 50%;
            animation: firework-explode 1.5s ease-out forwards;
        }

        @keyframes firework-explode {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            100% {
                transform: scale(0);
                opacity: 0;
            }
        }

        .firework-particle {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            animation: particle-fly 1.5s ease-out forwards;
        }

        @keyframes particle-fly {
            0% {
                transform: translate(0, 0) scale(1);
                opacity: 1;
            }
            100% {
                opacity: 0;
            }
        }

        /* 傷心特效 */
        .sad-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1000;
            overflow: hidden;
        }

        .tear {
            position: absolute;
            font-size: 30px;
            animation: tear-fall 3s ease-in forwards;
            opacity: 0.8;
        }

        @keyframes tear-fall {
            0% {
                transform: translateY(-20px) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 0.8;
            }
            100% {
                transform: translateY(100vh) rotate(20deg);
                opacity: 0;
            }
        }

        .sad-cloud {
            position: fixed;
            top: 10%;
            left: 50%;
            transform: translateX(-50%);
            font-size: 80px;
            animation: cloud-appear 0.5s ease-out forwards, cloud-shake 2s ease-in-out infinite;
            z-index: 1001;
            pointer-events: none;
        }

        @keyframes cloud-appear {
            0% {
                transform: translateX(-50%) scale(0);
                opacity: 0;
            }
            100% {
                transform: translateX(-50%) scale(1);
                opacity: 1;
            }
        }

        @keyframes cloud-shake {
            0%, 100% { transform: translateX(-50%) rotate(-2deg); }
            50% { transform: translateX(-50%) rotate(2deg); }
        }

        /* 勝利文字特效 */
        .victory-text {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 4em;
            font-weight: bold;
            color: #ffd700;
            text-shadow: 0 0 20px #ff6b00, 0 0 40px #ff6b00, 0 0 60px #ff6b00;
            animation: victory-pulse 0.5s ease-in-out infinite alternate;
            z-index: 1002;
            pointer-events: none;
            font-family: 'Ma Shan Zheng', cursive;
        }

        @keyframes victory-pulse {
            0% { transform: translate(-50%, -50%) scale(1); }
            100% { transform: translate(-50%, -50%) scale(1.1); }
        }

        /* 失敗文字特效 */
        .defeat-text {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-size: 3em;
            font-weight: bold;
            color: #666;
            text-shadow: 0 0 10px rgba(0,0,0,0.3);
            animation: defeat-appear 0.5s ease-out forwards;
            z-index: 1002;
            pointer-events: none;
            font-family: 'Ma Shan Zheng', cursive;
        }

        @keyframes defeat-appear {
            0% { 
                transform: translate(-50%, -50%) scale(2);
                opacity: 0;
            }
            100% { 
                transform: translate(-50%, -50%) scale(1);
                opacity: 1;
            }
        }

        /* 房間資訊 */
        .room-info {
            background: linear-gradient(145deg, #fff8f0, #f0e6d8);
            padding: 10px 20px;
            border-radius: 8px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 2px solid #c9b896;
        }

        .room-info .room-id {
            font-weight: bold;
            color: #8b4513;
            font-family: 'Ma Shan Zheng', cursive;
            font-size: 1.2em;
        }

        /* 隱藏區塊 */
        .hidden {
            display: none !important;
        }

        /* 載入動畫 */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* 比分板 */
        .scoreboard {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(145deg, #fff8f0, #f0e6d8);
            border-radius: 10px;
            border: 2px solid #c9b896;
        }

        .score-item {
            text-align: center;
        }

        .score-item .score-value {
            font-size: 2em;
            font-weight: bold;
            font-family: 'Ma Shan Zheng', cursive;
        }

        .score-item.x .score-value {
            color: #1a1a1a;
        }

        .score-item.o .score-value {
            color: #666;
        }

        .score-item .score-label {
            font-size: 0.9em;
            color: #5a4a3a;
        }

        /* 計時器 */
        .timer-container {
            margin: 15px 0;
            padding: 15px;
            background: linear-gradient(145deg, #3d2b1f, #2d1f15);
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.3);
            border: 3px solid #8b4513;
        }

        .timer-display {
            font-size: 3em;
            font-weight: bold;
            color: #ffd700;
            text-shadow: 0 0 10px #ffd700, 0 0 20px #daa520;
            font-family: 'Ma Shan Zheng', cursive;
        }

        .timer-display.warning {
            color: #ff8c00;
            text-shadow: 0 0 10px #ff8c00, 0 0 20px #ff6600;
            animation: timer-pulse 0.5s ease-in-out infinite;
        }

        .timer-display.danger {
            color: #ff4500;
            text-shadow: 0 0 10px #ff4500, 0 0 20px #dc143c;
            animation: timer-pulse 0.3s ease-in-out infinite;
        }

        @keyframes timer-pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .timer-label {
            font-size: 1em;
            color: #daa520;
            margin-top: 5px;
            font-family: 'ZCOOL XiaoWei', serif;
        }

        .timer-bar {
            width: 100%;
            height: 8px;
            background: #1a0f0a;
            border-radius: 4px;
            margin-top: 10px;
            overflow: hidden;
            border: 1px solid #8b4513;
        }

        .timer-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, #ffd700, #daa520);
            border-radius: 4px;
            transition: width 0.1s linear;
        }

        .timer-bar-fill.warning {
            background: linear-gradient(90deg, #ff8c00, #ff6600);
        }

        .timer-bar-fill.danger {
            background: linear-gradient(90deg, #ff4500, #dc143c);
        }

        /* 按鈕組 */
        .button-group {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        /* 連線狀態指示器 */
        .connection-status {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin: 10px 0;
            font-size: 0.9em;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: #ccc;
        }

        .status-dot.connected {
            background: #28a745;
            animation: blink 2s infinite;
        }

        .status-dot.waiting {
            background: #ffc107;
            animation: blink 1s infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>
</head>
<body>
    <div class="game-container">
        <h1>∴ 五子棋 ∵</h1>
        <p class="subtitle">『 雙人連線對弈 』</p>

        <!-- 房間選擇區 -->
        <div id="roomSection" class="room-section">
            <h3>建立或加入遊戲房間</h3>
            <div class="room-input">
                <input type="text" id="roomId" placeholder="輸入房間代碼" maxlength="10">
                <button class="btn btn-primary" onclick="joinRoom()">加入房間</button>
            </div>
            <p style="margin: 15px 0; color: #666;">— 或 —</p>
            <button class="btn btn-secondary" onclick="createRoom()">建立新房間</button>
        </div>

        <!-- 遊戲區 -->
        <div id="gameSection" class="hidden">
            <!-- 房間資訊 -->
            <div class="room-info">
                <span>房間: <span id="currentRoomId" class="room-id"></span></span>
                <button class="btn btn-danger" onclick="leaveRoom()" style="padding: 8px 15px; font-size: 14px;">離開房間</button>
            </div>

            <!-- 連線狀態 -->
            <div class="connection-status">
                <span class="status-dot" id="statusDot"></span>
                <span id="connectionText">連線中...</span>
            </div>

            <!-- 玩家狀態 -->
            <div class="status-bar">
                <div class="player-info player-x" id="playerX">
                    <div class="symbol">⚫</div>
                    <div class="label">黑棋</div>
                </div>
                <div class="player-info player-o" id="playerO">
                    <div class="symbol">⚪</div>
                    <div class="label">白棋</div>
                </div>
            </div>

            <!-- 遊戲訊息 -->
            <div class="game-message" id="gameMessage">等待對手加入...</div>

            <!-- 比分板 -->
            <div class="scoreboard">
                <div class="score-item x">
                    <div class="score-value" id="scoreX">0</div>
                    <div class="score-label">⚫ 黑棋獲勝</div>
                </div>
                <div class="score-item">
                    <div class="score-value" id="scoreDraw">0</div>
                    <div class="score-label">平手</div>
                </div>
                <div class="score-item o">
                    <div class="score-value" id="scoreO">0</div>
                    <div class="score-label">⚪ 白棋獲勝</div>
                </div>
            </div>

            <!-- 計時器 -->
            <div class="timer-container" id="timerContainer">
                <div class="timer-display" id="timerDisplay">10</div>
                <div class="timer-label">思考時間</div>
                <div class="timer-bar">
                    <div class="timer-bar-fill" id="timerBarFill" style="width: 100%"></div>
                </div>
            </div>

            <!-- 遊戲棋盤 -->
            <div class="game-board" id="gameBoard">
                <!-- 15x15 棋盤由 JavaScript 動態生成 -->
            </div>

            <!-- 按鈕組 -->
            <div class="button-group">
                <button class="btn btn-primary" id="restartBtn" onclick="requestRestart()" style="display: none;">再玩一局</button>
            </div>
        </div>
    </div>

    <script>
        // 遊戲狀態
        const BOARD_SIZE = 15;
        const TURN_TIME_LIMIT = 10; // 10秒思考時間
        let gameState = {
            roomId: null,
            playerId: null,
            playerSymbol: null,
            board: Array(BOARD_SIZE * BOARD_SIZE).fill(''),
            currentTurn: 'X',
            gameStatus: 'waiting', // waiting, playing, finished
            winner: null,
            scores: { X: 0, O: 0, draw: 0 }
        };

        let pollInterval = null;
        let timerInterval = null;
        let timeRemaining = TURN_TIME_LIMIT;
        let lastTurn = null;

        // 初始化棋盤
        function initBoard() {
            const boardEl = document.getElementById('gameBoard');
            boardEl.innerHTML = '';
            for (let i = 0; i < BOARD_SIZE * BOARD_SIZE; i++) {
                const cell = document.createElement('div');
                cell.className = 'cell disabled';
                cell.dataset.index = i;
                cell.onclick = () => makeMove(i);
                boardEl.appendChild(cell);
            }
        }

        // 頁面載入時初始化棋盤
        document.addEventListener('DOMContentLoaded', initBoard);

        // 開始計時器
        function startTimer() {
            stopTimer();
            timeRemaining = TURN_TIME_LIMIT;
            updateTimerDisplay();
            
            timerInterval = setInterval(() => {
                timeRemaining -= 0.1;
                if (timeRemaining <= 0) {
                    timeRemaining = 0;
                    updateTimerDisplay();
                    stopTimer();
                    // 時間到，自動跳過回合
                    if (gameState.currentTurn === gameState.playerSymbol && gameState.gameStatus === 'playing') {
                        handleTimeout();
                    }
                } else {
                    updateTimerDisplay();
                }
            }, 100);
        }

        // 停止計時器
        function stopTimer() {
            if (timerInterval) {
                clearInterval(timerInterval);
                timerInterval = null;
            }
        }

        // 更新計時器顯示
        function updateTimerDisplay() {
            const display = document.getElementById('timerDisplay');
            const barFill = document.getElementById('timerBarFill');
            const seconds = Math.ceil(timeRemaining);
            const percentage = (timeRemaining / TURN_TIME_LIMIT) * 100;
            
            display.textContent = seconds;
            barFill.style.width = percentage + '%';
            
            // 移除所有狀態類
            display.classList.remove('warning', 'danger');
            barFill.classList.remove('warning', 'danger');
            
            // 根據剩餘時間添加對應狀態
            if (timeRemaining <= 3) {
                display.classList.add('danger');
                barFill.classList.add('danger');
            } else if (timeRemaining <= 5) {
                display.classList.add('warning');
                barFill.classList.add('warning');
            }
        }

        // 處理超時
        async function handleTimeout() {
            try {
                const response = await fetch('game_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'timeout',
                        roomId: gameState.roomId,
                        playerId: gameState.playerId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    updateGameUI(data.gameState);
                }
            } catch (error) {
                console.error('Timeout error:', error);
            }
        }

        // 生成隨機房間ID
        function generateRoomId() {
            return Math.random().toString(36).substring(2, 8).toUpperCase();
        }

        // 生成玩家ID
        function generatePlayerId() {
            return 'player_' + Math.random().toString(36).substring(2, 10);
        }

        // 建立新房間
        async function createRoom() {
            const roomId = generateRoomId();
            document.getElementById('roomId').value = roomId;
            await joinRoom();
        }

        // 加入房間
        async function joinRoom() {
            const roomId = document.getElementById('roomId').value.trim().toUpperCase();
            if (!roomId) {
                alert('請輸入房間代碼！');
                return;
            }

            const playerId = generatePlayerId();
            
            try {
                const response = await fetch('game_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'join',
                        roomId: roomId,
                        playerId: playerId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    gameState.roomId = roomId;
                    gameState.playerId = playerId;
                    gameState.playerSymbol = data.playerSymbol;
                    
                    document.getElementById('roomSection').classList.add('hidden');
                    document.getElementById('gameSection').classList.remove('hidden');
                    document.getElementById('currentRoomId').textContent = roomId;
                    
                    updateGameUI(data.gameState);
                    startPolling();
                } else {
                    alert(data.message || '無法加入房間');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('連線錯誤，請檢查伺服器是否運行');
            }
        }

        // 離開房間
        async function leaveRoom() {
            if (!gameState.roomId) return;

            try {
                await fetch('game_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'leave',
                        roomId: gameState.roomId,
                        playerId: gameState.playerId
                    })
                });
            } catch (error) {
                console.error('Error:', error);
            }

            stopPolling();
            stopTimer();
            resetLocalState();
            document.getElementById('roomSection').classList.remove('hidden');
            document.getElementById('gameSection').classList.add('hidden');
        }

        // 重置本地狀態
        function resetLocalState() {
            gameState = {
                roomId: null,
                playerId: null,
                playerSymbol: null,
                board: ['', '', '', '', '', '', '', '', ''],
                currentTurn: 'X',
                gameStatus: 'waiting',
                winner: null,
                scores: { X: 0, O: 0, draw: 0 }
            };
        }

        // 下棋
        async function makeMove(index) {
            if (gameState.gameStatus !== 'playing') return;
            if (gameState.currentTurn !== gameState.playerSymbol) return;
            if (gameState.board[index] !== '') return;

            try {
                const response = await fetch('game_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'move',
                        roomId: gameState.roomId,
                        playerId: gameState.playerId,
                        position: index
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    updateGameUI(data.gameState);
                } else {
                    alert(data.message || '無法下棋');
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // 請求重新開始
        async function requestRestart() {
            // 清除特效
            clearEffects();
            
            try {
                const response = await fetch('game_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action: 'restart',
                        roomId: gameState.roomId,
                        playerId: gameState.playerId
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    updateGameUI(data.gameState);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // 輪詢遊戲狀態
        function startPolling() {
            pollInterval = setInterval(async () => {
                try {
                    const response = await fetch('game_api.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            action: 'status',
                            roomId: gameState.roomId,
                            playerId: gameState.playerId
                        })
                    });

                    const data = await response.json();
                    
                    if (data.success) {
                        updateGameUI(data.gameState);
                    } else if (data.message === 'room_not_found') {
                        alert('房間已關閉');
                        leaveRoom();
                    }
                } catch (error) {
                    console.error('Polling error:', error);
                }
            }, 1000); // 每秒輪詢一次
        }

        function stopPolling() {
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        }

        // 更新遊戲UI
        function updateGameUI(state) {
            if (!state) return;

            gameState.board = state.board;
            gameState.currentTurn = state.currentTurn;
            gameState.gameStatus = state.status;
            gameState.winner = state.winner;
            gameState.scores = state.scores || gameState.scores;

            // 更新棋盤
            const cells = document.querySelectorAll('.cell');
            cells.forEach((cell, index) => {
                cell.className = 'cell';
                
                if (gameState.board[index] === 'X') {
                    cell.classList.add('black');
                } else if (gameState.board[index] === 'O') {
                    cell.classList.add('white');
                }

                // 設定是否可點擊
                if (gameState.gameStatus !== 'playing' || 
                    gameState.currentTurn !== gameState.playerSymbol || 
                    gameState.board[index] !== '') {
                    cell.classList.add('disabled');
                }
            });

            // 標記獲勝格子
            if (state.winningLine && state.winningLine.length > 0) {
                state.winningLine.forEach(index => {
                    cells[index].classList.add('winning');
                });
            }

            // 更新連線狀態
            const statusDot = document.getElementById('statusDot');
            const connectionText = document.getElementById('connectionText');
            
            if (state.playerCount >= 2) {
                statusDot.className = 'status-dot connected';
                connectionText.textContent = '已連線 (2/2 玩家)';
            } else {
                statusDot.className = 'status-dot waiting';
                connectionText.textContent = `等待玩家 (${state.playerCount}/2)`;
            }

            // 更新遊戲訊息
            const messageEl = document.getElementById('gameMessage');
            messageEl.className = 'game-message';

            if (gameState.gameStatus === 'waiting') {
                messageEl.textContent = '等待對手加入...';
            } else if (gameState.gameStatus === 'finished') {
                if (gameState.winner === 'draw') {
                    messageEl.textContent = '🤝 平手！';
                    messageEl.classList.add('draw');
                } else if (gameState.winner === gameState.playerSymbol) {
                    messageEl.textContent = '🎉 恭喜你獲勝！';
                    messageEl.classList.add('win');
                    // 顯示煙火特效
                    if (!document.getElementById('fireworksContainer')) {
                        createFireworks();
                    }
                } else {
                    // 檢查是否因為超時輸掉
                    const loseText = state.timeoutLoss && gameState.winner !== gameState.playerSymbol 
                        ? '⏰ 對手超時，你獲勝！' 
                        : (state.timeoutLoss ? '⏰ 思考超時，你輸了！' : '😢 對手獲勝！');
                    
                    if (state.timeoutLoss && gameState.winner === gameState.playerSymbol) {
                        messageEl.textContent = '⏰ 對手超時，你獲勝！';
                        messageEl.classList.add('win');
                        if (!document.getElementById('fireworksContainer')) {
                            createFireworks();
                        }
                    } else {
                        messageEl.textContent = state.timeoutLoss ? '⏰ 思考超時，你輸了！' : '😢 對手獲勝！';
                        messageEl.classList.add('lose');
                        // 顯示傷心特效
                        if (!document.getElementById('sadContainer')) {
                            createSadEffect();
                        }
                    }
                }
                document.getElementById('restartBtn').style.display = 'inline-block';
            } else {
                document.getElementById('restartBtn').style.display = 'none';
                const symbol = gameState.playerSymbol === 'X' ? '⚫ 黑棋' : '⚪ 白棋';
                if (gameState.currentTurn === gameState.playerSymbol) {
                    messageEl.textContent = `輪到你了！你是 ${symbol}`;
                } else {
                    messageEl.textContent = `等待對手下棋...`;
                }
            }

            // 更新玩家高亮
            const playerX = document.getElementById('playerX');
            const playerO = document.getElementById('playerO');
            playerX.classList.remove('active');
            playerO.classList.remove('active');
            
            if (gameState.gameStatus === 'playing') {
                if (gameState.currentTurn === 'X') {
                    playerX.classList.add('active');
                } else {
                    playerO.classList.add('active');
                }
                
                // 計時器邏輯：當回合改變時重置計時器
                if (lastTurn !== gameState.currentTurn) {
                    lastTurn = gameState.currentTurn;
                    startTimer();
                }
            } else {
                // 遊戲未開始或已結束，停止計時器
                stopTimer();
                lastTurn = null;
            }

            // 更新比分
            document.getElementById('scoreX').textContent = gameState.scores.X || 0;
            document.getElementById('scoreO').textContent = gameState.scores.O || 0;
            document.getElementById('scoreDraw').textContent = gameState.scores.draw || 0;
        }

        // 頁面卸載時離開房間
        window.addEventListener('beforeunload', () => {
            if (gameState.roomId) {
                navigator.sendBeacon('game_api.php', JSON.stringify({
                    action: 'leave',
                    roomId: gameState.roomId,
                    playerId: gameState.playerId
                }));
            }
        });

        // 煙火特效
        function createFireworks() {
            const container = document.createElement('div');
            container.className = 'fireworks-container';
            container.id = 'fireworksContainer';
            document.body.appendChild(container);

            // 勝利文字
            const victoryText = document.createElement('div');
            victoryText.className = 'victory-text';
            victoryText.textContent = '� 大獲全勝 🎊';
            document.body.appendChild(victoryText);

            const colors = ['#ff0000', '#ff7700', '#ffff00', '#00ff00', '#00ffff', '#0077ff', '#ff00ff', '#ff69b4'];
            
            function launchFirework() {
                const x = Math.random() * window.innerWidth;
                const y = Math.random() * window.innerHeight * 0.6;
                const color = colors[Math.floor(Math.random() * colors.length)];
                
                // 創建多個粒子
                for (let i = 0; i < 30; i++) {
                    const particle = document.createElement('div');
                    particle.className = 'firework-particle';
                    particle.style.left = x + 'px';
                    particle.style.top = y + 'px';
                    particle.style.backgroundColor = color;
                    particle.style.boxShadow = `0 0 10px ${color}, 0 0 20px ${color}`;
                    
                    const angle = (Math.PI * 2 * i) / 30;
                    const velocity = 50 + Math.random() * 100;
                    const tx = Math.cos(angle) * velocity;
                    const ty = Math.sin(angle) * velocity;
                    
                    particle.style.animation = `particle-fly 1.5s ease-out forwards`;
                    particle.style.setProperty('--tx', tx + 'px');
                    particle.style.setProperty('--ty', ty + 'px');
                    
                    // 使用 CSS transform 來移動粒子
                    particle.animate([
                        { transform: 'translate(0, 0) scale(1)', opacity: 1 },
                        { transform: `translate(${tx}px, ${ty}px) scale(0)`, opacity: 0 }
                    ], {
                        duration: 1500,
                        easing: 'ease-out',
                        fill: 'forwards'
                    });
                    
                    container.appendChild(particle);
                    
                    // 清理粒子
                    setTimeout(() => particle.remove(), 1500);
                }
            }

            // 連續發射煙火
            let count = 0;
            const interval = setInterval(() => {
                launchFirework();
                launchFirework();
                count++;
                if (count > 15) {
                    clearInterval(interval);
                    // 5秒後清理所有特效
                    setTimeout(() => {
                        container.remove();
                        victoryText.remove();
                    }, 2000);
                }
            }, 300);
        }

        // 傷心特效
        function createSadEffect() {
            const container = document.createElement('div');
            container.className = 'sad-container';
            container.id = 'sadContainer';
            document.body.appendChild(container);

            // 傷心雲朵
            const cloud = document.createElement('div');
            cloud.className = 'sad-cloud';
            cloud.textContent = '😢';
            document.body.appendChild(cloud);

            // 失敗文字
            const defeatText = document.createElement('div');
            defeatText.className = 'defeat-text';
            defeatText.textContent = '💔 勝敗乃兵家常事...';
            document.body.appendChild(defeatText);

            const sadEmojis = ['😢', '😭', '💧', '🌧️', '😿'];
            
            function createTear() {
                const tear = document.createElement('div');
                tear.className = 'tear';
                tear.textContent = sadEmojis[Math.floor(Math.random() * sadEmojis.length)];
                tear.style.left = Math.random() * window.innerWidth + 'px';
                tear.style.animationDuration = (2 + Math.random() * 2) + 's';
                tear.style.fontSize = (20 + Math.random() * 20) + 'px';
                container.appendChild(tear);
                
                setTimeout(() => tear.remove(), 4000);
            }

            // 連續掉淚
            let count = 0;
            const interval = setInterval(() => {
                for (let i = 0; i < 3; i++) {
                    createTear();
                }
                count++;
                if (count > 20) {
                    clearInterval(interval);
                    // 清理特效
                    setTimeout(() => {
                        container.remove();
                        cloud.remove();
                        defeatText.remove();
                    }, 2000);
                }
            }, 200);
        }

        // 清除所有特效
        function clearEffects() {
            const fireworks = document.getElementById('fireworksContainer');
            const sad = document.getElementById('sadContainer');
            const victoryText = document.querySelector('.victory-text');
            const defeatText = document.querySelector('.defeat-text');
            const cloud = document.querySelector('.sad-cloud');
            
            if (fireworks) fireworks.remove();
            if (sad) sad.remove();
            if (victoryText) victoryText.remove();
            if (defeatText) defeatText.remove();
            if (cloud) cloud.remove();
        }
    </script>
</body>
</html>
