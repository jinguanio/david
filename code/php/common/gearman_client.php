<?php
# Create our client object.
$gmclient= new GearmanClient();

# Add default server (localhost).
//$gmclient->addServer('172.16.100.120');
$gmclient->addServer('127.0.0.1', 9000);

echo "Sending job\n";

# Send reverse job
do
{
    //$result = $gmclient->do("del_tmp_file", "/em_acl.class.php");
    $result = $gmclient->do("reverse", "hello!");
    //$result = $gmclient->doBackground("reverse", "hello!");

    # Check for various return packets and errors.
    switch($gmclient->returnCode()) {
    case GEARMAN_WORK_DATA:
        echo "Data: $result\n";
        break;
    case GEARMAN_WORK_STATUS:
        list($numerator, $denominator)= $gmclient->doStatus();
        echo "Status: $numerator/$denominator complete\n";
        break;
    case GEARMAN_WORK_FAIL:
        echo "Failed\n";
        exit;
    case GEARMAN_SUCCESS:
        echo "Result: $result\n"; 
        break;
    default:
        echo "RET: " . $gmclient->returnCode() . "\n";
        exit;
    }
}
while($gmclient->returnCode() != GEARMAN_SUCCESS);

