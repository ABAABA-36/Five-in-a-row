<?php
/**
 * 雙人連線對戰遊戲 API - 五子棋
 * 處理房間管理、遊戲邏輯、狀態同步
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// 處理 OPTIONS 請求
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// 棋盤大小
define('BOARD_SIZE', 15);

// 遊戲資料存儲目錄
$dataDir = __DIR__ . '/game_data';
if (!is_dir($dataDir)) {
    mkdir($dataDir, 0777, true);
}

// 獲取請求資料
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

$action = $data['action'] ?? '';
$roomId = $data['roomId'] ?? '';
$playerId = $data['playerId'] ?? '';

// 獲取房間檔案路徑
function getRoomFile($roomId) {
    global $dataDir;
    return $dataDir . '/room_' . preg_replace('/[^A-Z0-9]/i', '', $roomId) . '.json';
}

// 讀取房間資料
function loadRoom($roomId) {
    $file = getRoomFile($roomId);
    if (file_exists($file)) {
        $content = file_get_contents($file);
        return json_decode($content, true);
    }
    return null;
}

// 儲存房間資料
function saveRoom($roomId, $roomData) {
    $file = getRoomFile($roomId);
    file_put_contents($file, json_encode($roomData, JSON_PRETTY_PRINT));
}

// 刪除房間
function deleteRoom($roomId) {
    $file = getRoomFile($roomId);
    if (file_exists($file)) {
        unlink($file);
    }
}

// 建立新房間
function createRoom($roomId, $playerId) {
    return [
        'id' => $roomId,
        'players' => [
            'X' => $playerId,
            'O' => null
        ],
        'board' => array_fill(0, BOARD_SIZE * BOARD_SIZE, ''),
        'currentTurn' => 'X',
        'status' => 'waiting', // waiting, playing, finished
        'winner' => null,
        'winningLine' => [],
        'scores' => ['X' => 0, 'O' => 0, 'draw' => 0],
        'lastActivity' => time(),
        'created' => time()
    ];
}

// 檢查五子棋勝利
function checkWinner($board) {
    $size = BOARD_SIZE;
    
    // 方向：水平、垂直、斜對角線（右下）、斜對角線（左下）
    $directions = [
        [0, 1],   // 水平
        [1, 0],   // 垂直
        [1, 1],   // 右下斜線
        [1, -1]   // 左下斜線
    ];
    
    for ($row = 0; $row < $size; $row++) {
        for ($col = 0; $col < $size; $col++) {
            $index = $row * $size + $col;
            $player = $board[$index];
            
            if ($player === '') continue;
            
            foreach ($directions as $dir) {
                $line = [$index];
                $count = 1;
                
                // 向一個方向檢查
                for ($i = 1; $i < 5; $i++) {
                    $newRow = $row + $dir[0] * $i;
                    $newCol = $col + $dir[1] * $i;
                    
                    if ($newRow < 0 || $newRow >= $size || $newCol < 0 || $newCol >= $size) {
                        break;
                    }
                    
                    $newIndex = $newRow * $size + $newCol;
                    if ($board[$newIndex] === $player) {
                        $count++;
                        $line[] = $newIndex;
                    } else {
                        break;
                    }
                }
                
                if ($count >= 5) {
                    return ['winner' => $player, 'line' => $line];
                }
            }
        }
    }

    // 檢查平手（棋盤滿了）
    $hasEmpty = in_array('', $board);
    if (!$hasEmpty) {
        return ['winner' => 'draw', 'line' => []];
    }

    return null;
}

// 清理過期房間（超過1小時）
function cleanupOldRooms() {
    global $dataDir;
    $files = glob($dataDir . '/room_*.json');
    $now = time();
    
    foreach ($files as $file) {
        $content = file_get_contents($file);
        $room = json_decode($content, true);
        
        if ($room && ($now - ($room['lastActivity'] ?? 0)) > 3600) {
            unlink($file);
        }
    }
}

// 偶爾清理舊房間
if (rand(1, 100) <= 5) {
    cleanupOldRooms();
}

// 處理各種動作
switch ($action) {
    case 'join':
        $room = loadRoom($roomId);
        
        if (!$room) {
            // 建立新房間
            $room = createRoom($roomId, $playerId);
            $playerSymbol = 'X';
        } else {
            // 加入現有房間
            if ($room['players']['X'] === $playerId) {
                $playerSymbol = 'X';
            } elseif ($room['players']['O'] === $playerId) {
                $playerSymbol = 'O';
            } elseif ($room['players']['O'] === null) {
                $room['players']['O'] = $playerId;
                $playerSymbol = 'O';
                $room['status'] = 'playing';
            } else {
                echo json_encode(['success' => false, 'message' => '房間已滿']);
                exit;
            }
        }
        
        $room['lastActivity'] = time();
        saveRoom($roomId, $room);
        
        $playerCount = ($room['players']['X'] ? 1 : 0) + ($room['players']['O'] ? 1 : 0);
        
        echo json_encode([
            'success' => true,
            'playerSymbol' => $playerSymbol,
            'gameState' => [
                'board' => $room['board'],
                'currentTurn' => $room['currentTurn'],
                'status' => $room['status'],
                'winner' => $room['winner'],
                'winningLine' => $room['winningLine'],
                'scores' => $room['scores'],
                'playerCount' => $playerCount
            ]
        ]);
        break;

    case 'move':
        $position = $data['position'] ?? -1;
        $room = loadRoom($roomId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'room_not_found']);
            exit;
        }
        
        // 驗證玩家
        $playerSymbol = null;
        if ($room['players']['X'] === $playerId) {
            $playerSymbol = 'X';
        } elseif ($room['players']['O'] === $playerId) {
            $playerSymbol = 'O';
        }
        
        if (!$playerSymbol) {
            echo json_encode(['success' => false, 'message' => '無效的玩家']);
            exit;
        }
        
        // 驗證是否輪到該玩家
        if ($room['currentTurn'] !== $playerSymbol) {
            echo json_encode(['success' => false, 'message' => '還沒輪到你']);
            exit;
        }
        
        // 驗證遊戲狀態
        if ($room['status'] !== 'playing') {
            echo json_encode(['success' => false, 'message' => '遊戲未開始或已結束']);
            exit;
        }
        
        // 驗證位置
        if ($position < 0 || $position >= BOARD_SIZE * BOARD_SIZE || $room['board'][$position] !== '') {
            echo json_encode(['success' => false, 'message' => '無效的位置']);
            exit;
        }
        
        // 下棋
        $room['board'][$position] = $playerSymbol;
        $room['currentTurn'] = ($playerSymbol === 'X') ? 'O' : 'X';
        $room['lastActivity'] = time();
        
        // 檢查勝負
        $result = checkWinner($room['board']);
        if ($result) {
            $room['status'] = 'finished';
            $room['winner'] = $result['winner'];
            $room['winningLine'] = $result['line'];
            
            // 更新比分
            if ($result['winner'] === 'draw') {
                $room['scores']['draw']++;
            } else {
                $room['scores'][$result['winner']]++;
            }
        }
        
        saveRoom($roomId, $room);
        
        $playerCount = ($room['players']['X'] ? 1 : 0) + ($room['players']['O'] ? 1 : 0);
        
        echo json_encode([
            'success' => true,
            'gameState' => [
                'board' => $room['board'],
                'currentTurn' => $room['currentTurn'],
                'status' => $room['status'],
                'winner' => $room['winner'],
                'winningLine' => $room['winningLine'],
                'scores' => $room['scores'],
                'playerCount' => $playerCount
            ]
        ]);
        break;

    case 'status':
        $room = loadRoom($roomId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'room_not_found']);
            exit;
        }
        
        $room['lastActivity'] = time();
        saveRoom($roomId, $room);
        
        $playerCount = ($room['players']['X'] ? 1 : 0) + ($room['players']['O'] ? 1 : 0);
        
        echo json_encode([
            'success' => true,
            'gameState' => [
                'board' => $room['board'],
                'currentTurn' => $room['currentTurn'],
                'status' => $room['status'],
                'winner' => $room['winner'],
                'winningLine' => $room['winningLine'],
                'scores' => $room['scores'],
                'playerCount' => $playerCount
            ]
        ]);
        break;

    case 'restart':
        $room = loadRoom($roomId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'room_not_found']);
            exit;
        }
        
        // 重置棋盤但保留比分
        $room['board'] = array_fill(0, BOARD_SIZE * BOARD_SIZE, '');
        $room['currentTurn'] = 'X';
        $room['status'] = ($room['players']['O'] !== null) ? 'playing' : 'waiting';
        $room['winner'] = null;
        $room['winningLine'] = [];
        $room['lastActivity'] = time();
        
        saveRoom($roomId, $room);
        
        $playerCount = ($room['players']['X'] ? 1 : 0) + ($room['players']['O'] ? 1 : 0);
        
        echo json_encode([
            'success' => true,
            'gameState' => [
                'board' => $room['board'],
                'currentTurn' => $room['currentTurn'],
                'status' => $room['status'],
                'winner' => $room['winner'],
                'winningLine' => $room['winningLine'],
                'scores' => $room['scores'],
                'playerCount' => $playerCount
            ]
        ]);
        break;

    case 'leave':
        $room = loadRoom($roomId);
        
        if ($room) {
            // 移除玩家
            if ($room['players']['X'] === $playerId) {
                $room['players']['X'] = null;
            } elseif ($room['players']['O'] === $playerId) {
                $room['players']['O'] = null;
            }
            
            // 如果房間空了，刪除房間
            if ($room['players']['X'] === null && $room['players']['O'] === null) {
                deleteRoom($roomId);
            } else {
                // 重置遊戲狀態
                $room['status'] = 'waiting';
                $room['board'] = array_fill(0, BOARD_SIZE * BOARD_SIZE, '');
                $room['currentTurn'] = 'X';
                $room['winner'] = null;
                $room['winningLine'] = [];
                $room['lastActivity'] = time();
                saveRoom($roomId, $room);
            }
        }
        
        echo json_encode(['success' => true]);
        break;

    case 'timeout':
        // 處理超時：隨機幫玩家下一顆棋
        $room = loadRoom($roomId);
        
        if (!$room) {
            echo json_encode(['success' => false, 'message' => 'room_not_found']);
            exit;
        }
        
        // 驗證玩家
        $playerSymbol = null;
        if ($room['players']['X'] === $playerId) {
            $playerSymbol = 'X';
        } elseif ($room['players']['O'] === $playerId) {
            $playerSymbol = 'O';
        }
        
        if (!$playerSymbol) {
            echo json_encode(['success' => false, 'message' => '無效的玩家']);
            exit;
        }
        
        // 確認是該玩家的回合才能超時
        if ($room['currentTurn'] !== $playerSymbol) {
            echo json_encode(['success' => false, 'message' => '不是你的回合']);
            exit;
        }
        
        // 確認遊戲進行中
        if ($room['status'] !== 'playing') {
            echo json_encode(['success' => false, 'message' => '遊戲未進行中']);
            exit;
        }
        
        // 找出所有空位
        $emptyPositions = [];
        for ($i = 0; $i < BOARD_SIZE * BOARD_SIZE; $i++) {
            if ($room['board'][$i] === '') {
                $emptyPositions[] = $i;
            }
        }
        
        // 隨機選擇一個空位下棋
        if (count($emptyPositions) > 0) {
            $randomIndex = array_rand($emptyPositions);
            $randomPosition = $emptyPositions[$randomIndex];
            $room['board'][$randomPosition] = $playerSymbol;
            $room['currentTurn'] = ($playerSymbol === 'X') ? 'O' : 'X';
            $room['lastActivity'] = time();
            
            // 檢查勝負
            $result = checkWinner($room['board']);
            if ($result) {
                $room['status'] = 'finished';
                $room['winner'] = $result['winner'];
                $room['winningLine'] = $result['line'];
                
                // 更新比分
                if ($result['winner'] === 'draw') {
                    $room['scores']['draw']++;
                } else {
                    $room['scores'][$result['winner']]++;
                }
            }
        }
        
        saveRoom($roomId, $room);
        
        $playerCount = ($room['players']['X'] ? 1 : 0) + ($room['players']['O'] ? 1 : 0);
        
        echo json_encode([
            'success' => true,
            'gameState' => [
                'board' => $room['board'],
                'currentTurn' => $room['currentTurn'],
                'status' => $room['status'],
                'winner' => $room['winner'],
                'winningLine' => $room['winningLine'],
                'scores' => $room['scores'],
                'playerCount' => $playerCount,
                'randomMove' => true
            ]
        ]);
        break;

    default:
        echo json_encode(['success' => false, 'message' => 'Unknown action']);
}
?>
