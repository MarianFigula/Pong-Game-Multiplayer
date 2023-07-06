let numberOfPlayersSpan1 = document.getElementById("number-of-players1")
let joinButton = document.getElementById("btn-add-player");
let playerName = document.getElementById("player-name")
let startGameButton = document.getElementById("btn-start-game")
let waitingContainer = document.getElementById("waiting-container")
let hitDiv = document.getElementById("hit-counter-div")
let hitSpan = document.getElementById("hit-counter")
let playersDiv = document.getElementById("players-div")
let playerLeft = document.getElementById("player-left")
let playerRight = document.getElementById("player-right")
let playerTop = document.getElementById("player-top")
let playerBottom = document.getElementById("player-bottom")
let loginContainer = document.getElementById("login-container")
let borderRectWidth = 20;
let ballSize = 15;
var canvas = document.getElementById("my_canvas");
var ctx = canvas.getContext("2d");
let numberOfRectsCol = 5
let numberOfRectsRow = 8

canvas.width = 810;
canvas.height = 600;

let app_width = canvas.width
let app_height = canvas.height

let fullRowBorder = app_width / borderRectWidth
let fullColumnBorder = app_height / borderRectWidth

fourPlayersArena()

function redrawGame(){
    ctx.clearRect(borderRectWidth,borderRectWidth,app_width - 2*borderRectWidth ,app_height - 2*borderRectWidth)
    drawBall(dataJs.ball.x, dataJs.ball.y)

    if (dataJs.paddleL.player_name !== null){
        drawPaddle(dataJs.paddleL.x,dataJs.paddleL.y, dataJs.paddleL.width, dataJs.paddleL.height, "#d90429")
        createColumnBorder(borderRectWidth, 0, 0, numberOfRectsCol)
        createColumnBorder(borderRectWidth, 0, app_height - (numberOfRectsCol * borderRectWidth), numberOfRectsCol)

    }else if(dataJs.paddleL.player_name == null){
        createColumnBorder(borderRectWidth, 0, 0, fullColumnBorder)
    }

    if (dataJs.paddleR.player_name !== null){
        drawPaddle(dataJs.paddleR.x,dataJs.paddleR.y,dataJs.paddleR.width, dataJs.paddleR.height,"#49a078")
        createColumnBorder(borderRectWidth, app_width - borderRectWidth, app_height - (numberOfRectsCol * borderRectWidth), numberOfRectsCol)
        createColumnBorder(borderRectWidth, app_width - borderRectWidth, 0, numberOfRectsCol)
    }else if(dataJs.paddleR.player_name == null){
        createColumnBorder(borderRectWidth, app_width-borderRectWidth, 0, fullColumnBorder)
    }

    if (dataJs.paddleT.player_name !== null){
        drawPaddle(dataJs.paddleT.x,dataJs.paddleT.y,dataJs.paddleT.width, dataJs.paddleT.height,"#00b4d8")
        createRowBorder(borderRectWidth, 0, 0, numberOfRectsRow);
        createRowBorder(borderRectWidth, app_width - (numberOfRectsRow * borderRectWidth), 0, numberOfRectsRow);
    }else if(dataJs.paddleT.player_name == null){
        createRowBorder(borderRectWidth, 0, 0, fullRowBorder);
    }

    if (dataJs.paddleB.player_name !== null){
        drawPaddle(dataJs.paddleB.x,dataJs.paddleB.y,dataJs.paddleB.width, dataJs.paddleB.height,"#ffd100")
        createRowBorder(borderRectWidth, app_width - (numberOfRectsRow * borderRectWidth), app_height - borderRectWidth, numberOfRectsRow);
        createRowBorder(borderRectWidth, 0, app_height - borderRectWidth, numberOfRectsRow);
    }else if (dataJs.paddleB.player_name == null){
        createRowBorder(borderRectWidth, 0, app_height-borderRectWidth, fullRowBorder);
    }
}

function drawPaddle(startX, startY, paddleWidth, paddleHeight, color) {
    ctx.fillStyle = color;
    ctx.fillRect(startX, startY, paddleWidth, paddleHeight);
}

function drawBall(ball_x, ball_y) {
    ctx.beginPath();
    ctx.fillStyle = "#ffffff";
    ctx.arc(ball_x,ball_y, ballSize, 0, 2 * Math.PI);
    ctx.fill();
}


function createRowBorder(borderRectWidth, borderRectX, borderRectY, numberOfRects) {
    for (let i = 0; i < numberOfRects; i++) {
        ctx.strokeStyle = "#444444";
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(borderRectX, borderRectY, borderRectWidth, borderRectWidth);
        ctx.strokeRect(borderRectX, borderRectY, borderRectWidth, borderRectWidth);
        borderRectX += borderRectWidth;
    }
}

