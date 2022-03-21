<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Services\Deliverer;
use App\Services\ReplyMessageGenerator;
use App\Services\RequestParser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use LINE\LINEBot\MessageBuilder\TextMessageBuilder;

class LineController extends Controller
{
    // メッセージ送信
    public function delivery()
    {
        // TODO: ここに具体的に実装

        // 1. 登録されている友だちにメッセージを送信
        $deliverer = new Deliverer(env('LINE_CHANNEL_ACCESS_TOKEN'), env('LINE_CHANNEL_SECRET'));
        $deliverer->deliverAll('test');

        return response()->json(['message' => 'sent']);
    }

    // メッセージを受け取って返信
    public function callback(Request $request)
    {
        // TODO: ここに具体的に実装

        // 1. 受け取った情報からメッセージの情報を取り出す
        $parser = new RequestParser($request->getContent());
        $recievedMessages = $parser->getRecievedMessages();
        if ($recievedMessages->isEmpty()){
            return response()->json(['message' => 'recieved(no events)']);
        }
        
        $generator = new ReplyMessageGenerator;
        $deliverer = new Deliverer(env('LINE_CHANNEL_ACCESS_TOKEN'), env('LINE_CHANNEL_SECRET'));
        foreach ($recievedMessages as $recievedMessage){
            // 2. 受け取ったメッセージの内容から返信するメッセージを生成
            $replyMessage = $generator->generate($recievedMessage->getText());

            // 3. 返信メッセージを返信先に送信
            $deliverer->reply($recievedMessage->getReplyToken(), $replyMessage);
            
        }

        return response()->json(['message' => 'received']);
    }
}
