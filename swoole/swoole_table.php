<?php
// 初始化一个容量为 1024 的 Swoole Table
$table = new \Swoole\Table(1024);
// 在 Table 中新增 id 列
$table->column('id', \Swoole\Table::TYPE_INT);
// 在 Table 中新增 name 列, 长度为 50
$table->column('name', \Swoole\Table::TYPE_STRING, 50);
// 在 Table 中新增 score 列
$table->column('score', \Swoole\Table::TYPE_FLOAT);
// 创建这个  Swoole Table
$table->create();

// 设置 Key-Value 值
$table->set('student-1', ['id' => 1, 'name' => '沛一', 'score' => 90]);
$table->set('student-2', ['id' => 2, 'name' => '迎一', 'score' => 88]);
$table->set('student-3', ['id' => 3, 'name' => '沛二', 'score' => 85]);

// 打印 Swoole Table
var_dump($table);

// 如果指定 Key 值存在，则打印对应 Value 值
if ($table->exist('student-1')) {
    echo "Student-" . $table->get('student-1', 'id') . ':' . $table->get('student-1', 'name') . ':' . $table->get('student-1', 'score') . "\n";
}

// 自增操作
$table->incr('student-2', 'score', 5);
// 自减操作
$table->decr('student-2', 'score', 5);

// 表中总记录数
$count = $table->count();
echo 'count=' . $count . "\n";

// 删除指定表记录
$table->del('student-3');

// 获取表格的最大行数
echo $table->size . "\n";

// 获取实际占用内存的尺寸，单位为字节
echo $table->memorySize . "\n";

foreach ($table as $key => $row) {
    echo 'key:' . $key . "\n";
    var_dump($row);
}
