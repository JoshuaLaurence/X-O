<!DOCTYPE html>
<html lang="en">

<?php session_start(); ?>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="mains.css" rel="stylesheet">
    <title>X&O</title>
</head>

<body>
    <div class="main">
        <div class="title">
            <div class="letter">X</div>
            <div class="letter">&</div>
            <div class="letter">O</div>
            <div class="letterSpace"> </div>
            <div class="letter">-</div>
            <div class="letterSpace"> </div>
            <div class="letter">C</div>
            <div class="letter">h</div>
            <div class="letter">o</div>
            <div class="letter">o</div>
            <div class="letter">s</div>
            <div class="letter">e</div>
            <div class="letterSpace"> </div>
            <div class="letter">a</div>
            <div class="letterSpace"> </div>
            <div class="letter">m</div>
            <div class="letter">o</div>
            <div class="letter">d</div>
            <div class="letter">e</div>
        </div>
        <form class="mainForm" method="post">
            <input class="mainFormButton" type="submit" name="gamemode" value="AI">
            <input class="mainFormButton" type="submit" name="gamemode" value="Two Player">
        </form>

        <?php

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["gamemode"])) {
            echo $_POST["gamemode"];
            if ($_POST["gamemode"] === "AI") {
                $_SESSION["mode"] = "ai";
            } else {
                $_SESSION["mode"] = "twoPlayer";
            }
            $_SESSION["board"] = [
                [" ", " ", " "],
                [" ", " ", " "],
                [" ", " ", " "],
            ];
            $_SESSION["canPlay"] = TRUE;
            $_SESSION["currentPlayer"] = "X";
            $_SESSION["fromAI"] = FALSE;
            $_SESSION["winString"] = "";
            unset($_POST["gamemode"]);
            echo $_SESSION["mode"];
            header("Location: /xo/game/mainGame.php");
        }

        ?>
    </div>
</body>

</html>