function createColumnBorder(borderRectWidth, borderRectX, borderRectY, numberOfRects) {
    for (let i = 0; i < numberOfRects; i++) {
        let borderRect = document.createElement("canvas");
        borderRect.width = borderRectWidth;
        borderRect.height = borderRectWidth;

        ctx.strokeStyle = "#444444";
        ctx.fillStyle = "#ffffff";
        ctx.fillRect(borderRectX, borderRectY, borderRectWidth, borderRectWidth);
        ctx.strokeRect(borderRectX, borderRectY, borderRectWidth, borderRectWidth);
        borderRect.setAttribute("name", "border");
        borderRect.setAttribute("x", borderRectX)
        borderRect.setAttribute("y", borderRectY)

        borderRectY += borderRectWidth;
    }
}

function fourPlayersArena() {


    console.log("SOM 4KA")
    // upper side
    createRowBorder(borderRectWidth, 0, 0, numberOfRectsRow);
    createRowBorder(borderRectWidth, app_width - (numberOfRectsRow * borderRectWidth), 0, numberOfRectsRow);
    // lower side
    createRowBorder(borderRectWidth, app_width - (numberOfRectsRow * borderRectWidth), app_height - borderRectWidth, numberOfRectsRow);
    createRowBorder(borderRectWidth, 0, app_height - borderRectWidth, numberOfRectsRow);
    // left side
    createColumnBorder(borderRectWidth, 0, 0, numberOfRectsCol)
    createColumnBorder(borderRectWidth, 0, app_height - (numberOfRectsCol * borderRectWidth), numberOfRectsCol)
    // right side
    createColumnBorder(borderRectWidth, app_width - borderRectWidth, app_height - (numberOfRectsCol * borderRectWidth), numberOfRectsCol)
    createColumnBorder(borderRectWidth, app_width - borderRectWidth, 0, numberOfRectsCol)

}

// prihlasenie
let ws;
let ball;
var dataJs;


$(document).ready(function () {
    ws = new WebSocket("wss://site87.webte.fei.stuba.sk:9000");
    ws.onopen = function () {
        log("Connection established");
    };
    ws.onerror = function (error) {
        log("Unknown WebSocket Error " + JSON.stringify(error));
    };
    ws.onmessage = function (e) {
        var data = JSON.parse(e.data);

        if (typeof data === "string"){
            data = JSON.parse(data)
        }

        if (data.hasOwnProperty("type") && data.type === "start"){
            //startGameButton.style.display = "block";
            document.getElementById("div-button").style.display = "block";
            startGameButton.style.display = "inline-block";
            startGameButton.style.marginLeft = "auto";
            startGameButton.style.marginRight = "auto";
        }

        if (data.hasOwnProperty("type") && data.type === "buttonJoin"){
            if (dataJs.paddleL.id == null){
                dataJs.paddleL.id = data.data;
            }
            if (dataJs.paddleR.id == null){
                dataJs.paddleR.id = data.data;
            }
            if (dataJs.paddleT.id == null){
                dataJs.paddleT.id = data.data;
            }
            if (dataJs.paddleB.id == null){
                dataJs.paddleB.id = data.data;
            }
        }else {
            dataJs = data
        }



        console.log(dataJs)
        log("< " + data.msg);
        ball = dataJs.ball;
        numberOfPlayersSpan1.innerHTML = dataJs.num_players;
        hitSpan.innerHTML = dataJs.hit_counter

        if (dataJs.game_started === true){
            if (dataJs.paddleL.player_name == null){
                playerLeft.style.display = "none"
            }else {
                playerLeft.style.display = "block"
            }
            if (dataJs.paddleR.player_name == null){
                playerRight.style.display = "none"
            }else {
                playerRight.style.display = "block"
            }
            if (dataJs.paddleT.player_name == null){
                playerTop.style.display = "none"
            }else {
                playerTop.style.display = "block"
            }
            if (dataJs.paddleB.player_name == null){
                playerBottom.style.display = "none"
            }else {
                playerBottom.style.display = "block"
            }

            if (dataJs.num_players === 0){
                //console.log("JE NULA")
                dataJs.hit_counter = 0;

                hitDiv.style.display = "none"
                playerLeft.innerText = ""
                playerRight.innerText = ""
                playerTop.innerText = ""
                playerBottom.innerText = ""
                canvas.style.display = "none";
                loginContainer.style.display = "block";
                playersDiv.style.display = "none";
                dataJs.game_started = false;
                dataJs.paddleL.lives = 3;
                dataJs.paddleR.lives = 3;
                dataJs.paddleT.lives = 3;
                dataJs.paddleB.lives = 3;
                return;
            }

            waitingContainer.style.display = "none";
            canvas.style.display = "block";
            hitDiv.style.display = "block"
            playersDiv.style.display = "block";
            playerLeft.innerText = dataJs.paddleL.player_name + ": " + dataJs.paddleL.lives;
            playerRight.innerText = dataJs.paddleR.player_name + ": " + dataJs.paddleR.lives;
            playerTop.innerText = dataJs.paddleT.player_name + ": " + dataJs.paddleT.lives;
            playerBottom.innerText = dataJs.paddleB.player_name + ": " + dataJs.paddleB.lives;

            redrawGame()
        }
    };
    ws.onclose = function () {
        log("Connection closed - Either the host or the client has lost connection");
        ws.close();
    }

    function log(m) {
        $("#log").append(m + "<br />");
    }

    function send() {
        $Msg = $("#msg");
        if ($Msg.val() == "") return alert("Textarea is empty");

        try {
            //ws.send(num_players.toString())
            ws.send($Msg.val());
            log('> Sent to server:' + $Msg.val());
        } catch (exception) {
            log(exception);
        }
        $Msg.val("");
    }

    $("#send").click(send);
    $("#msg").on("keydown", function (event) {
        if (event.keyCode == 13) send();
    });
    $("#quit").click(function () {
        log("Connection closed");
        ws.close();
        ws = null;
    });
});



