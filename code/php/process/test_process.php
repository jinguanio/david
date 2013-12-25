<?php
require_once EMBASE_PATH_EYOU_TOOLMAIL_CONF . 'conf_global.php';
require_once PATH_EYOUM_LIB . 'implements/em_implements_helper.class.php';
require_once PATH_EYOUM_LIB. 'em_exception.class.php';
require_once PATH_EYOUM_LIB. 'em_log.class.php';
require_once PATH_EYOUM_LIB. 'daemon/em_daemon_log.class.php';

$proc_class_name = em_implements_helper::import('process', 'agent_client');
class client extends emimp_process_agent_client
{
    protected $__proc_config = array (
        'enable' => '1',
        'debug' => '1',
        'listen' => '0.0.0.0:8548',
        'src_allow' => '127.0.0.1,172.16.100.114',
        'dest_host' => '127.0.0.1:8538',
        'max_dest_buffer_len' => '10000',
        'loop_timeout' => '3600',
        'write_timeout' => '10',
        'noop_interval' => '50',
        'report_interval' => '60',
        'retry_connect_dest_interval' => '30',
        'zip_len' => '100',
        'read_buffer' => '10485760',
        'encrypt' => '1',
        'private_key' => '1234567812345678',
        'type' => 2,
        'max_process' => 1,
    );

    public function init()
    {
        $this->_init();
    }

    public function run() 
    {
        while (true) {
            try {
                $this->_run();
            } catch (em_exception $e) {
                var_dump($e);
            }
        }
    }
}

$options = array(
	'src' => PATH_EYOUM_LOG . 'test_auth.log',
	'own' => EYOUM_EXEC_UID,
);
$writer = em_log::writer_factory('file', $options);
$log = new em_daemon_log($writer);
$log->set_debug(7);

$client = new client();
$client->set_log($log);
$client->init();
$client->run();

