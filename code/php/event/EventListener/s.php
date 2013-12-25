<?php
class MyListener {
    public $base,
        $listener,
        $socket;
    private $conn = array();

    public function __construct($port) 
    {
        $this->base = new EventBase();
        if (!$this->base) {
            echo "Couldn't open event base";
            exit(1);
        }

        $this->listener = new EventListener($this->base,
            array($this, "acceptConnCallback"), $this->base,
            EventListener::OPT_CLOSE_ON_FREE | EventListener::OPT_REUSEABLE, -1,
            "0.0.0.0:$port");

        if (!$this->listener) {
            echo "Couldn't create listener";
            exit(1);
        }

        $this->listener->setErrorCallback(array($this, "accept_error_cb"));
    }

    public function run() {
        while (true) {
            $this->base->exit(30);
            $this->base->loop();
        }
    }

    // This callback is invoked when there is data to read on $bev
    public function acceptConnCallback($listener, $fd, $address) {
        $this->bev = new EventBufferEvent($this->base, $fd, EventBufferEvent::OPT_CLOSE_ON_FREE);

        $this->bev->setCallbacks([$this, 'echoReadCallback'], NULL,
            array($this, "echoEventCallback"), NULL);

        if (!$this->bev->enable(Event::READ)) {
            echo "Failed to enable READ\n";
            return;
        }
    }

    public function accept_error_cb($listener, $ctx) {
        $base = $this->base;

        fprintf(STDERR, "Got an error %d (%s) on the listener. "
            ."Shutting down.\n",
            EventUtil::getLastSocketErrno(),
            EventUtil::getLastSocketError());

        $base->exit(NULL);
    }

    public function echoReadCallback($bev, $ctx) 
    {
        $input = $bev->getInput();

        $data = '';
        while(true) {
            $res = $input->readLine(EventBuffer::EOL_CRLF);
            if (is_null($res)) {
                break;
            }

            $data .= $res;
        }

        var_dump($data);

    }

    public function echoEventCallback($bev, $events, $ctx) {
        if ($events & EventBufferEvent::ERROR) {
            echo "Error from bufferevent\n";
        }

        if ($events & (EventBufferEvent::EOF | EventBufferEvent::ERROR)) {
            $bev->free();

            $error_no = EventUtil::getLastSocketErrno();
            $error_msg = EventUtil::getLastSocketError();

            $log = "line: " .__LINE__. ", $error_msg ($error_no)";
            var_dump($log);
        }
    }
}

$port = 9808;

if ($argc > 1) {
    $port = (int) $argv[1];
}
if ($port <= 0 || $port > 65535) {
    exit("Invalid port");
}

$l = new MyListener($port);
$l->run();