// prihlasenie

// todo: ak pocet hracov je 0 tak zvys o jedna a tomuto pridaj buton na spustenie
joinButton.addEventListener("click", () => {
    let login = document.getElementById("login");
    if (playerName.value === ""){
        login.innerText = "Login je prázdny!"
        login.style.display = "block";
        return;
    }
    if (dataJs.num_players >= 4){
        login.innerText = "Lobby je plné"
        login.style.display = "block";
        return;
    }

    login.style.display = "none";

    if (dataJs.num_players >= 0){
        if (dataJs.paddleL.player_name == null){
            dataJs.paddleL.player_name = playerName.value.toUpperCase();
        }
    }
    if (dataJs.num_players >= 1){
        if (dataJs.paddleR.player_name == null){
            dataJs.paddleR.player_name = playerName.value.toUpperCase();
        }
    }
    if (dataJs.num_players >= 2){
        if (dataJs.paddleT.player_name == null){
            dataJs.paddleT.player_name = playerName.value.toUpperCase();
        }
    }
    if (dataJs.num_players >= 3){
        if (dataJs.paddleB.player_name == null){
            dataJs.paddleB.player_name = playerName.value.toUpperCase();
        }
    }
    dataJs.num_players++;

    var dataJoin = {
        "type": "buttonJoin",
        "data": dataJs.num_players
    }
    ws.send(JSON.stringify(dataJoin));

    if (dataJs.num_players === 1){
        startGameButton.style.display = 'inline-block';
    }
    update()
    loginContainer.style.display = 'none';
    waitingContainer.style.display = 'block';
})

startGameButton.addEventListener("click", () => {
    waitingContainer.style.display = "none";
    canvas.style.display = "block";
    dataJs.game_started = true;
    update();

})

function update(){
    ws.send([JSON.stringify(dataJs)]);
}

let paddleSpeed = 10;
document.addEventListener("keydown", function (event) {
    if (dataJs == null)
        return;

    if (dataJs.paddleL.player_name != null ) {
        if (event.key === "w") {
            if(dataJs.paddleL.y > 80) {
                dataJs.paddleL.y -= paddleSpeed;
            }
        } else if (event.key === "s") {
            if(dataJs.paddleL.y + dataJs.paddleL.width < 430) {
                dataJs.paddleL.y += paddleSpeed;
            }
        }
    }
    if (dataJs.paddleR.player_name != null) {
        if (event.key === "i") {
            if(dataJs.paddleR.y > 80) {
                dataJs.paddleR.y -= paddleSpeed;
            }
        } else if (event.key === "k") {
            if(dataJs.paddleR.y + dataJs.paddleR.width< 430) {
                dataJs.paddleR.y += paddleSpeed;
            }
        }
        //update()
    }
    if (dataJs.paddleT.player_name != null){
        if (event.key === "a") {
            if(dataJs.paddleT.x > 90) {
                dataJs.paddleT.x -= paddleSpeed;
            }
        } else if (event.key === "d") {
            if(dataJs.paddleT.x + dataJs.paddleT.width < 700) {
                dataJs.paddleT.x += paddleSpeed;
            }
        }
    }
    if (dataJs.paddleB.player_name != null){
        if (event.key === "j"){
            //dataJs.which_player = "dl"
            if(dataJs.paddleB.x > 90) {
                dataJs.paddleB.x -= paddleSpeed;
            }
        }else if (event.key === "l"){
            if(dataJs.paddleB.x + dataJs.paddleB.width < 700) {
                dataJs.paddleB.x += paddleSpeed;
            }
        }
    }
    update()
});

