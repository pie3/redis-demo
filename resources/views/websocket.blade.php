<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <title>Laravel Websocket</title>

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

  <!-- Styles -->
  <link rel="stylesheet" href="{{ asset('css/app.css') }}">

</head>

<body class="antialiased">
  <h1>Broadcast Test</h1>
  <p>{{ $message ?? '' }}</p>
</body>

<!-- Scripts -->
<script rel="text/javascript" src="{{ asset('js/app.js') }}"></script>
<script type="text/javascript">
  let groupId = 1;

  // 通过 Echo.private 方法接收私有频道广播的消息
  window.Echo.private('wechat.group.' + groupId).listen('UserSendMessage', event => {
    console.log(event.user.name + ' Says: ' + event.message);
  });

  // 通过 Echo.join 方法加入某个私有频道返回的 PresenceChannel 实例，然后在其基础上通过 listen 接收 Websocket 服务端广播消息.
  window.Echo.join('wechat.group.' + groupId).listen('UserEnterGroup', event => {
    // 监听 & 接收服务端广播的消息
    console.log(event.user.name + ' 加入了群聊');
  });

</script>

</html>
