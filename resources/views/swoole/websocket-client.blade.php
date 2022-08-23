<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Websocket Client</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body class="antialiased">
  <input id="input" style="width: 100%;">
</body>

<!-- Scripts -->
{{-- <script rel="text/javascript" src="{{ asset('js/app.js') }}"></script> --}}
<script type="text/javascript">
  window.onload = function() {
    var nick = prompt("Enter your nickname");
    var input = document.getElementById("input");
    input.focus();

    // 初始化客户端套接字并建立连接
    // var socket = new WebSocket("ws://localhost:8001"); // client:8001 -> server:8000
    var socket = new WebSocket("ws://blog-s.test/ws");

    // 连接建立时触发
    socket.onopen = function(event) {
      console.log("Connection open ...");
    };

    // 接收到服务端推送时执行
    socket.onmessage = function(event) {
      var msg = event.data;
      var node = document.createTextNode(msg);
      var div = document.createElement("div");
      div.appendChild(node);
      document.body.insertBefore(div, input);
      input.scrollIntoView();
    };

    // 连接关闭时触发
    socket.onclose = function(event) {
      console.log("Connection closed ...");
    };

    input.onchange = function() {
      var msg = nick + ": " + input.value;
      // 将输入框变更信息通过 send 方法发送到服务器
      socket.send(msg);
      input.value = "";
    };


  }
</script>

</html>
