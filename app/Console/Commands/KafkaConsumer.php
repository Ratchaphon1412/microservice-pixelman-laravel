<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Junges\Kafka\Contracts\KafkaConsumerMessage;
use Junges\Kafka\Facades\Kafka;
use App\Events\CouldUploadDataRecived;


class KafkaConsumer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from Kafka topics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        //
        $consumer = Kafka::createConsumer(['upload'])
            ->withHandler(function (KafkaConsumerMessage $message) {
                event(new CouldUploadDataRecived(json_encode($message->getBody())));
                $this->info('Received message: ' . json_encode($message->getBody()));
            })->build();

        $consumer->consume();
    }
}
