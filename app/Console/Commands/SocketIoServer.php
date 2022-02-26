<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\StudentTeacherMessage;
use App\Models\Teacher;
use Illuminate\Console\Command;
use PHPSocketIO\SocketIO;
use Workerman\Lib\Timer;
use Workerman\Worker;

class SocketIoServer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'socketio {action : start|stop|restart}
                            {--d|d=0: 守护进程}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'socket-io server';

    protected $io;
    protected $inner_http_worker;

    protected $onlineUsers = []; // 保存在线 users

    protected $studentsCache = []; // 学生缓存
    protected $teachersCache = []; // 教师缓存

    protected $ioPort; // socket-io port
    protected $innerHttpHost; // inner-http host

    const HEART_COUNT = 3; // 心跳停止的次数
    const HEART_HIT = 5; // 心跳间隔

    const TYPE_HEART = 'heart'; // 心跳
    const TYPE_LOGIN = 'login'; // 登录
    const TYPE_CHAT = 'chat'; // 交流
    const TYPE_ONLINE = 'online'; // 在线情况

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        global $argv;
        $action = trim($this->argument('action'));
        /**
         * argv[0] 默认当前文件
         */
        $argv[1] = $action;
        $argv[2] = !empty($this->option('d')) ? '-d' : '';

        $this->setConfig();
        $this->start();
    }

    private function start()
    {
        $this->startSocketIo();
        Worker::$stdoutFile = './socket-io.log';
        Worker::runAll();
    }

    private function setConfig()
    {
        $this->ioPort = config('socket-io.io_port', 12306);
        $this->innerHttpHost = config('socket-io.io_inner_http', 'http://0.0.0.0:12307');
    }

    private function startSocketIo()
    {
        $this->io = new SocketIO($this->ioPort);
        foreach (['connection', 'workerStart'] as $event) {
            if (method_exists($this, $event)) {
                $this->io->on($event, [$this, $event]);
            }
        }
    }

    /**
     * 当连接建立时触发的回调函数
     * @param $connection
     */
    public function connection($socket)
    {
        // $this->log('connect-event connections count is ' . count($this->io->worker->connections));

        // 心跳检测，3次没有收到pong,就关闭客户端连接
        $socket->heart_count = self::HEART_COUNT;

        // 登录
        $socket->on(self::TYPE_LOGIN, function ($data) use ($socket) {
            /**
             * @var $data array 登录人
             * @example ['type' => 'student|teacher', 'id' => 1]
             */
            if (!isset($socket->_user)) {
                if (is_array($data)) {
                    $key = $data['type'] . $data['id'];
                    $this->onlineUsers[$key] = $data; // 添加在线用户
                    $socket->_user = $data; // 记录用户
                    $socket->join($key); // 将这个连接加入到独立的分组，方便针对个人推送数据
                    if ($data['type'] === 'student') {
                        // 进入上线状态
                        Student::where('id', $data['id'])->update(['online' => Student::ONLINE]);
                    } else if ($data['type'] === 'teacher') {
                        Teacher::where('id', $data['id'])->update(['online' => Teacher::ONLINE]);
                    }
                }
            }
        });

        // 断开（一般是关闭网页或者跳转刷新导致）
        $socket->on('disconnect', function () use ($socket) {
            if ($socket->heart_timer) {
                // 关闭心跳定时器
                Timer::del($socket->heart_timer);
            }
            if (isset($socket->_user)) {
                $data = $socket->_user;
                $key = $data['type'] . $data['id'];
                unset($this->onlineUsers[$key]); // 去除用户
                $socket->leave($key); // 离开分组
                // $this->log('disconnect-event connections count is ' . count($this->io->worker->connections));
                if ($data['type'] === 'student') {
                    // 进入离线状态
                    Student::where('id', $data['id'])->update(['online' => Student::OFFLINE]);
                } else if ($data['type'] === 'teacher') {
                    Teacher::where('id', $data['id'])->update(['online' => Teacher::OFFLINE]);
                }
            }
        });

        // 心跳定时器
        $socket->heart_timer = Timer::add(self::HEART_HIT, function () use ($socket) {
            if ($socket->heart_count <= 0) {
                $socket->disconnect(); // 关闭连接
                return;
            }
            $socket->heart_count--;
            $socket->emit(self::TYPE_HEART, [
                'ping' => millisecond(), // 发送客户端ping
            ]);
        });

        // 心跳检测
        $socket->on(self::TYPE_HEART, function ($data) use ($socket) {
            if (is_array($data) && isset($data['pong'])) {
                // 如果是 pong 消息（不检测时间是否正确）
                $socket->heart_count = self::HEART_COUNT; //重新计数
            }
        });

        // 聊天
        $socket->on(self::TYPE_CHAT, function ($data) use ($socket) {
            /**
             * @var $data array 接收人信息
             * @example ['id' => 1, 'content' => '', 'type' => 'student|teacher']
             */
            if (is_array($data)) {
                try {
                    $info = [];
                    $originator = $socket->_user; // 发起人 ['type' => 'student|teacher', 'id' => 1]
                    if (!empty($originator)) {
                        if ($originator['type'] === 'teacher') {
                            $info['teacher_id'] = $originator['id'];
                            $info['student_id'] = $data['id'];
                            $info['direction'] = StudentTeacherMessage::DIRECTION_TEACHER;
                        } else if ($originator['type'] === 'student') {
                            $info['teacher_id'] = $data['id'];
                            $info['student_id'] = $originator['id'];
                            $info['direction'] = StudentTeacherMessage::DIRECTION_STUDENT;
                        }
                        $info['content'] = $data['content'];
                        $info['status'] = 1; // 已读, TODO 未读消息处理
                        $result = StudentTeacherMessage::create($info);

                        $message = $result->toArray();
                        if (!isset($this->studentsCache[$message['student_id']])) {
                            $this->studentsCache[$message['student_id']] = Student::where('id', $message['student_id'])->value('name');
                        }
                        $message['student_name'] = $this->studentsCache[$message['student_id']];
                        if (!isset($this->teachersCache[$message['teacher_id']])) {
                            $this->teachersCache[$message['teacher_id']] = Teacher::where('id', $message['teacher_id'])->value('name');
                        }
                        $message['teacher_name'] = $this->teachersCache[$message['teacher_id']];

                        // 定向推送给接收人
                        $key = $data['type'] . $data['id'];
                        // 在线的话，推送过去
                        $this->io->to($key)->emit(self::TYPE_CHAT, [
                            'code' => 200,
                            'data' => $message,
                        ]);
                        $socket->emit(self::TYPE_CHAT, [
                            'code' => 200,
                            'data' => $message,
                        ]);
                    }
                } catch (\Throwable $e) {
                    $this->log($e->getMessage());
                    // 推给自己发送失败消息
                    $socket->emit(self::TYPE_CHAT, [
                        'code' => 101,
                        'message' => '发送失败',
                    ]);
                }
            } else {
                // 推给自己发送失败消息
                $socket->emit(self::TYPE_CHAT, [
                    'code' => 100,
                    'message' => '消息类型不合规',
                ]);
            }
        });
    }

    /**
     * 每个进程启动时触发
     * @param $worker
     */
    public function workerStart($worker)
    {
        $this->onlineUsers();
        // $this->createInnerHttp();
    }

    public function onlineUsers()
    {
        // 3秒 推送一次,当前在线人数情况
        $timeInterval = 3;
        Timer::add($timeInterval, function () {
            $this->io->emit(self::TYPE_ONLINE, array_values($this->onlineUsers));
        });
    }

    /**
     * 第三方 http 请求方式推送数据
     * @Author sorry510 491559675@qq.com
     * @DateTime 2022-02-26
     */
    public function createInnerHttp()
    {
        if (!$this->inner_http_worker) {
            $this->inner_http_worker = $inner_http_worker = new Worker($this->innerHttpHost);
            $inner_http_worker->onMessage = function ($http_connection, $request) {
                $data = $request->post();
                if (empty($data)) {
                    $data = $request->get();
                }
                try {
                    $to = $data['to'] ?? 0;
                    $content = $data['content'];
                    switch ($data['type']) {
                        case 'publish':
                            // 消息推送
                            if ($to) {
                                // 定向发送socket组发送数据
                                $this->io->to($to)->emit('chat', $content);
                            }
                            break;
                    }
                } catch (\Throwable $e) {
                    $this->log($e->getMessage());
                }
            };
            $inner_http_worker->listen();
        }
    }

    protected function log($data = '')
    {
        echo date('Y-m-d H:i:s') . ': ' . $data . PHP_EOL;
    }

}
