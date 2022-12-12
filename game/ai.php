<?php
require_once "mainGame.php";

$ai_board = $board;

function createAIProjectionMap()
{
    global $ai_board;
    $projectionMap = [];

    //Rows
    for ($j = 0; $j < count($ai_board); $j++) {
        $tempRow = [];
        for ($i = 0; $i < count($ai_board[0]); $i++) {
            array_push($tempRow, $ai_board[$j][$i]);
        }
        array_push($projectionMap, $tempRow);
    }

    //Columns
    for ($j = 0; $j < count($ai_board); $j++) {
        $tempRow = [];
        for ($i = 0; $i < count($ai_board[0]); $i++) {
            array_push($tempRow, $ai_board[$i][$j]);
        }
        array_push($projectionMap, $tempRow);
    }

    //Diagonal One
    $tempDiag = [];
    for ($k = 0; $k < count($ai_board[0]); $k++) {
        array_push($tempDiag, $ai_board[$k][$k]);
    }
    array_push($projectionMap, $tempDiag);

    //Diagonal Two
    $tempDiag = [];
    $j = count($ai_board) - 1;
    for ($k = 0; $k < count($ai_board[0]); $k++) {
        array_push($tempDiag, $ai_board[$k][$j]);
        $j--;
    }
    array_push($projectionMap, $tempDiag);

    return $projectionMap;
}

function winCheckAI()
{
    global $ai_board;
    $projectionMap = createAIProjectionMap();
    $won = FALSE;
    $draw = TRUE;
    foreach ($projectionMap as $singleList) {
        if (array_unique($singleList) === array("X")) {
            $won = TRUE;
            return "X";
            break;
        } elseif (array_unique($singleList) === array("O")) {
            $won = TRUE;
            return "O";
            break;
        }
    }
    if ($won === FALSE) {
        foreach ($ai_board as $singleRow) {
            if (in_array(" ", $singleRow)) {
                $draw = FALSE;
                break;
            }
        }
        if ($draw) {
            return "tie";
        } else {
            return null;
        }
    }
}

$scores = [
    "X" => -1,
    "O" => 1,
    "tie" => 0
];

function minimax($depth, $isMaximising)
{
    global $ai_board;
    global $scores;
    $result = winCheckAI();
    if ($result !== null) {
        return $scores[$result];
    }

    if ($isMaximising) {
        $bestScore = -INF;
        for ($i = 0; $i < count($ai_board); $i++) {
            for ($j = 0; $j < count($ai_board[0]); $j++) {
                if ($ai_board[$i][$j] === " ") {
                    $ai_board[$i][$j] = "O";
                    $score = minimax($depth + 1, FALSE);
                    $ai_board[$i][$j] = " ";
                    $bestScore = max($score, $bestScore);
                }
            }
        }
        return $bestScore;
    } else {
        $bestScore = INF;
        for ($i = 0; $i < count($ai_board); $i++) {
            for ($j = 0; $j < count($ai_board[0]); $j++) {
                if ($ai_board[$i][$j] === " ") {
                    $ai_board[$i][$j] = "X";
                    $score = minimax($depth + 1, TRUE);
                    $ai_board[$i][$j] = " ";
                    $bestScore = min($score, $bestScore);
                }
            }
        }
        return $bestScore;
    }
}

function bestAIMove()
{
    global $ai_board;
    $bestScore = -INF;
    $bestMove = [];
    for ($i = 0; $i < count($ai_board); $i++) {
        for ($j = 0; $j < count($ai_board[0]); $j++) {
            if ($ai_board[$i][$j] === " ") {
                $ai_board[$i][$j] = "O";
                $score = minimax(0, FALSE);
                $ai_board[$i][$j] = " ";
                if ($score > $bestScore) {
                    $bestScore = $score;
                    $bestMove = [$i, $j];
                }
            }
        }
    }
    $ai_board[$bestMove[0]][$bestMove[1]] = "O";
    $_SESSION["board"] = $ai_board;
    $_SESSION["fromAI"] = TRUE;
}
