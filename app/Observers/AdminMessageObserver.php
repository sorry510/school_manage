<?php

namespace App\Observers;

use App\Models\AdminMessage;
use Illuminate\Support\Facades\Log;

class AdminMessageObserver
{
    /**
     * @return void
     */
    public function creating(AdminMessage $model)
    {
        if ($model->type == AdminMessage::TYPE_LINE) {
            $model->status = AdminMessage::STATUS_ON; // 直接改为已发送
            try {
                // TODO 改为异步发送
                $httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient(config('line-bot.channel_access_token'));
                $bot = new \LINE\LINEBot($httpClient, ['channelSecret' => config('line-bot.channel_secret')]);
                $textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder($model->content);
                $response = $bot->pushMessage($model->line_id, $textMessageBuilder);
                // echo $response->getHTTPStatus() . ' ' . $response->getRawBody();
            } catch (\Throwable $e) {
                Log::info('line is network blocked');
            }
        }
    }
}
