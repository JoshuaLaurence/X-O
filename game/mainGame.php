<html lang="en">

<?php session_start() ?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="game.css" rel="stylesheet">
    <title>X&O | <?php
                    if ($_SESSION["mode"] === "ai") {
                        echo "AI";
                    } else {
                        echo "2 Player";
                    }
                    ?></title>
</head>

<body>
    <?php

    $board = [
        [" ", " ", " "],
        [" ", " ", " "],
        [" ", " ", " "],
    ];

    $currentPlayer = "X";

    $mode = "twoPlayer";

    $winString = "";

    $canPlay = TRUE;

    if ($_SESSION["board"] && $_SESSION["currentPlayer"]) {
        $board = $_SESSION["board"];
        $currentPlayer = $_SESSION["currentPlayer"];
        $canPlay = $_SESSION["canPlay"];
        $mode = $_SESSION["mode"];
        $winString = $_SESSION["winString"];
    }

    ?>
    <div class="topRow">
        <form method="post" class="topRowForm">
            <input class="topRowButton" type="submit" name="back" value="Back">
            <label class="invisiblePointer"><?php

                                            if ($winString) {
                                                echo "->";
                                            }

                                            ?></label>
        </form>
        <?php echo "<label class='winString'>" . $winString . "</label>"; ?>
        <form method="post" class="topRowForm">

            <label class="pointer"><?php

                                    if ($winString) {
                                        echo "->";
                                    }

                                    ?></label>
            <input class="topRowButton" type="submit" name="reset" value="Reset Game">

            <?php

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["reset"])) {
                $_SESSION["board"] = [
                    [" ", " ", " "],
                    [" ", " ", " "],
                    [" ", " ", " "],
                ];
                $_SESSION["canPlay"] = TRUE;
                $_SESSION["currentPlayer"] = "X";
                $_SESSION["winString"] = "";
                $board = $_SESSION["board"];
                $currentPlayer = $_SESSION["currentPlayer"];
                $canPlay = $_SESSION["canPlay"];
                $winString = $_SESSION["winString"];
                // session_destroy();
                unset($_POST["reset"]);
                header("Refresh:0");
            }

            ?>
        </form>
    </div>
    <?php
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["back"])) {
        unset($_POST["back"]);
        header("Location: /xo");
    }
    ?>

    <div class="outer">
        <div>
            <?php

            function createProjectionMap()
            {
                global $board;
                $projectionMap = [];

                //Rows
                for ($j = 0; $j < count($board); $j++) {
                    $tempRow = [];
                    for ($i = 0; $i < count($board[0]); $i++) {
                        array_push($tempRow, $board[$j][$i]);
                    }
                    array_push($projectionMap, $tempRow);
                }

                //Columns
                for ($j = 0; $j < count($board); $j++) {
                    $tempRow = [];
                    for ($i = 0; $i < count($board[0]); $i++) {
                        array_push($tempRow, $board[$i][$j]);
                    }
                    array_push($projectionMap, $tempRow);
                }

                //Diagonal One
                $tempDiag = [];
                for ($k = 0; $k < count($board[0]); $k++) {
                    array_push($tempDiag, $board[$k][$k]);
                }
                array_push($projectionMap, $tempDiag);

                //Diagonal Two
                $tempDiag = [];
                $j = count($board) - 1;
                for ($k = 0; $k < count($board[0]); $k++) {
                    array_push($tempDiag, $board[$k][$j]);
                    $j--;
                }
                array_push($projectionMap, $tempDiag);

                return $projectionMap;
            }

            function winCheck()
            {
                global $board;
                global $canPlay;
                $projectionMap = createProjectionMap();
                $won = FALSE;
                $draw = TRUE;
                foreach ($projectionMap as $singleList) {
                    if (array_unique($singleList) === array("X")) {
                        $_SESSION["winString"] = "Player X has won!!";
                        $won = TRUE;
                        $canPlay = FALSE;
                        $_SESSION["canPlay"] = $canPlay;
                        break;
                    } elseif (array_unique($singleList) === array("O")) {
                        $_SESSION["winString"] = "Player O has won!!";
                        $won = TRUE;
                        $canPlay = FALSE;
                        $_SESSION["canPlay"] = $canPlay;
                        break;
                    }
                }
                if ($won === FALSE) {
                    foreach ($board as $singleRow) {
                        if (in_array(" ", $singleRow)) {
                            $draw = FALSE;
                            break;
                        }
                    }
                    if ($draw) {
                        $canPlay = FALSE;
                        $_SESSION["canPlay"] = $canPlay;
                        $_SESSION["winString"] = "Draw!!";
                    }
                }

                header("Refresh:0");
            }

            function changeCurrentPlayer(&$currentPlayer)
            {
                if ($currentPlayer === "X") {
                    $currentPlayer = "O";
                } else {
                    $currentPlayer = "X";
                }
                $_SESSION["currentPlayer"] = $currentPlayer;
            }

            function validateHumanMove($co_ords)
            {
                global $currentPlayer;
                global $board;
                global $mode;
                if ($board[intval($co_ords[0])][intval($co_ords[1])] === " ") {
                    $board[intval($co_ords[0])][intval($co_ords[1])] = $currentPlayer;

                    if ($mode === "twoPlayer") changeCurrentPlayer($currentPlayer);
                }
                $_SESSION["board"] = $board;
            }

            if ($_SESSION["fromAI"] === TRUE) {
                winCheck();
                $_SESSION["fromAI"] = FALSE;
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["cell"])) {
                $co_ords = explode("|", $_POST["position"]);
                if ($canPlay) {
                    validateHumanMove($co_ords);
                    if ($mode === "ai") {
                        include "ai.php";
                        bestAIMove();
                    }
                }
                winCheck();
                unset($_POST["cell"]);
                unset($_POST["position"]);
            }

            ?>
        </div>
        <div class="board">

            <?php

            // $board = [
            //     [" ", " ", " "],
            //     [" ", " ", " "],
            //     [" ", " ", " "],
            // ];

            for ($i = 0; $i < 3; $i++) {
                for ($j = 0; $j < 3; $j++) {
                    echo "
                    <form method='post'>
                        <input type='hidden' name='position' value='" . $i . "|" . $j . "'>
                        <input type='submit' name='cell' class='boardCell " . $board[$i][$j] . "' value='" . $board[$i][$j] . "'>
                    </form>
                    ";
                }
            }


            ?>

        </div>
    </div>
</body>

</html>
