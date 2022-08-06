<?php

use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
|
| Here you may register all of the event broadcasting channels that your
| application supports. The given channel authorization callbacks are
| used to check if an authenticated user can listen to the channel.
|
*/

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

// 私有频道和存在频道 (共用同一个授权路由：'wechat.group.{id}') 的授权路由，
// 因为存在频道是基于私有频道的，频道名称一样 (都是：('wechat.group.{id}' )，
// 加入存在频道的授权校验逻辑不需要调整，与私有频道共用同一个授权路由
Broadcast::channel('wechat.group.{id}', function ($user, $id) {
    // 模拟微信群与用户映射关系列表，正式项目可以读取数据库获取
    $groupUsers = [
        [
            'group_id' => 1,
            'user_id' => 1,
        ],
        [
            'group_id' => 1,
            'user_id' => 2,
        ],
    ];

    // 判断微信群 ID 是否有效以及用户是否在给定群里，并以此作为授权通过条件
    $result = collect($groupUsers)->groupBy('group_id')->first(function ($group, $groupId) use ($user, $id) {
        return $id == $groupId && $group->contains('user_id', $user->id);
    });

    return $result == null ? false : true;
});
